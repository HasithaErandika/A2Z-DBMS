<?php
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
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
    <link rel="stylesheet" href="<?php echo htmlspecialchars(FULL_BASE_URL . '/css/cost_calculation.css', ENT_QUOTES, 'UTF-8'); ?>">
    <style>
        .details ul {
            list-style-type: none;
            padding-left: 0;
        }
        .details ul li {
            margin-bottom: 0.5rem;
        }
        .details {
            display: none;
        }
        .collapsible.active + .details {
            display: block;
        }
        .percentage-panel {
            margin-top: 1rem;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .percentage-panel p {
            margin: 0.25rem 0;
        }
        .table-card + .table-card {
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo htmlspecialchars($report_title, ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="header-controls">
                <button class="btn btn-primary" onclick="window.print()"><i class="ri-printer-line"></i> Print</button>
                <button class="btn btn-primary" onclick="window.location.href='<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>'"><i class="ri-arrow-left-line"></i> Go Back</button>
                <button class="btn btn-secondary" onclick="downloadCSV()"><i class="ri-download-line"></i> Download CSV</button>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>
            <div class="filter-card">
                <form method="GET" class="filter-form" id="filterForm" action="<?php echo htmlspecialchars(FULL_BASE_URL . '/reports/wages_report', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="filter-item">
                        <label for="year">Select Year</label>
                        <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($filters['year'], ENT_QUOTES, 'UTF-8'); ?>" min="2024" max="2025" required>
                    </div>
                    <div class="filter-item">
                        <label for="month">Select Month</label>
                        <select id="month" name="month" required>
                            <option value="">All Months</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php echo $m; ?>" <?php echo $filters['month'] == $m ? 'selected' : ''; ?>>
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
                                <option value="<?php echo htmlspecialchars($emp['emp_id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $filters['emp_id'] === $emp['emp_id'] ? 'selected' : ''; ?>>
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
                    <div class="summary-item"><h3>Total Wages</h3><p><?php echo number_format($total_wages, 2); ?></p></div>
                    <div class="summary-item"><h3>Daily Wages</h3><p><?php echo number_format($total_daily_wages, 2); ?></p></div>
                    <div class="summary-item"><h3>EPF Costs</h3><p><?php echo number_format($epf_costs, 2); ?></p></div>
                    <div class="summary-item"><h3>Employee Count</h3><p><?php echo htmlspecialchars($employee_count, ENT_QUOTES, 'UTF-8'); ?></p></div>
                    <div class="summary-item"><h3>Average Wage</h3><p><?php echo number_format($avg_wage_per_employee, 2); ?></p></div>
                </div>
                <div class="section">
                    <h2>Additional Metrics</h2>
                    <div class="summary-grid">
                        <div class="summary-item"><h3>Total Working Days</h3><p><?php echo number_format($total_working_days, 2); ?></p></div>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <h2>Financial Overview</h2>
                <div class="chart-container">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>

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
                            <th>Basic Salary</th>
                            <th>ETF</th>
                            <th>EPF (Employee)</th>
                            <th>EPF (Company)</th>
                            <th>Total Payable</th>
                            <th>Paid Amount</th>
                            <th>Net Payable</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Hardcoded basic salaries for Daily rate_type employees
                        $basic_salaries = [
                            'W.H. Danushka Indrajith Welikala' => 30000,
                            'M.D. Prabhath Kumara Sanjeewa' => 25000,
                            'W.P Kasun Udayanga Kumara' => 25000,
                            'H.A.I. Wickramasinghe' => 50000,
                            'H.M. Tharindu Chathuranga' => 25000,
                            'Athula Munasinghe' => 25000,
                            'A. Chaminda Saman Kumara' => 25000,
                            'H.A.S.K Wickramasinghe' => 50000,
                            'H.A.H.E. Wickramasinghe' => 50000
                        ];

                        $daily_wage_employees = array_filter($wage_data, function($emp) {
                            return ($emp['rate_type'] ?? 'Daily') === 'Daily';
                        });

                        foreach ($daily_wage_employees as $emp):
                            $emp_name = $emp['emp_name'];
                            $rate_type = $emp['rate_type'] ?? 'Daily';
                            $daily_wage = $emp['rate_amount'] ?? 0;
                            $presence_count = $emp['total_days'] ?? 0;
                            $total_payment = $emp['total_payment'] ?? 0;
                            $earned = $presence_count * $daily_wage;
                            $paid_amount = $total_payment; // Assuming paid amount is total_payment

                            // Basic Salary from hardcoded array
                            $basic_salary = isset($basic_salaries[$emp_name]) ? $basic_salaries[$emp_name] : 0;

                            // Calculate ETF, EPF (Employee), EPF (Company)
                            $ETF_employee = $basic_salary * 0.03;
                            $EPF_employee = $basic_salary * 0.08;
                            $EPF_company = $basic_salary * 0.12;

                            // Calculate Total Payable and Net Payable
                            $total_payable = $total_payment + $basic_salary + $EPF_company;
                            $net_payable = $total_payable - $ETF_employee - $EPF_employee - $paid_amount;

                            // Collapsible details
                            $employeeDetails = "<ul>";
                            $employeeDetails .= "<li>Employee ID: " . htmlspecialchars($emp['emp_id'], ENT_QUOTES, 'UTF-8') . "</li>";
                            $employeeDetails .= "<li>Name: " . htmlspecialchars($emp_name, ENT_QUOTES, 'UTF-8') . "</li>";
                            $employeeDetails .= "<li>Rate Type: " . htmlspecialchars($rate_type, ENT_QUOTES, 'UTF-8') . "</li>";
                            $employeeDetails .= "</ul>";

                            $attendanceDetails = "<ul>";
                            $zeroPresenceCount = 0;
                            $attendance_records = $emp['attendance_details'] ?? [];
                            foreach ($attendance_records as $detail) {
                                $presence = floatval($detail['presence']);
                                if ($presence == 0.0) {
                                    $zeroPresenceCount++;
                                    continue;
                                }
                                $presenceText = $presence == 1.0 ? 'Full Day' : ($presence == 0.5 ? 'Half Day' : number_format($presence, 1) . ' Days');
                                $date = htmlspecialchars($detail['date'], ENT_QUOTES, 'UTF-8');
                                $payment = number_format($detail['payment'], 2);
                                $customer_reference = htmlspecialchars($detail['customer_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                $location = htmlspecialchars($detail['location'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                $job_capacity = htmlspecialchars($detail['job_capacity'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                $project_description = htmlspecialchars($detail['project_description'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                $company_reference = htmlspecialchars($detail['company_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
                                $attendanceDetails .= "<li>$date: $presenceText, Payment: $payment, Customer: $customer_reference, Location: $location, Capacity: $job_capacity, Project: $project_description, Company: $company_reference</li>";
                            }
                            if ($zeroPresenceCount > 0) {
                                $attendanceDetails .= "<li>$zeroPresenceCount days with 0.0 presence</li>";
                            }
                            if (empty($attendance_records) || $zeroPresenceCount == count($attendance_records)) {
                                $attendanceDetails .= "<li>No attendance recorded</li>";
                            }
                            $attendanceDetails .= "</ul>";
                        ?>
                            <tr>
                                <td>
                                    <div class="collapsible">
                                        <span class="total"><?php echo htmlspecialchars($emp_name, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $employeeDetails; ?></div>
                                </td>
                                <td><?php echo number_format($presence_count, 2); ?></td>
                                <td><?php echo number_format($daily_wage, 2); ?></td>
                                <td><?php echo number_format($earned, 2); ?></td>
                                <td><?php echo number_format($basic_salary, 2); ?></td>
                                <td><?php echo number_format($ETF_employee, 2); ?></td>
                                <td><?php echo number_format($EPF_employee, 2); ?></td>
                                <td><?php echo number_format($EPF_company, 2); ?></td>
                                <td><?php echo number_format($total_payable, 2); ?></td>
                                <td><?php echo number_format($paid_amount, 2); ?></td>
                                <td><?php echo number_format($net_payable, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($daily_wage_employees)): ?>
                            <tr>
                                <td colspan="11">No daily wage employees found.</td>
                            </tr>
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
                            <th>Paid Amount</th>
                            <th>Net Payable</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $fixed_rate_employees = array_filter($wage_data, function($emp) {
                            return ($emp['rate_type'] ?? 'Daily') === 'Fixed';
                        });

                        foreach ($fixed_rate_employees as $emp):
                            $emp_name = $emp['emp_name'];
                            $rate_type = $emp['rate_type'] ?? 'Daily';
                            $basic_salary = $emp['rate_amount'] ?? 0; // For Fixed, rate_amount is Basic Salary
                            $paid_amount = 0; // Assuming no payments recorded for Fixed employees

                            // Calculate ETF, EPF (Employee), EPF (Company)
                            $ETF_employee = $basic_salary * 0.03;
                            $EPF_employee = $basic_salary * 0.08;
                            $EPF_company = $basic_salary * 0.12;

                            // Calculate Total Payable and Net Payable
                            $total_payable = $basic_salary + $EPF_company;
                            $net_payable = $total_payable - $ETF_employee - $EPF_employee - $paid_amount;

                            // Collapsible details
                            $employeeDetails = "<ul>";
                            $employeeDetails .= "<li>Employee ID: " . htmlspecialchars($emp['emp_id'], ENT_QUOTES, 'UTF-8') . "</li>";
                            $employeeDetails .= "<li>Name: " . htmlspecialchars($emp_name, ENT_QUOTES, 'UTF-8') . "</li>";
                            $employeeDetails .= "<li>Rate Type: " . htmlspecialchars($rate_type, ENT_QUOTES, 'UTF-8') . "</li>";
                            $employeeDetails .= "</ul>";

                            $attendanceDetails = "<ul>";
                            $attendanceDetails .= "<li>No attendance recorded (Fixed Rate Employee)</li>";
                            $attendanceDetails .= "</ul>";
                        ?>
                            <tr>
                                <td>
                                    <div class="collapsible">
                                        <span class="total"><?php echo htmlspecialchars($emp_name, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $employeeDetails; ?></div>
                                </td>
                                <td><?php echo number_format($basic_salary, 2); ?></td>
                                <td><?php echo number_format($ETF_employee, 2); ?></td>
                                <td><?php echo number_format($EPF_employee, 2); ?></td>
                                <td><?php echo number_format($EPF_company, 2); ?></td>
                                <td><?php echo number_format($total_payable, 2); ?></td>
                                <td><?php echo number_format($paid_amount, 2); ?></td>
                                <td><?php echo number_format($net_payable, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($fixed_rate_employees)): ?>
                            <tr>
                                <td colspan="8">No fixed rate employees found.</td>
                            </tr>
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
                        <?php foreach ($labor_wages_data['summations'] as $sum): ?>
                            <?php
                            $laborDetails = "<ul>";
                            foreach ($labor_wages_data['details'][$sum['labor_name']] ?? [] as $detail) {
                                $job_id = htmlspecialchars($detail['job_id'], ENT_QUOTES, 'UTF-8');
                                $date = htmlspecialchars($detail['expensed_date'], ENT_QUOTES, 'UTF-8');
                                $description = htmlspecialchars($detail['description'], ENT_QUOTES, 'UTF-8');
                                $amount = number_format($detail['expense_amount'], 2);
                                $laborDetails .= "<li>Job ID: $job_id, Date: $date, Description: $description, Amount: $amount</li>";
                            }
                            $laborDetails .= "</ul>";
                            ?>
                            <tr>
                                <td>
                                    <div class="collapsible">
                                        <span class="total"><?php echo htmlspecialchars($sum['labor_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $laborDetails; ?></div>
                                </td>
                                <td><?php echo number_format($sum['total_days'], 0); ?></td>
                                <td><?php echo number_format($sum['total_amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($labor_wages_data['summations'])): ?>
                            <tr>
                                <td colspan="3">No labor wages summations found for the selected filters.</td>
                            </tr>
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

            const ctx = document.getElementById('financialChart').getContext('2d');
            const financialData = [
                <?php echo isset($total_wages) ? $total_wages : 0; ?>,
                <?php echo isset($total_daily_wages) ? $total_daily_wages : 0; ?>,
                <?php echo isset($epf_costs) ? $epf_costs : 0; ?>,
                <?php echo isset($avg_wage_per_employee) ? $avg_wage_per_employee : 0; ?>
            ];
            if (financialData.every(val => val === 0)) {
                document.getElementById('financialChart').parentElement.innerHTML = '<p>No financial data available.</p>';
            } else {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Total Wages', 'Daily Wages', 'EPF Costs', 'Average Wage'],
                        datasets: [{
                            label: 'Financial Metrics',
                            data: financialData,
                            backgroundColor: [
                                'rgba(30, 144, 255, 0.6)', 'rgba(16, 185, 129, 0.6)', 
                                'rgba(255, 107, 0, 0.6)', 'rgba(0, 196, 180, 0.6)'
                            ],
                            borderColor: [
                                'rgba(30, 144, 255, 1)', 'rgba(16, 185, 129, 1)', 
                                'rgba(255, 107, 0, 1)', 'rgba(0, 196, 180, 1)'
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