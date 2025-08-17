<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
}

if (!isset($filters) || !is_array($filters)) {
    $filters = [
        'invoice_no' => '',
        'customer_reference' => '',
        'company_reference' => '',
        'status' => '',
        'from_date' => '',
        'to_date' => '',
        'completion' => ''
    ];
}
if (!isset($customer_refs) || !is_array($customer_refs)) $customer_refs = [];
if (!isset($company_refs) || !is_array($company_refs)) $company_refs = [];
if (!isset($job_data) || !is_array($job_data)) $job_data = [];
if (!isset($total_invoice_amount)) $total_invoice_amount = 0;
if (!isset($total_paid_amount)) $total_paid_amount = 0;
if (!isset($total_unpaid_amount)) $total_unpaid_amount = 0;
if (!isset($unpaid_invoice_count)) $unpaid_invoice_count = 0;
if (!isset($due_balance)) $due_balance = 0;
if (!isset($total_expenses)) $total_expenses = 0;
if (!isset($total_employee_costs_sum)) $total_employee_costs_sum = 0;
if (!isset($total_capacity)) $total_capacity = 0;
if (!isset($total_net_profit)) $total_net_profit = 0;
if (!isset($profit_margin)) $profit_margin = 0;
if (!isset($overall_invoice_amount)) $overall_invoice_amount = 0;
if (!isset($overall_paid_amount)) $overall_paid_amount = 0;
if (!isset($overall_unpaid_amount)) $overall_unpaid_amount = 0;
if (!isset($overall_unpaid_count)) $overall_unpaid_count = 0;
if (!isset($overall_due_balance)) $overall_due_balance = 0;
if (!isset($error)) $error = null;

// Group jobs by completion status
$job_groups = [
    'Completed' => [],
    'Ongoing' => [],
    'Started' => [],
    'Not Started' => [],
    'Cancelled' => []
];
foreach ($job_data as $row) {
    $status = $row['completion_status'] ?? 'Unknown';
    if (!isset($job_groups[$status])) {
        $job_groups['Unknown'][] = $row;
    } else {
        $job_groups[$status][] = $row;
    }
}

// Apply filters to grouped job data
$filtered_job_groups = [];
foreach ($job_groups as $status => $jobs) {
    $filtered_job_groups[$status] = array_filter($jobs, function($row) use ($filters) {
        if (!empty($filters['invoice_no']) && stripos($row['invoice_no'] ?? '', $filters['invoice_no']) === false) {
            return false;
        }
        if (!empty($filters['customer_reference']) && ($row['customer_reference'] ?? '') !== $filters['customer_reference']) {
            return false;
        }
        if (!empty($filters['company_reference']) && ($row['company_reference'] ?? '') !== $filters['company_reference']) {
            return false;
        }
        if (!empty($filters['status'])) {
            $has_invoice = !is_null($row['invoice_no']);
            $outstanding = $has_invoice ? floatval($row['invoice_value'] ?? 0) - floatval($row['received_amount'] ?? 0) : 0;
            if ($filters['status'] === 'paid' && ($outstanding > 0 || !$has_invoice)) {
                return false;
            }
            if ($filters['status'] === 'unpaid' && $outstanding <= 0 && $has_invoice) {
                return false;
            }
        }
        if (!empty($filters['from_date']) && $row['date_completed'] !== '0000-00-00' && strtotime($row['date_completed'] ?? '') < strtotime($filters['from_date'])) {
            return false;
        }
        if (!empty($filters['to_date']) && $row['date_completed'] !== '0000-00-00' && strtotime($row['date_completed'] ?? '') > strtotime($filters['to_date'])) {
            return false;
        }
        if (!empty($filters['completion']) && ($row['completion'] ?? '') !== $filters['completion']) {
            return false;
        }
        return true;
    });
}

// Calculate totals based on filtered data
$total_expenses = 0;
$total_employee_costs_sum = 0;
$total_capacity = 0;
$total_invoice_amount = 0;
$total_paid_amount = 0;
$total_unpaid_amount = 0;
$unpaid_invoice_count = 0;
$due_balance = 0;

