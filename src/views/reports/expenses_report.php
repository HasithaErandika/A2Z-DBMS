<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
}

// Fallbacks
$filters = $filters ?? ['year' => '', 'month' => ''];
$report_title = $report_title ?? 'Full Company Expense Report (All Time)';
$total_invoices = floatval($total_invoices ?? 0);
$total_expenses = floatval($total_expenses ?? 0);
$total_employee_costs = floatval($total_employee_costs ?? 0);
$total_invoices_count = intval($total_invoices_count ?? 0);
$total_jobs = intval($total_jobs ?? 0);
$total_job_capacity = floatval($total_job_capacity ?? 0);
$profit = floatval($profit ?? 0);
$expenses_by_category = $expenses_by_category ?? [];
$employee_costs_by_type = $employee_costs_by_type ?? ['Attendance-Based' => 0, 'Hiring of Labor' => 0];
$error = $error ?? null;
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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-poppins bg-gray-50 text-gray-900 leading-relaxed overflow-x-hidden">
    <div class="container mx-auto p-6 min-h-screen bg-[url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120"%3E%3Cg fill="%231E3A8A" fill-opacity="0.03"%3E%3Cpath d="M60 20 L80 60 L60 100 L40 60 Z"/%3E%3C/g%3E%3C/svg%3E')] bg-[length:240px]">
        <div class="header bg-gradient-to-br from-blue-900 to-blue-500 p-8 rounded-xl shadow-2xl flex items-center justify-between mb-10 text-white relative overflow-hidden hover:after:opacity-20 after:content-[''] after:absolute after:top-[-50%] after:left-[-50%] after:w-[200%] after:h-[200%] after:bg-white/10 after:rotate-30 after:transition-all after:duration-500 after:opacity-0 after:z-0">
            <h1 class="text-3xl font-semibold z-10">Expense Report</h1>
            <div class="header-controls flex gap-3 z-10">
                <button class="btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30" onclick="window.print()"><i class="ri-printer-line"></i> Print</button>
                <button class="btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30" onclick="window.location.href='<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>'"><i class="ri-arrow-left-line"></i> Go Back</button>
                <button class="btn bg-transparent text-blue-900 border-2 border-blue-900 px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:bg-blue-900 hover:text-white hover:translate-y-[-2px]" onclick="downloadCSV()"><i class="ri-download-line"></i> Download CSV</button>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="error-message bg-red-500 text-white p-4 rounded-xl mb-5 text-center"><?php echo htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>

            <div class="filter-card bg-white p-6 rounded-xl shadow-lg mb-10 transition-all duration-300 hover:shadow-2xl">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Report Filters</h2>
                <form method="POST" action="<?php echo htmlspecialchars(FULL_BASE_URL . '/reports/expenses_report', ENT_QUOTES, 'UTF-8'); ?>" class="filter-form grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-4 items-end" id="filterForm">
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Select Year</label>
                        <select name="year" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                            <option value="">All Years</option>
                            <option value="2024" <?php echo $filters['year'] === '2024' ? 'selected' : ''; ?>>2024</option>
                            <option value="2025" <?php echo $filters['year'] === '2025' ? 'selected' : ''; ?>>2025</option>
                            <option value="2026" <?php echo $filters['year'] === '2026' ? 'selected' : ''; ?>>2026</option>
                        </select>
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Select Month</label>
                        <select name="month" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                            <option value="">All Months</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php echo $m; ?>" <?php echo $filters['month'] == $m ? 'selected' : ''; ?>>
                                    <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30"><i class="ri-filter-line"></i> Filter</button>
                </form>
            </div>

            <div class="summary-card bg-white p-6 rounded-xl shadow-lg mb-10 transition-all duration-300 hover:shadow-2xl">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Financial Summary</h2>
                <div class="summary-grid grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-6">
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-blue-500">
                        <h3 class="text-base text-gray-500 mb-2">Total Revenue</h3>
                        <p class="text-lg font-semibold text-gray-900">Rs <?php echo number_format($total_invoices, 2); ?></p>
                        <small class="text-xs text-gray-500"><?php echo $total_invoices_count; ?> Invoices</small>
                    </div>
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-red-500">
                        <h3 class="text-base text-gray-500 mb-2">Total Expenses</h3>
                        <p class="text-lg font-semibold text-gray-900">Rs <?php echo number_format($total_expenses + $total_employee_costs, 2); ?></p>
                        <small class="text-xs text-gray-500">Operational + Labor</small>
                    </div>
                    <div class="summary-item <?php echo $profit >= 0 ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500'; ?> bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg">
                        <h3 class="text-base text-gray-500 mb-2">Net Profit</h3>
                        <p class="text-lg font-semibold text-gray-900">Rs <?php echo number_format($profit, 2); ?></p>
                        <small class="text-xs text-gray-500"><?php echo $profit >= 0 ? 'Profit' : 'Loss'; ?></small>
                    </div>
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-teal-500">
                        <h3 class="text-base text-gray-500 mb-2">Active Jobs</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo $total_jobs; ?></p>
                        <small class="text-xs text-gray-500"><?php echo number_format($total_job_capacity, 2); ?> Capacity</small>
                    </div>
                </div>
            </div>

            <div class="charts-container flex flex-row gap-6 mb-10 flex-wrap">
                <div class="chart-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl flex-1 min-w-[300px]">
                    <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Revenue vs Total Costs</h2>
                    <div class="chart-container max-w-full h-[300px]">
                        <canvas id="revenueVsCostChart"></canvas>
                    </div>
                </div>
                <div class="chart-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl flex-1 min-w-[300px]">
                    <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Expense Breakdown by Category</h2>
                    <div class="chart-container max-w-full h-[300px]">
                        <canvas id="expenseBreakdownChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="table-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl mb-10 overflow-x-auto">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Operational Expenses by Category</h2>
                <?php if (empty($expenses_by_category) && !isset($expenses_by_category['EPF'])): ?>
                    <p class="text-center text-gray-500 py-10">No operational expenses recorded for this period.</p>
                <?php else: ?>
                    <table class="table w-full border-collapse text-base">
                        <thead>
                            <tr>
                                <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Category</th>
                                <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Amount (Rs)</th>
                                <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalAllExpenses = $total_expenses + $total_employee_costs;
                            foreach ($expenses_by_category as $cat => $amt): 
                                $percentage = $totalAllExpenses > 0 ? ($amt / $totalAllExpenses) * 100 : 0;
                            ?>
                                <tr>
                                    <td class="p-3 border-b border-gray-200"><?php echo htmlspecialchars($cat); ?></td>
                                    <td class="p-3 border-b border-gray-200 text-right font-medium"><?php echo number_format($amt, 2); ?></td>
                                    <td class="p-3 border-b border-gray-200 text-right"><?php echo number_format($percentage, 1); ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="bg-blue-50">
                                <td class="p-3 font-semibold">Total Operational Expenses</td>
                                <td class="p-3 text-right font-semibold">Rs <?php echo number_format($total_expenses, 2); ?></td>
                                <td class="p-3 text-right font-semibold">100%</td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="table-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl overflow-x-auto">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Employee & Labor Costs</h2>
                <table class="table w-full border-collapse text-base">
                    <thead>
                        <tr>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Cost Type</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Amount (Rs)</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach ($employee_costs_by_type as $type => $amt): 
                            $percentage = $total_employee_costs > 0 ? ($amt / $total_employee_costs) * 100 : 0;
                        ?>
                            <tr>
                                <td class="p-3 border-b border-gray-200"><?php echo htmlspecialchars($type); ?></td>
                                <td class="p-3 border-b border-gray-200 text-right font-medium"><?php echo number_format($amt, 2); ?></td>
                                <td class="p-3 border-b border-gray-200 text-right"><?php echo number_format($percentage, 1); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (isset($expenses_by_category['EPF']) && $expenses_by_category['EPF'] > 0): ?>
                            <tr>
                                <td class="p-3 border-b border-gray-200">EPF Contribution</td>
                                <td class="p-3 border-b border-gray-200 text-right font-medium"><?php echo number_format($expenses_by_category['EPF'], 2); ?></td>
                                <td class="p-3 border-b border-gray-200 text-right"><?php echo $total_employee_costs > 0 ? number_format(($expenses_by_category['EPF'] / $total_employee_costs) * 100, 1) : '0.0'; ?>%</td>
                            </tr>
                        <?php endif; ?>
                        <tr class="bg-green-50">
                            <td class="p-3 font-semibold">Total Employee & Labor Costs</td>
                            <td class="p-3 text-right font-semibold">Rs <?php echo number_format($total_employee_costs, 2); ?></td>
                            <td class="p-3 text-right font-semibold">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Revenue vs Costs Bar Chart
            new Chart(document.getElementById('revenueVsCostChart'), {
                type: 'bar',
                data: {
                    labels: ['Revenue', 'Operational Exp.', 'Employee Costs', 'Net Profit'],
                    datasets: [{
                        label: 'Amount (Rs)',
                        data: [
                            <?php echo $total_invoices; ?>,
                            <?php echo $total_expenses; ?>,
                            <?php echo $total_employee_costs; ?>,
                            <?php echo $profit; ?>
                        ],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(249, 115, 22, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            <?php echo $profit >= 0 ? "'rgba(16, 185, 129, 0.8)'" : "'rgba(239, 68, 68, 0.9)'"; ?>
                        ],
                        borderRadius: 8,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => 'Rs ' + ctx.parsed.y.toLocaleString() } }
                    },
                    scales: { y: { beginAtZero: true, ticks: { callback: value => 'Rs ' + value.toLocaleString() } } }
                }
            });

            // Expense Breakdown Doughnut Chart
            const allCategories = <?php echo json_encode(array_keys($expenses_by_category)); ?>;
            const allAmounts = <?php echo json_encode(array_values($expenses_by_category)); ?>;
            const colors = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899', '#6366f1'];

            new Chart(document.getElementById('expenseBreakdownChart'), {
                type: 'doughnut',
                data: {
                    labels: allCategories.length ? allCategories : ['No Expenses'],
                    datasets: [{
                        data: allAmounts.length ? allAmounts : [1],
                        backgroundColor: colors,
                        borderColor: '#fff',
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 20, font: { size: 14 } } },
                        tooltip: { callbacks: { label: ctx => ctx.label + ': Rs ' + Number(ctx.parsed).toLocaleString() } }
                    }
                }
            });
        });

        function downloadCSV() {
            const form = document.getElementById('filterForm');
            const url = new URL(form.action);
            const data = new FormData(form);
            data.append('download_csv', '1');
            for (let [k, v] of data.entries()) url.searchParams.append(k, v);
            window.location.href = url.toString();
        }
    </script>
</body>
</html>