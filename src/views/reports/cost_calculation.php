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
        'to_date' => ''
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
    <style>
        :root {
            --primary: #ff6b00;
            --secondary: #00c4b4;
            --accent: #1e90ff;
            --background: #ffffff;
            --card-bg: #f9fafb;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --shadow: rgba(0, 0, 0, 0.1);
            --border: #e5e7eb;
            --success: #10b981;
            --danger: #ef4444;
            --hover-bg: #f1f5f9;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background);
            color: var(--text-dark);
            line-height: 1.6;
            font-size: 14px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 600;
        }

        .header-controls {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
            transition: background 0.2s, transform 0.1s;
        }

        .btn:hover { transform: translateY(-1px); }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: #167bd6; }
        .btn-secondary { background: var(--secondary); color: white; }
        .btn-secondary:hover { background: #00a896; }

        .filter-card, .summary-card, .table-card, .chart-card {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px var(--shadow);
            margin-bottom: 30px;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .filter-item label {
            display: block;
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 5px;
            color: var(--text-muted);
        }

        .filter-item input, .filter-item select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
        }

        .filter-item input:focus, .filter-item select:focus { border-color: var(--accent); }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
        }

        .summary-item {
            background: rgba(30, 144, 255, 0.05);
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }

        .summary-item h3 {
            font-size: 13px;
            color: var(--accent);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .summary-item p {
            font-size: 16px;
            font-weight: 600;
        }

        .summary-item.unpaid p { color: var(--danger); }
        .summary-item.paid p { color: var(--success); }
        .summary-item.neutral p { color: var(--text-dark); }
        .summary-item.profit-positive p { color: var(--success); }
        .summary-item.profit-negative p { color: var(--danger); }

        .summary-card h2 {
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .summary-card .section {
            border-top: 1px solid var(--border);
            padding-top: 15px;
            margin-top: 15px;
        }

        .table-card h2, .chart-card h2 {
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px var(--shadow);
        }

        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            background: var(--accent);
            color: white;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            font-size: 13px;
            color: var(--text-dark);
            vertical-align: middle;
        }

        .table tr:hover { background: var(--hover-bg); }

        .collapsible {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .collapsible .total { font-weight: 600; }
        .collapsible .icon { font-size: 12px; transition: transform 0.2s; }
        .collapsible.active .icon { transform: rotate(180deg); }

        .details {
            display: none;
            padding: 10px;
            background: var(--hover-bg);
            border-radius: 4px;
            margin-top: 5px;
        }

        .details ul {
            list-style: none;
            padding: 0;
            font-size: 12px;
            color: var(--text-muted);
        }

        .details ul ul { margin-left: 20px; font-size: 12px; }

        .profit-positive { color: var(--success); font-weight: 600; }
        .profit-negative { color: var(--danger); font-weight: 600; }

        .chart-container { max-width: 600px; margin: 0 auto; }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .filter-form { grid-template-columns: 1fr; }
            .summary-grid { grid-template-columns: 1fr; }
            .table { font-size: 12px; }
            .chart-container { max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cost Calculation Report</h1>
            <div class="header-controls">
                <button class="btn btn-primary" onclick="window.print()"><i class="ri-printer-line"></i> Print</button>
                <button class="btn btn-primary" onclick="window.location.href='<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/tables', ENT_QUOTES, 'UTF-8'); ?>'"><i class="ri-arrow-left-line"></i> Go Back</button>
                <button class="btn btn-secondary" onclick="downloadCSV()"><i class="ri-download-line"></i> Download CSV</button>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>
            <div class="filter-card">
                <form method="GET" class="filter-form" id="filterForm" action="<?php echo htmlspecialchars(FULL_BASE_URL . '/reports/cost_calculation', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="filter-item">
                        <label>Invoice ID</label>
                        <input type="text" name="invoice_id" value="<?php echo htmlspecialchars($filters['invoice_no'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="filter-item">
                        <label>Customer</label>
                        <select name="customer_name">
                            <option value="">All</option>
                            <?php foreach ($customer_refs as $ref): ?>
                                <option value="<?php echo htmlspecialchars($ref, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $filters['customer_reference'] === $ref ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ref, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>Company Ref</label>
                        <select name="client_ref">
                            <option value="">All</option>
                            <?php foreach ($company_refs as $ref): ?>
                                <option value="<?php echo htmlspecialchars($ref, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $filters['company_reference'] === $ref ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ref, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>Status</label>
                        <select name="status">
                            <option value="">All</option>
                            <option value="paid" <?php echo $filters['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="unpaid" <?php echo $filters['status'] === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>From Date</label>
                        <input type="date" name="from_date" value="<?php echo htmlspecialchars($filters['from_date'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="filter-item">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="<?php echo htmlspecialchars($filters['to_date'], ENT_QUOTES, 'UTF-8'); ?>">
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
                        <div class="summary-item"><h3>Total Jobs</h3><p><?php echo htmlspecialchars(count($job_data), ENT_QUOTES, 'UTF-8'); ?></p></div>
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
                        <?php foreach ($job_data as $row): ?>
                            <?php
                            $jobDetails = "<ul>";
                            $jobDetails .= "<li>Job ID: " . htmlspecialchars($row['job_id'], ENT_QUOTES, 'UTF-8') . "</li>";
                            $jobDetails .= "<li>Location: " . htmlspecialchars($row['location'], ENT_QUOTES, 'UTF-8') . "</li>";
                            $jobDetails .= "<li>Company Ref: " . htmlspecialchars($row['company_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</li>";
                            $jobDetails .= "<li>Engineer: " . htmlspecialchars($row['engineer'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</li>";
                            $jobDetails .= "</ul>";

                            $invoiceDetails = "<ul>";
                            $invoiceDetails .= "<li>No: " . htmlspecialchars($row['invoice_no'], ENT_QUOTES, 'UTF-8') . "</li>";
                            $invoiceDetails .= "<li>Value: " . number_format(floatval($row['invoice_value']), 2) . "</li>";
                            $invoiceDetails .= "<li>Received: " . number_format(floatval($row['received_amount']), 2) . "</li>";
                            $invoiceDetails .= "<li>Date Paid: " . htmlspecialchars($row['payment_received_date'], ENT_QUOTES, 'UTF-8') . "</li>";
                            $invoiceDetails .= "</ul>";

                            $expenseDetails = "<ul>";
                            if (!empty($row['expense_details'])) {
                                foreach ($row['expense_details'] as $category => $amount) {
                                    $expenseDetails .= "<li>" . htmlspecialchars($category, ENT_QUOTES, 'UTF-8') . ": " . number_format(floatval($amount), 2) . "</li>";
                                }
                            } else {
                                $expenseDetails .= "<li>No expenses recorded</li>";
                            }
                            $expenseDetails .= "</ul>";

                            $employeeDetails = "<ul>";
                            if (!empty($row['employee_details']) && is_array($row['employee_details'])) {
                                foreach ($row['employee_details'] as $emp) {
                                    $employeeDetails .= "<li>" . htmlspecialchars($emp['emp_name'] ?? 'Unknown Employee', ENT_QUOTES, 'UTF-8') . ": " . number_format(floatval($emp['payment'] ?? 0), 2);
                                    if (!empty($emp['days']) && is_array($emp['days'])) {
                                        $employeeDetails .= "<ul>";
                                        foreach ($emp['days'] as $day) {
                                            $presence = floatval($day['presence'] ?? 0);
                                            $presenceText = $presence == 1.0 ? 'Full Day' : ($presence == 0.5 ? 'Half Day' : number_format($presence, 1) . ' Days');
                                            $rate = number_format(floatval($day['rate'] ?? 0), 2);
                                            $payment = number_format(floatval($day['payment'] ?? 0), 2);
                                            $date = htmlspecialchars($day['date'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                            $employeeDetails .= "<li>$date: $presenceText (Rate: $rate, Paid: $payment)</li>";
                                        }
                                        $employeeDetails .= "</ul>";
                                    } else {
                                        $employeeDetails .= " (No attendance recorded)";
                                    }
                                    $employeeDetails .= "</li>";
                                }
                            } else {
                                $employeeDetails .= "<li>No employee costs recorded for Job ID: " . htmlspecialchars($row['job_id'], ENT_QUOTES, 'UTF-8') . " (Check attendance or rate data)</li>";
                            }
                            $employeeDetails .= "</ul>";

                            $outstanding = floatval($row['invoice_value']) - floatval($row['received_amount']);
                            ?>
                            <tr>
                                <td>
                                    <div class="collapsible">
                                        <span class="total">Customer: <?php echo htmlspecialchars($row['customer_reference'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $jobDetails; ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($row['date_completed'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($row['job_capacity'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <div class="collapsible">
                                        <span class="total"><?php echo htmlspecialchars($row['invoice_no'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $invoiceDetails; ?></div>
                                </td>
                                <td>
                                    <div class="collapsible">
                                        <span class="total">Total: <?php echo number_format(floatval($row['operational_expenses']), 2); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $expenseDetails; ?></div>
                                </td>
                                <td>
                                    <div class="collapsible">
                                        <span class="total">Total: <?php echo number_format(floatval($row['total_employee_costs']), 2); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $employeeDetails; ?></div>
                                </td>
                                <td><?php echo number_format($outstanding, 2); ?></td>
                                <td class="<?php echo $row['net_profit'] >= 0 ? 'profit-positive' : 'profit-negative'; ?>">
                                    <?php echo number_format(floatval($row['net_profit']), 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.collapsible').forEach(item => {
                item.addEventListener('click', () => {
                    const details = item.nextElementSibling;
                    item.classList.toggle('active');
                    details.style.display = details.style.display === 'block' ? 'none' : 'block';
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