foreach ($filtered_job_groups as $jobs) {
    foreach ($jobs as $row) {
        $has_invoice = !is_null($row['invoice_no']);
        $invoice_value = $has_invoice ? floatval($row['invoice_value'] ?? 0) : 0;
        $received_amount = $has_invoice ? floatval($row['received_amount'] ?? 0) : 0;
        $outstanding = $invoice_value - $received_amount;

        $total_invoice_amount += $invoice_value;
        $total_paid_amount += $received_amount;
        $total_unpaid_amount += $outstanding;
        if ($outstanding > 0 || !$has_invoice) {
            $unpaid_invoice_count++;
            $due_balance += $outstanding;
        }

        if (!empty($row['expense_details'])) {
            foreach ($row['expense_details'] as $amount) {
                $total_expenses += floatval($amount);
            }
        }
        if (!empty($row['employee_details']) && is_array($row['employee_details'])) {
            foreach ($row['employee_details'] as $emp) {
                $total_employee_costs_sum += floatval($emp['payment'] ?? 0);
            }
        }
        $total_capacity += floatval($row['job_capacity'] ?? 0);
    }
}

$total_net_profit = $total_invoice_amount - $total_expenses - $total_employee_costs_sum;
$profit_margin = ($total_invoice_amount > 0) ? (($total_net_profit / $total_invoice_amount) * 100) : 0;

