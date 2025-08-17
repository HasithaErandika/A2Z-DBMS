<!-- views/reports/expenses_report.php -->
<?php
// Define BASE_PATH and FULL_BASE_URL for consistency with other files
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
}

// Fallback for missing data from AdminController::expenseReport()
if (!isset($filters) || !is_array($filters)) {
    $filters = ['year' => '', 'month' => ''];
}
if (!isset($report_title)) {
    $report_title = 'Full Company Expense Report (All Time)';
}
if (!isset($total_invoices)) $total_invoices = 0;
if (!isset($total_expenses)) $total_expenses = 0;
if (!isset($total_employee_costs)) $total_employee_costs = 0;
if (!isset($total_invoices_count)) $total_invoices_count = 0;
if (!isset($total_jobs)) $total_jobs = 0;
if (!isset($total_job_capacity)) $total_job_capacity = 0;
if (!isset($profit)) $profit = 0;
if (!isset($expenses_by_category) || !is_array($expenses_by_category)) {
    $expenses_by_category = [];
}
if (!isset($employee_costs_by_type) || !is_array($employee_costs_by_type)) {
    $employee_costs_by_type = [];
}
if (!isset($error)) $error = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - Expense Report</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(FULL_BASE_URL . '/css/expenses_report.css', ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Expense Report - A2Z Engineering</h1>
            <div class="header-controls">
                <button class="btn btn-primary" onclick="window.print()"><i class="ri-printer-line"></i> Print</button>
                <button class="btn btn-primary" onclick="window.location.href='<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/tables', ENT_QUOTES, 'UTF-8'); ?>'"><i class="ri-arrow-left-line"></i> Go Back</button>
                <button class="btn btn-secondary" onclick="downloadCSV()"><i class="ri-download-line"></i> Download CSV</button>
            </div>
        </div>

        <div class="form-card">
            <form method="POST" class="form-group" id="filterForm" action="<?php echo htmlspecialchars(FULL_BASE_URL . '/reports/expenses_report', ENT_QUOTES, 'UTF-8'); ?>">
                <div class="form-item">
                    <label>Year</label>
                    <select name="year" required>
                        <option value="2024" <?php echo $filters['year'] === '2024' ? 'selected' : ''; ?>>2024</option>
                        <option value="2025" <?php echo $filters['year'] === '2025' ? 'selected' : ''; ?>>2025</option>
                    </select>
                </div>
                <div class="form-item">
                    <label>Month</label>
                    <select name="month" required>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo $filters['month'] == $m ? 'selected' : ''; ?>>
                                <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="ri-filter-line"></i> Filter</button>
            </form>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>
            <div class="report-card">
                <h2><?php echo htmlspecialchars($report_title, ENT_QUOTES, 'UTF-8'); ?></h2>
                <div class="summary-grid">
                    <div class="summary-item"><h3>Total Invoices</h3><p><?php echo number_format($total_invoices, 2); ?></p></div>
                    <div class="summary-item"><h3>Total Operational Expenses</h3><p><?php echo number_format($total_expenses, 2); ?></p></div>
                    <div class="summary-item"><h3>Total Employee Costs</h3><p><?php echo number_format($total_employee_costs, 2); ?></p></div>
                    <div class="summary-item"><h3>Invoice Count</h3><p><?php echo htmlspecialchars($total_invoices_count, ENT_QUOTES, 'UTF-8'); ?></p></div>
                    <div class="summary-item"><h3>Total Jobs</h3><p><?php echo htmlspecialchars($total_jobs, ENT_QUOTES, 'UTF-8'); ?></p></div>
                    <div class="summary-item"><h3>Total Job Capacity</h3><p><?php echo number_format($total_job_capacity, 2); ?></p></div>
                    <div class="summary-item <?php echo $profit >= 0 ? 'profit-positive' : 'profit-negative'; ?>">
                        <h3>Net Profit</h3><p><?php echo number_format($profit, 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <h2>Financial Overview</h2>
                <div class="chart-container">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>

            <div class="report-card">
                <h2>Operational Expenses by Category</h2>
                <table class="table">
                    <thead><tr><th>Category</th><th>Amount</th></tr></thead>
                    <tbody>
                        <?php foreach ($expenses_by_category as $category => $amount): ?>
                            <tr><td><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo number_format($amount, 2); ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="report-card">
                <h2>Employee Costs by Type</h2>
                <table class="table">
                    <thead><tr><th>Type</th><th>Amount</th></tr></thead>
                    <tbody>
                        <?php foreach ($employee_costs_by_type as $type => $amount): ?>
                            <tr><td><?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo number_format($amount, 2); ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const financialCtx = document.getElementById('financialChart').getContext('2d');
            new Chart(financialCtx, {
                type: 'bar',
                data: {
                    labels: ['Total Invoices', 'Operational Expenses', 'Employee Costs', 'Net Profit'],
                    datasets: [{
                        label: 'Financial Metrics',
                        data: [<?php echo "$total_invoices, $total_expenses, $total_employee_costs, $profit"; ?>],
                        backgroundColor: [
                            'rgba(30, 144, 255, 0.6)', 'rgba(255, 107, 0, 0.6)', 'rgba(0, 196, 180, 0.6)',
                            '<?php echo $profit >= 0 ? "rgba(16, 185, 129, 0.6)" : "rgba(239, 68, 68, 0.6)"; ?>'
                        ],
                        borderColor: [
                            'rgba(30, 144, 255, 1)', 'rgba(255, 107, 0, 1)', 'rgba(0, 196, 180, 1)',
                            '<?php echo $profit >= 0 ? "rgba(16, 185, 129, 1)" : "rgba(239, 68, 68, 1)"; ?>'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true } },
                    plugins: { legend: { display: false }, title: { display: true, text: 'Financial Overview' } }
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