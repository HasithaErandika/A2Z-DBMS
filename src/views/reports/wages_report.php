<?php
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
}

// Calculate Maximum Working Days based on filters
$max_working_days = 0;
if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
    $start = new DateTime($filters['from_date']);
    $end = new DateTime($filters['to_date']);
    $interval = $start->diff($end);
    $max_working_days = $interval->days + 1; // Include end date
} elseif (!empty($filters['year']) && !empty($filters['month'])) {
    $year = $filters['year'];
    $month = $filters['month'];
    $max_working_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
} elseif (!empty($filters['year'])) {
    $year = $filters['year'];
    $max_working_days = (new DateTime("$year-12-31"))->format('L') ? 366 : 365; // Leap year check
} else {
    $max_working_days = 365; // Default to a year if no filters
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - Wages Report</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(FULL_BASE_URL . '/css/wages_report.css', ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo htmlspecialchars($report_title ?? 'Wages Report', ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="header-controls">
                <button class="btn btn-primary" onclick="window.print()"><i class="ri-printer-line"></i> Print</button>
                <button class="btn btn-primary" onclick="window.location.href='<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>'"><i class="ri-arrow-left-line"></i> Go Back</button>
                <button class="btn btn-secondary" onclick="downloadCSV()"><i class="ri-download-line"></i> Download CSV</button>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>
            <!-- Debug: Display wage_data for inspection -->
            <?php if (empty($wage_data)): ?>
                <div class="error-message">No wage data available for the selected filters.</div>
            <?php else: ?>
                <pre style="display: none;">wage_data: <?php print_r($wage_data); ?></pre>
            <?php endif; ?>

            <div class="filter-card">
                <form method="GET" class="filter-form" id="filterForm" action="<?php echo htmlspecialchars(FULL_BASE_URL . '/reports/wages_report', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="filter-item">
                        <label for="year">Select Year</label>
                        <select id="year" name="year" required>
                            <?php $selectedYear = $filters['year'] ?? date('Y'); for ($year = 2023; $year <= 2033; $year++):  ?>
                            <option value="<?php echo $year; ?>" <?php echo ($year == $selectedYear) ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                                </option>
                                <?php endfor; ?>
                        </select>
                    </div>

                    <div class="filter-item">
                        <label for="month">Select Month</label>
                        <select id="month" name="month" required>
                            <option value="">All Months</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php echo $m; ?>" <?php echo ($filters['month'] ?? '') == $m ? 'selected' : ''; ?>>
                                    <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label for="emp_id">Select Employee</label>
                        <select id="emp_id" name="emp_id">
                            <option value="">All Employees</option>
                            <?php foreach ($employee_refs as $emp): ?>
                                <option value="<?php echo htmlspecialchars($emp['emp_id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($filters['emp_id'] ?? '') === $emp['emp_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($emp['emp_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="ri-filter-line"></i> Apply Filter</button>
                </form>
            </div>

            <div class="summary-card">
                <h2>Filtered Summary</h2>
                <div class="summary-grid">
                    <div class="summary-item"><h3>Total Wages</h3><p><?php echo number_format($total_wages ?? 0, 2); ?></p></div>
                    <div class="summary-item"><h3>Daily Wages</h3><p><?php echo number_format($total_daily_wages ?? 0, 2); ?></p></div>
                    <div class="summary-item"><h3>Fixed Wages</h3><p><?php echo number_format($total_fixed_wages ?? 0, 2); ?></p></div>
                    <div class="summary-item"><h3>EPF Costs</h3><p><?php echo number_format($epf_costs ?? 0, 2); ?></p></div>
                    <div class="summary-item"><h3>Employee Count</h3><p><?php echo htmlspecialchars($employee_count ?? 0, ENT_QUOTES, 'UTF-8'); ?></p></div>
                    <div class="summary-item"><h3>Average Wage</h3><p><?php echo number_format($avg_wage_per_employee ?? 0, 2); ?></p></div>
                </div>
                <div class="section">
                    <h2>Additional Metrics</h2>
                    <div class="summary-grid">
                        <div class="summary-item"><h3>Maximum Working Days</h3><p><?php echo number_format($max_working_days, 0); ?></p></div>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <h2>Financial Overview</h2>
                <div class="chart-container">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>

            <?php
            // Ensure $wage_data is an array
            $wage_data = $wage_data ?? [];
            // Separate Daily and Fixed employees with explicit rate_type check
            $daily_wage_employees = array_filter($wage_data, fn($emp) => ($emp['rate_type'] ?? 'Daily') !== 'Fixed');
            $fixed_rate_employees = array_filter($wage_data, fn($emp) => ($emp['rate_type'] ?? 'Daily') === 'Fixed');
            ?>

            <!-- Daily Wage Employees Table -->
            <div class="table-card">
                <h2>Daily Wage Employees Analysis</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Presence Count</th>
                            <th>Daily Wage</th>
                            <th>Earned</th>
                            <th>Total Payable</th>
                            <th>Total Paid</th>
                            <th>Net Payable</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($daily_wage_employees)): ?>
                            <?php foreach ($daily_wage_employees as $emp):
                                $emp_name = $emp['emp_name'] ?? 'Unknown';
                                $daily_wage = floatval($emp['rate_amount'] ?? 0);
                                $presence_count = floatval($emp['attendance_summary']['presence_count'] ?? 0);
                                // Get categorized paid amounts
                                $paid_amounts = $emp['paid_amount'] ?? [
                                    'Monthly Salary' => 0,
                                    'Daily Wage' => 0,
                                    'Advance' => 0,
                                    'Other' => 0
                                ];
                                // Sum only Monthly Salary, Advance, and Other for Total Paid
                                $total_paid = floatval($paid_amounts['Monthly Salary']) +
                                              floatval($paid_amounts['Advance']) +
                                              floatval($paid_amounts['Other']);
                                // Create dropdown content for payment and attendance details
                                $details = "<div class='details-section'>";
                                $details .= "<h4>Payment Details</h4>";
                                $details .= "<ul>";
                                $details .= "<li>Monthly Salary: " . number_format($paid_amounts['Monthly Salary'], 2) . "</li>";
                                $details .= "<li>Advance: " . number_format($paid_amounts['Advance'], 2) . "</li>";
                                $details .= "<li>Other: " . number_format($paid_amounts['Other'], 2) . "</li>";
                                $details .= "</ul>";
                                $details .= "<h4>Attendance Details</h4>";
                                $details .= "<ul>";
                                $details .= "<li>Presence Count: " . number_format($presence_count, 0) . "</li>";
                                $details .= "<li>Records: <ul>";
                                foreach ($emp['attendance_details'] ?? [] as $record) {
                                    $details .= "<li>";
                                    $details .= "Date: " . htmlspecialchars($record['date'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "<br>";
                                    $details .= "Presence: " . number_format($record['presence'] ?? 0, 2) . "<br>";
                                    $details .= "Payment: " . number_format($record['payment'] ?? 0, 2) . "<br>";
                                    $details .= "Customer Reference: " . htmlspecialchars($record['customer_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "<br>";
                                    $details .= "Location: " . htmlspecialchars($record['location'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "<br>";
                                    $details .= "Job Capacity: " . htmlspecialchars($record['job_capacity'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "<br>";
                                    $details .= "Project Description: " . htmlspecialchars($record['project_description'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "<br>";
                                    $details .= "Company Reference: " . htmlspecialchars($record['company_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                    $details .= "</li>";
                                }
                                $details .= "</ul></li>";
                                $details .= "</ul>";
                                $details .= "</div>";

                                $earned = $presence_count * $daily_wage;
                                $total_payable = $earned;
                                $net_payable = $total_payable - $total_paid;
                            ?>
                            <tr>
                                <td>
                                    <div class="collapsible">
                                        <span class="total"><?php echo htmlspecialchars($emp_name, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $details; ?></div>
                                </td>
                                <td><?php echo number_format($presence_count, 0); ?></td>
                                <td><?php echo number_format($daily_wage, 2); ?></td>
                                <td><?php echo number_format($earned, 2); ?></td>
                                <td><?php echo number_format($total_payable, 2); ?></td>
                                <td><?php echo number_format($total_paid, 2); ?></td>
                                <td><?php echo number_format($net_payable, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7">No daily wage employees found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Fixed Rate Employees Table -->
            <div class="table-card">
                <h2>Fixed Rate Employees Analysis</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Basic Salary</th>
                            <th>ETF</th>
                            <th>EPF (Employee)</th>
                            <th>EPF (Company)</th>
                            <th>Total Payable</th>
                            <th>Total Paid</th>
                            <th>Net Payable</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($fixed_rate_employees)): ?>
                            <?php foreach ($fixed_rate_employees as $emp):
                                $emp_name = $emp['emp_name'] ?? 'Unknown';
                                $basic_salary = floatval($emp['basic_salary'] ?? $emp['rate_amount'] ?? 0);
                                // Get categorized paid amounts
                                $paid_amounts = $emp['paid_amount'] ?? [
                                    'Monthly Salary' => 0,
                                    'Daily Wage' => 0,
                                    'Advance' => 0,
                                    'Other' => 0
                                ];
                                // Sum only Monthly Salary, Advance, and Other for Total Paid
                                $total_paid = floatval($paid_amounts['Monthly Salary']) +
                                              floatval($paid_amounts['Advance']) +
                                              floatval($paid_amounts['Other']);
                                // Create dropdown content for payment and attendance details
                                $details = "<div class='details-section'>";
                                $details .= "<h4>Payment Details</h4>";
                                $details .= "<ul>";
                                $details .= "<li>Monthly Salary: " . number_format($paid_amounts['Monthly Salary'], 2) . "</li>";
                                $details .= "<li>Advance: " . number_format($paid_amounts['Advance'], 2) . "</li>";
                                $details .= "<li>Other: " . number_format($paid_amounts['Other'], 2) . "</li>";
                                $details .= "</ul>";
                                $details .= "<h4>Attendance Details</h4>";
                                $details .= "<ul>";
                                $details .= "<li>Presence Count: " . number_format($emp['attendance_summary']['presence_count'] ?? 0, 0) . "</li>";
                                $details .= "<li>Records: <ul>";
                                foreach ($emp['attendance_details'] ?? [] as $record) {
                                    $details .= "<li>";
                                    $details .= "Date: " . htmlspecialchars($record['date'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "<br>";
                                    $details .= "Presence: " . number_format($record['presence'] ?? 0, 2) . "<br>";
                                    $details .= "Payment: " . number_format($record['payment'] ?? 0, 2) . "<br>";
                                    $details .= "Customer Reference: " . htmlspecialchars($record['customer_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "<br>";
                                    $details .= "Location: " . htmlspecialchars($record['location'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "<br>";
                                    $details .= "Job Capacity: " . htmlspecialchars($record['job_capacity'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "<br>";
                                    $details .= "Project Description: " . htmlspecialchars($record['project_description'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "<br>";
                                    $details .= "Company Reference: " . htmlspecialchars($record['company_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                    $details .= "</li>";
                                }
                                $details .= "</ul></li>";
                                $details .= "</ul>";
                                $details .= "</div>";

                                // ETF, EPF calculations
                                $ETF_employee = $basic_salary * 0.03;
                                $EPF_employee = $basic_salary * 0.08;
                                $EPF_company = $basic_salary * 0.12;

                                $total_payable = $basic_salary + $EPF_company;
                                $net_payable = $total_payable - $ETF_employee - $EPF_employee - $total_paid;
                            ?>
                            <tr>
                                <td>
                                    <div class="collapsible">
                                        <span class="total"><?php echo htmlspecialchars($emp_name, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $details; ?></div>
                                </td>
                                <td><?php echo number_format($basic_salary, 2); ?></td>
                                <td><?php echo number_format($ETF_employee, 2); ?></td>
                                <td><?php echo number_format($EPF_employee, 2); ?></td>
                                <td><?php echo number_format($EPF_company, 2); ?></td>
                                <td><?php echo number_format($total_payable, 2); ?></td>
                                <td><?php echo number_format($total_paid, 2); ?></td>
                                <td><?php echo number_format($net_payable, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8">No fixed rate employees found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="percentage-panel">
                    <p>ETF: 3%</p>
                    <p>EPF (Employee): 8%</p>
                    <p>EPF (Company): 12%</p>
                </div>
            </div>

            <!-- Labor Wages Summation Table -->
            <div class="table-card">
                <h2>Labor Wages Summation</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Labor Name</th>
                            <th>Total Days</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($labor_wages_data['summations'] ?? [] as $sum): ?>
                            <?php
                            $laborDetails = "<ul>";
                            foreach ($labor_wages_data['details'][$sum['labor_name']] ?? [] as $detail) {
                                $laborDetails .= "<li>Job ID: " . htmlspecialchars($detail['job_id'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . ", Date: " . htmlspecialchars($detail['expensed_date'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . ", Description: " . htmlspecialchars($detail['description'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . ", Amount: " . number_format($detail['expense_amount'] ?? 0, 2) . "</li>";
                            }
                            $laborDetails .= "</ul>";
                            ?>
                            <tr>
                                <td>
                                    <div class="collapsible">
                                        <span class="total"><?php echo htmlspecialchars($sum['labor_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $laborDetails; ?></div>
                                </td>
                                <td><?php echo number_format($sum['total_days'] ?? 0, 0); ?></td>
                                <td><?php echo number_format($sum['total_amount'] ?? 0, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($labor_wages_data['summations'])): ?>
                            <tr><td colspan="3">No labor wages summations found for the selected filters.</td></tr>
                        <?php endif; ?>
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

            const ctx = document.getElementById('financialChart')?.getContext('2d');
            if (ctx) {
                const financialData = [
                    <?php echo isset($total_wages) ? floatval($total_wages) : 0; ?>,
                    <?php echo isset($total_daily_wages) ? floatval($total_daily_wages) : 0; ?>,
                    <?php echo isset($total_fixed_wages) ? floatval($total_fixed_wages) : 0; ?>,
                    <?php echo isset($epf_costs) ? floatval($epf_costs) : 0; ?>,
                    <?php echo isset($avg_wage_per_employee) ? floatval($avg_wage_per_employee) : 0; ?>
                ];
                if (financialData.every(val => val === 0)) {
                    ctx.parentElement.innerHTML = '<p>No financial data available.</p>';
                } else {
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Total Wages', 'Daily Wages', 'Fixed Wages', 'EPF Costs', 'Average Wage'],
                            datasets: [{
                                label: 'Financial Metrics',
                                data: financialData,
                                backgroundColor: [
                                    'rgba(30, 144, 255, 0.6)', 'rgba(16, 185, 129, 0.6)', 
                                    'rgba(255, 99, 132, 0.6)', 'rgba(255, 107, 0, 0.6)', 
                                    'rgba(0, 196, 180, 0.6)'
                                ],
                                borderColor: [
                                    'rgba(30, 144, 255, 1)', 'rgba(16, 185, 129, 1)', 
                                    'rgba(255, 99, 132, 1)', 'rgba(255, 107, 0, 1)', 
                                    'rgba(0, 196, 180, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: { y: { beginAtZero: true } },
                            plugins: { legend: { display: false }, title: { display: true, text: 'Filtered Financial Overview' } }
                        }
                    });
                }
            }
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