// Calculate overall_unpaid_count if not provided
$overall_unpaid_count = $overall_unpaid_count ?: array_reduce($job_data, function($count, $row) {
    $has_invoice = !is_null($row['invoice_no']);
    $outstanding = $has_invoice ? floatval($row['invoice_value'] ?? 0) - floatval($row['received_amount'] ?? 0) : 0;
    return $count + ($outstanding > 0 || !$has_invoice ? 1 : 0);
}, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - Cost Calculation</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(FULL_BASE_URL . '/css/cost_calculation.css', ENT_QUOTES, 'UTF-8'); ?>">
    <style>
        .status-section { margin-bottom: 20px; }
        .status-header { 
            cursor: pointer; 
            background: #f1f5f9; 
            padding: 10px; 
            border-radius: 5px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .status-header h2 { margin: 0; }
        .status-content { display: block; }
        .status-header i { transition: transform 0.3s; }
        .status-header.active i { transform: rotate(180deg); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cost Calculation Report</h1>
            <div class="header-controls">
                <button class="btn btn-primary" onclick="window.print()"><i class="ri-printer-line"></i> Print</button>
                <button class="btn btn-primary" onclick="window.location.href='<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>'"><i class="ri-arrow-left-line"></i> Go Back</button>
                <button class="btn btn-secondary" onclick="downloadCSV()"><i class="ri-download-line"></i> Download CSV</button>
            </div>
        </div>

        <!-- Debug Output -->
        <pre style="display: none;">
            Filtered Job Groups: <?php print_r($filtered_job_groups); ?>
        </pre>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>
            <div class="filter-card">
                <form method="GET" class="filter-form" id="filterForm" action="<?php echo htmlspecialchars(FULL_BASE_URL . '/reports/cost_calculation', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="filter-item">
                        <label>Invoice ID</label>
                        <input type="text" name="invoice_id" value="<?php echo htmlspecialchars($filters['invoice_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="filter-item">
                        <label>Customer</label>
                        <select name="customer_name">
                            <option value="">All</option>
                            <?php foreach ($customer_refs as $ref): ?>
                                <option value="<?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php echo $filters['customer_reference'] === $ref ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>Company Ref</label>
                        <select name="client_ref">
                            <option value="">All</option>
                            <?php foreach ($company_refs as $ref): ?>
                                <option value="<?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php echo $filters['company_reference'] === $ref ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>Payment Status</label>
                        <select name="status">
                            <option value="">All</option>
                            <option value="paid" <?php echo $filters['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="unpaid" <?php echo $filters['status'] === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>Completion Status</label>
                        <select name="completion">
                            <option value="">All</option>
                            <option value="1.0" <?php echo $filters['completion'] === '1.0' ? 'selected' : ''; ?>>Completed</option>
                            <option value="0.5" <?php echo $filters['completion'] === '0.5' ? 'selected' : ''; ?>>Ongoing</option>
                            <option value="0.2" <?php echo $filters['completion'] === '0.2' ? 'selected' : ''; ?>>Started</option>
                            <option value="0.0" <?php echo $filters['completion'] === '0.0' ? 'selected' : ''; ?>>Not Started</option>
                            <option value="0.1" <?php echo $filters['completion'] === '0.1' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>From Date</label>
                        <input type="date" name="from_date" value="<?php echo htmlspecialchars($filters['from_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="filter-item">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="<?php echo htmlspecialchars($filters['to_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="ri-filter-line"></i> Filter</button>
                </form>
            </div>

            <div class="summary-card">
                <h2>Filtered Summary</h2>
                <div class="summary-grid">
                    <div class="summary-item"><h3>Total Invoices</h3><p><?php echo number_format($total_invoice_amount, 2); ?></p></div>
                    <div class="summary-item paid"><h3>Paid Amount</h3><p><?php echo number_format($total_paid_amount, 2); ?></p></div>
                    <div class="summary-item unpaid"><h3>Unpaid Amount</h3><p><?php echo number_format($total_unpaid_amount, 2); ?></p></div>
                    <div class="summary-item"><h3>Unpaid Count</h3><p><?php echo htmlspecialchars($unpaid_invoice_count, ENT_QUOTES, 'UTF-8'); ?></p></div>
                    <div class="summary-item unpaid"><h3>Due Balance</h3><p><?php echo number_format($due_balance, 2); ?></p></div>
                </div>
                <div class="section">
                    <h2>Additional Metrics</h2>
                    <div class="summary-grid">
                        <div class="summary-item"><h3>Total Expenses</h3><p><?php echo number_format($total_expenses, 2); ?></p></div>
                        <div class="summary-item"><h3>Total Employee Costs</h3><p><?php echo number_format($total_employee_costs_sum, 2); ?></p></div>
                        <div class="summary-item"><h3>Total Jobs</h3><p><?php echo htmlspecialchars(array_sum(array_map('count', $filtered_job_groups)), ENT_QUOTES, 'UTF-8'); ?></p></div>
                        <div class="summary-item"><h3>Total Capacity</h3><p><?php echo number_format($total_capacity, 2); ?></p></div>
                        <div class="summary-item <?php echo $total_net_profit >= 0 ? 'profit-positive' : 'profit-negative'; ?>">
                            <h3>Net Profit</h3><p><?php echo number_format($total_net_profit, 2); ?></p>
                        </div>
                        <div class="summary-item <?php echo $profit_margin >= 0 ? 'profit-positive' : 'profit-negative'; ?>">
                            <h3>Profit Margin</h3><p><?php echo number_format($profit_margin, 2); ?>%</p>
                        </div>
                    </div>
                </div>
                <div class="section overall">
                    <h2>Overall Summary</h2>
                    <div class="summary-grid">
                        <div class="summary-item"><h3>Total Invoices</h3><p><?php echo number_format($overall_invoice_amount, 2); ?></p></div>
                        <div class="summary-item paid"><h3>Paid Amount</h3><p><?php echo number_format($overall_paid_amount, 2); ?></p></div>
                        <div class="summary-item unpaid"><h3>Unpaid Amount</h3><p><?php echo number_format($overall_unpaid_amount, 2); ?></p></div>
                        <div class="summary-item"><h3>Unpaid Count</h3><p><?php echo htmlspecialchars($overall_unpaid_count, ENT_QUOTES, 'UTF-8'); ?></p></div>
                        <div class="summary-item unpaid"><h3>Due Balance</h3><p><?php echo number_format($overall_due_balance, 2); ?></p></div>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <h2>Financial Overview</h2>
                <div class="chart-container">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>

            <div class="table-card">
                <h2>Detailed Job Analysis</h2>
                <?php foreach ($filtered_job_groups as $status => $jobs): ?>
                    <?php if (!empty($jobs)): ?>
                        <div class="status-section">
                            <div class="status-header">
                                <h2><?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?> Jobs (<?php echo count($jobs); ?>)</h2>
                                <i class="ri-arrow-down-s-line"></i>
                            </div>
                            <div class="status-content active">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Job Details</th>
                                            <th>Date</th>
                                            <th>Capacity</th>
                                            <th>Invoice Details</th>
                                            <th>Expenses</th>
                                            <th>Employee Costs</th>
                                            <th>Outstanding</th>
                                            <th>Net Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($jobs as $row): ?>
                                            <?php
                                            $jobDetails = "<ul>";
                                            $jobDetails .= "<li>Job ID: " . htmlspecialchars($row['job_id'] ?? '', ENT_QUOTES, 'UTF-8') . "</li>";
                                            $jobDetails .= "<li>Location: " . htmlspecialchars($row['location'] ?? '', ENT_QUOTES, 'UTF-8') . "</li>";
                                            $jobDetails .= "<li>Company Ref: " . htmlspecialchars($row['company_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</li>";
                                            $jobDetails .= "<li>Engineer: " . htmlspecialchars($row['engineer'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</li>";
                                            $jobDetails .= "</ul>";

                                            $has_invoice = !is_null($row['invoice_no']);
                                            $invoiceDetails = "<ul>";
                                            if ($has_invoice) {
                                                $invoiceDetails .= "<li>No: " . htmlspecialchars($row['invoice_no'] ?? '', ENT_QUOTES, 'UTF-8') . "</li>";
                                                $invoiceDetails .= "<li>Value: " . number_format(floatval($row['invoice_value'] ?? 0), 2) . "</li>";
                                                $invoiceDetails .= "<li>Received: " . number_format(floatval($row['received_amount'] ?? 0), 2) . "</li>";
                                                $invoiceDetails .= "<li>Date Paid: " . htmlspecialchars($row['payment_received_date'] ?? '', ENT_QUOTES, 'UTF-8') . "</li>";
                                            } else {
                                                $invoiceDetails .= "<li>No Invoice</li>";
                                            }
                                            $invoiceDetails .= "</ul>";

                                            $expenseDetails = "<ul>";
                                            $totalExpensesForJob = 0;
                                            if (!empty($row['expense_details'])) {
                                                foreach ($row['expense_details'] as $category => $amount) {
                                                    $expenseDetails .= "<li>" . htmlspecialchars($category ?? '', ENT_QUOTES, 'UTF-8') . ": " . number_format(floatval($amount ?? 0), 2) . "</li>";
                                                    $totalExpensesForJob += floatval($amount ?? 0);
                                                }
                                            }
                                            if ($totalExpensesForJob == 0) {
                                                $expenseDetails .= "<li>No expenses recorded</li>";
                                            }
                                            $expenseDetails .= "</ul>";

                                            $employeeDetails = "<ul>";
                                            $totalEmployeeCostsForJob = 0;
                                            if (!empty($row['employee_details']) && is_array($row['employee_details'])) {
                                                foreach ($row['employee_details'] as $emp) {
                                                    $employeeDetails .= "<li>" . htmlspecialchars($emp['emp_name'] ?? 'Unknown Employee', ENT_QUOTES, 'UTF-8') . ": " . number_format(floatval($emp['payment'] ?? 0), 2);
                                                    $totalEmployeeCostsForJob += floatval($emp['payment'] ?? 0);
                                                    if (!empty($emp['days']) && is_array($emp['days'])) {
                                                        $employeeDetails .= "<ul>";
                                                        foreach ($emp['days'] as $day) {
                                                            $presence = floatval($day['presence'] ?? 0);
                                                            $presenceText = $presence == 1.0 ? 'Full Day' : ($presence == 0.5 ? 'Half Day' : number_format($presence, 1) . ' Days');
                                                            $employeeDetails .= "<li>";
                                                            $employeeDetails .= "Date: " . htmlspecialchars($day['date'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "<br>";
                                                            $employeeDetails .= "Presence: " . $presenceText . "<br>";
                                                            $employeeDetails .= "Payment: " . number_format(floatval($day['payment'] ?? 0), 2);
                                                            $employeeDetails .= "</li>";
                                                        }
                                                        $employeeDetails .= "</ul>";
                                                    } else {
                                                        $employeeDetails .= " (No attendance recorded)";
                                                    }
                                                    $employeeDetails .= "</li>";
                                                }
                                            }
                                            if ($totalEmployeeCostsForJob == 0) {
                                                $employeeDetails .= "<li>No employee costs recorded for Job ID: " . htmlspecialchars($row['job_id'] ?? '', ENT_QUOTES, 'UTF-8') . " (Check attendance or rate data)</li>";
                                            }
                                            $employeeDetails .= "</ul>";

                                            $outstanding = $has_invoice ? floatval($row['invoice_value'] ?? 0) - floatval($row['received_amount'] ?? 0) : 0;
                                            $netProfit = $has_invoice ? floatval($row['invoice_value'] ?? 0) - $totalExpensesForJob - $totalEmployeeCostsForJob : -$totalExpensesForJob - $totalEmployeeCostsForJob;
                                            $displayDate = $row['date_completed'] === '0000-00-00' ? 'Not Set' : htmlspecialchars($row['date_completed'] ?? '', ENT_QUOTES, 'UTF-8');
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="collapsible">
                                                        <span class="total">Customer: <?php echo htmlspecialchars($row['customer_reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                        <i class="ri-arrow-down-s-line icon"></i>
                                                    </div>
                                                    <div class="details"><?php echo $jobDetails; ?></div>
                                                </td>
                                                <td><?php echo $displayDate; ?></td>
                                                <td><?php echo htmlspecialchars($row['job_capacity'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td>
                                                    <div class="collapsible">
                                                        <span class="total"><?php echo $has_invoice ? htmlspecialchars($row['invoice_no'] ?? '', ENT_QUOTES, 'UTF-8') : 'No Invoice'; ?></span>
                                                        <i class="ri-arrow-down-s-line icon"></i>
                                                    </div>
                                                    <div class="details"><?php echo $invoiceDetails; ?></div>
                                                </td>
                                                <td>
                                                    <div class="collapsible">
                                                        <span class="total">Total: <?php echo number_format($totalExpensesForJob, 2); ?></span>
                                                        <i class="ri-arrow-down-s-line icon"></i>
                                                    </div>
                                                    <div class="details"><?php echo $expenseDetails; ?></div>
                                                </td>
                                                <td>
                                                    <div class="collapsible">
                                                        <span class="total">Total: <?php echo number_format($totalEmployeeCostsForJob, 2); ?></span>
                                                        <i class="ri-arrow-down-s-line icon"></i>
                                                    </div>
                                                    <div class="details"><?php echo $employeeDetails; ?></div>
                                                </td>
                                                <td><?php echo $has_invoice ? number_format($outstanding, 2) : 'N/A'; ?></td>
                                                <td class="<?php echo $netProfit >= 0 ? 'profit-positive' : 'profit-negative'; ?>">
                                                    <?php echo number_format($netProfit, 2); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Row-level collapsible
            document.querySelectorAll('.collapsible').forEach(item => {
                item.addEventListener('click', () => {
                    const details = item.nextElementSibling;
                    item.classList.toggle('active');
                    details.style.display = details.style.display === 'block' ? 'none' : 'block';
                });
            });

            // Status section collapsible
            document.querySelectorAll('.status-header').forEach(header => {
                item.addEventListener('click', () => {
                    const content = header.nextElementSibling;
                    header.classList.toggle('active');
                    content.classList.toggle('active');
                    content.style.display = content.classList.contains('active') ? 'block' : 'none';
                });
            });

            const ctx = document.getElementById('financialChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Total Invoices', 'Paid Amount', 'Expenses', 'Employee Costs', 'Net Profit'],
                    datasets: [{
                        label: 'Financial Metrics',
                        data: [
                            <?php echo $total_invoice_amount; ?>,
                            <?php echo $total_paid_amount; ?>,
                            <?php echo $total_expenses; ?>,
                            <?php echo $total_employee_costs_sum; ?>,
                            <?php echo $total_net_profit; ?>
                        ],
                        backgroundColor: [
                            'rgba(30, 144, 255, 0.6)', 'rgba(16, 185, 129, 0.6)', 
                            'rgba(255, 107, 0, 0.6)', 'rgba(0, 196, 180, 0.6)',
                            '<?php echo $total_net_profit >= 0 ? "rgba(16, 185, 129, 0.6)" : "rgba(239, 68, 68, 0.6)"; ?>'
                        ],
                        borderColor: [
                            'rgba(30, 144, 255, 1)', 'rgba(16, 185, 129, 1)', 
                            'rgba(255, 107, 0, 1)', 'rgba(0, 196, 180, 1)',
                            '<?php echo $total_net_profit >= 0 ? "rgba(16, 185, 129, 1)" : "rgba(239, 68, 68, 1)"; ?>'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true } },
                    plugins: { legend: { display: false }, title: { display: true, text: 'Filtered Financial Overview' } }
                }
            });
        });

        function downloadCSV() {
            const form = document.getElementById('filterForm');
            const url = new URL(form.action);
            url.search = new URLSearchParams(new FormData(form)).toString();
            url.searchParams.set('download_csv', '1');
            window.location.href = url.toString();
        }
    </script>
</body>
</html>
