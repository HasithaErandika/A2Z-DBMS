<!-- views/reports/cost_calculation.php -->
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: #167bd6;
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
        }

        .btn-secondary:hover {
            background: #00a896;
        }

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

        .filter-item input:focus, .filter-item select:focus {
            border-color: var(--accent);
        }

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

        .table tr:hover {
            background: var(--hover-bg);
        }

        .collapsible {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .collapsible .total {
            font-weight: 600;
        }

        .collapsible .icon {
            font-size: 12px;
            transition: transform 0.2s;
        }

        .collapsible.active .icon {
            transform: rotate(180deg);
        }

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

        .profit-positive { color: var(--success); font-weight: 600; }
        .profit-negative { color: var(--danger); font-weight: 600; }

        .chart-container {
            max-width: 600px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .filter-form { grid-template-columns: 1fr; }
            .summary-grid { grid-template-columns: 1fr; }
            .table { font-size: 12px; }
            .chart-container { max-width: 100%; }
        }
        .details ul ul { margin-left: 20px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cost Calculation Report</h1>
            <div class="header-controls">
                <button class="btn btn-primary"><i class="ri-printer-line"></i> Print</button>
                <button class="btn btn-primary" onclick="window.location.href='<?php echo BASE_PATH; ?>/admin/tables'"><i class="ri-arrow-left-line"></i> Go Back</button>
                <button class="btn btn-secondary" onclick="downloadCSV()"><i class="ri-download-line"></i> Download CSV</button>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php else: ?>
            <div class="filter-card">
                <form method="GET" class="filter-form" id="filterForm" action="<?php echo BASE_PATH; ?>/reports/cost_calculation">
                    <div class="filter-item">
                        <label>Invoice ID</label>
                        <input type="text" name="invoice_id" value="<?php echo htmlspecialchars($filters['invoice_no']); ?>">
                    </div>
                    <div class="filter-item">
                        <label>Customer</label>
                        <select name="customer_name">
                            <option value="">All</option>
                            <?php foreach ($customer_refs as $ref): ?>
                                <option value="<?php echo htmlspecialchars($ref); ?>" <?php echo $filters['customer_reference'] === $ref ? 'selected' : ''; ?>><?php echo htmlspecialchars($ref); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-item">
                        <label>Company Ref</label>
                        <select name="client_ref">
                            <option value="">All</option>
                            <?php foreach ($company_refs as $ref): ?>
                                <option value="<?php echo htmlspecialchars($ref); ?>" <?php echo $filters['company_reference'] === $ref ? 'selected' : ''; ?>><?php echo htmlspecialchars($ref); ?></option>
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
                        <input type="date" name="from_date" value="<?php echo htmlspecialchars($filters['from_date']); ?>">
                    </div>
                    <div class="filter-item">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="<?php echo htmlspecialchars($filters['to_date']); ?>">
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
                    <div class="summary-item"><h3>Unpaid Count</h3><p><?php echo $unpaid_invoice_count; ?></p></div>
                    <div class="summary-item unpaid"><h3>Due Balance</h3><p><?php echo number_format($due_balance, 2); ?></p></div>
                </div>
                <div class="section">
                    <h2>Additional Metrics</h2>
                    <div class="summary-grid">
                        <div class="summary-item"><h3>Total Expenses</h3><p><?php echo number_format($total_expenses, 2); ?></p></div>
                        <div class="summary-item"><h3>Total Employee Costs</h3><p><?php echo number_format($total_employee_costs_sum, 2); ?></p></div>
                        <div class="summary-item"><h3>Total Jobs</h3><p><?php echo count($job_data); ?></p></div>
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
                        <div class="summary-item"><h3>Unpaid Count</h3><p><?php echo $overall_unpaid_count; ?></p></div>
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
                            $jobDetails .= "<li>Job ID: " . htmlspecialchars($row['job_id']) . "</li>";
                            $jobDetails .= "<li>Location: " . htmlspecialchars($row['location']) . "</li>";
                            $jobDetails .= "<li>Company Ref: " . htmlspecialchars($row['company_reference'] ?? 'N/A') . "</li>";
                            $jobDetails .= "<li>Engineer: " . htmlspecialchars($row['engineer'] ?? 'N/A') . "</li>";
                            $jobDetails .= "</ul>";

                            $invoiceDetails = "<ul>";
                            $invoiceDetails .= "<li>No: " . htmlspecialchars($row['invoice_no']) . "</li>";
                            $invoiceDetails .= "<li>Value: " . number_format($row['invoice_value'], 2) . "</li>";
                            $invoiceDetails .= "<li>Received: " . number_format($row['received_amount'], 2) . "</li>";
                            $invoiceDetails .= "<li>Date Paid: " . htmlspecialchars($row['payment_received_date']) . "</li>";
                            $invoiceDetails .= "</ul>";

                            $expenseDetails = "<ul>";
                            if (!empty($row['expense_details'])) {
                                foreach ($row['expense_details'] as $category => $amount) {
                                    $expenseDetails .= "<li>" . htmlspecialchars($category) . ": " . number_format($amount, 2) . "</li>";
                                }
                            } else {
                                $expenseDetails .= "<li>No expenses</li>";
                            }
                            $expenseDetails .= "</ul>";

                            $employeeDetails = "<ul>";
                            foreach ($row['employee_details'] as $emp) {
                                $employeeDetails .= "<li>" . htmlspecialchars($emp['emp_name']) . ": " . number_format($emp['payment'], 2);
                                if (!empty($emp['days'])) {
                                    $employeeDetails .= "<ul>";
                                    foreach ($emp['days'] as $day) {
                                        $presenceText = $day['presence'] == 1.0 ? 'Full Day' : 'Half Day';
                                        $employeeDetails .= "<li>{$day['date']}: {$presenceText}</li>";
                                    }
                                    $employeeDetails .= "</ul>";
                                }
                                $employeeDetails .= "</li>";
                            }
                            $employeeDetails .= empty($row['employee_details']) ? "<li>No employee costs</li></ul>" : "</ul>";

                            $outstanding = floatval($row['invoice_value']) - floatval($row['received_amount']);
                            ?>
                            <tr>
                                <td>
                                    <div class="collapsible">
                                        <span class="total">Customer: <?php echo htmlspecialchars($row['customer_reference']); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $jobDetails; ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($row['date_completed']); ?></td>
                                <td><?php echo htmlspecialchars($row['job_capacity']); ?></td>
                                <td>
                                    <div class="collapsible">
                                        <span class="total"><?php echo htmlspecialchars($row['invoice_no']); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $invoiceDetails; ?></div>
                                </td>
                                <td>
                                    <div class="collapsible">
                                        <span class="total">Total: <?php echo number_format($row['operational_expenses'], 2); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $expenseDetails; ?></div>
                                </td>
                                <td>
                                    <div class="collapsible">
                                        <span class="total">Total: <?php echo number_format($row['total_employee_costs'], 2); ?></span>
                                        <i class="ri-arrow-down-s-line icon"></i>
                                    </div>
                                    <div class="details"><?php echo $employeeDetails; ?></div>
                                </td>
                                <td><?php echo number_format($outstanding, 2); ?></td>
                                <td class="<?php echo $row['net_profit'] >= 0 ? 'profit-positive' : 'profit-negative'; ?>">
                                    <?php echo number_format($row['net_profit'], 2); ?>
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