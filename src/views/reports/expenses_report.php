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
    <?php require_once __DIR__ . '/../partials/theme.php'; ?>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased overflow-x-hidden min-h-screen">

    <?php 
    $activePage = 'reports';
    $headerTitle = 'Expenses & Revenue Audit';
    $headerSubtitle = 'Comprehensive profit analysis comparing client invoices against operational expenses and labor costs.';
    $breadcrumb = 'Reports / Expenses';
    require_once __DIR__ . '/../partials/sidebar.php';
    ?>

    <!-- Main Container -->
    <div class="ml-64 transition-all duration-300 min-h-screen flex flex-col justify-between" id="container">
        <div>
            <?php require_once __DIR__ . '/../partials/header.php'; ?>

            <main class="p-8 animate-fadeIn">
                <!-- Action Controls -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="text-xs text-slate-500 font-semibold"><?php echo htmlspecialchars($report_title); ?></div>
                    <div class="flex items-center gap-2">
                        <a href="<?php echo htmlspecialchars(BASE_PATH . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-arrow-left"></i> Back to Reports
                        </a>
                        <button onclick="window.print()" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button onclick="downloadCSV()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </button>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center space-x-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php else: ?>

                    <!-- Filters Card -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-8">
                        <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Report Filters</h3>
                        <form method="POST" action="<?php echo htmlspecialchars(BASE_PATH . '/reports/expenses_report', ENT_QUOTES, 'UTF-8'); ?>" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end" id="filterForm">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Year</label>
                                <select name="year" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                                    <option value="">All Years</option>
                                    <option value="2024" <?php echo $filters['year'] === '2024' ? 'selected' : ''; ?>>2024</option>
                                    <option value="2025" <?php echo $filters['year'] === '2025' ? 'selected' : ''; ?>>2025</option>
                                    <option value="2026" <?php echo $filters['year'] === '2026' ? 'selected' : ''; ?>>2026</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Month</label>
                                <select name="month" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                                    <option value="">All Months</option>
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo $m; ?>" <?php echo $filters['month'] == $m ? 'selected' : ''; ?>>
                                            <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]">
                                <i class="fas fa-filter"></i> Apply Filter
                            </button>
                        </form>
                    </div>

                    <!-- Financial Summary Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Revenue</h4>
                            <p class="text-xl font-extrabold text-slate-900">Rs <?php echo number_format($total_invoices, 2); ?></p>
                            <small class="text-[11px] text-slate-500 mt-1 block"><?php echo $total_invoices_count; ?> Invoices</small>
                        </div>
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Expenses</h4>
                            <p class="text-xl font-extrabold text-slate-900">Rs <?php echo number_format($total_expenses + $total_employee_costs, 2); ?></p>
                            <small class="text-[11px] text-slate-500 mt-1 block">Operational + Labor</small>
                        </div>
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Net Profit</h4>
                            <p class="text-xl font-extrabold <?php echo $profit >= 0 ? 'text-emerald-600' : 'text-red-500'; ?>">Rs <?php echo number_format($profit, 2); ?></p>
                            <small class="text-[11px] text-slate-500 mt-1 block"><?php echo $profit >= 0 ? 'Profit Margin' : 'Financial Deficit'; ?></small>
                        </div>
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Active Jobs</h4>
                            <p class="text-xl font-extrabold text-slate-900"><?php echo $total_jobs; ?></p>
                            <small class="text-[11px] text-slate-500 mt-1 block"><?php echo number_format($total_job_capacity, 1); ?>kW Cumulative Capacity</small>
                        </div>
                    </div>

                    <!-- Charts Container -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Revenue vs Total Costs</h3>
                            <div class="h-[260px] flex items-center justify-center">
                                <canvas id="revenueVsCostChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Expense Breakdown by Category</h3>
                            <div class="h-[260px] flex items-center justify-center">
                                <canvas id="expenseBreakdownChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Operational Expenses Table -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-8 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 bg-slate-50">
                            <h3 class="text-sm font-bold text-slate-900">Operational Expenses by Category</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <?php if (empty($expenses_by_category) && !isset($expenses_by_category['EPF'])): ?>
                                <p class="text-center text-slate-500 py-10 text-xs">No operational expenses recorded for this period.</p>
                            <?php else: ?>
                                <table class="w-full border-collapse text-xs text-left">
                                    <thead>
                                        <tr class="bg-slate-100 border-b border-slate-200 text-slate-700">
                                            <th class="p-4 font-bold">Category</th>
                                            <th class="p-4 font-bold text-right">Amount (Rs)</th>
                                            <th class="p-4 font-bold text-right">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php 
                                        $totalAllExpenses = $total_expenses + $total_employee_costs;
                                        foreach ($expenses_by_category as $cat => $amt): 
                                            $percentage = $totalAllExpenses > 0 ? ($amt / $totalAllExpenses) * 100 : 0;
                                        ?>
                                            <tr class="hover:bg-slate-50/55 transition-colors">
                                                <td class="p-4 font-medium text-slate-900"><?php echo htmlspecialchars($cat); ?></td>
                                                <td class="p-4 text-right font-medium text-slate-800">Rs <?php echo number_format($amt, 2); ?></td>
                                                <td class="p-4 text-right text-slate-500"><?php echo number_format($percentage, 1); ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr class="bg-slate-50 font-bold border-t border-slate-200">
                                            <td class="p-4 text-slate-800">Total Operational Expenses</td>
                                            <td class="p-4 text-right text-slate-900 font-bold">Rs <?php echo number_format($total_expenses, 2); ?></td>
                                            <td class="p-4 text-right text-slate-600">100%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Employee & Labor Costs Table -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-8 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 bg-slate-50">
                            <h3 class="text-sm font-bold text-slate-900">Employee & Labor Costs</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse text-xs text-left">
                                <thead>
                                    <tr class="bg-slate-100 border-b border-slate-200 text-slate-700">
                                        <th class="p-4 font-bold">Cost Type</th>
                                        <th class="p-4 font-bold text-right">Amount (Rs)</th>
                                        <th class="p-4 font-bold text-right">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php 
                                    foreach ($employee_costs_by_type as $type => $amt): 
                                        $percentage = $total_employee_costs > 0 ? ($amt / $total_employee_costs) * 100 : 0;
                                    ?>
                                        <tr class="hover:bg-slate-50/55 transition-colors">
                                            <td class="p-4 font-medium text-slate-900"><?php echo htmlspecialchars($type); ?></td>
                                            <td class="p-4 text-right font-medium text-slate-800">Rs <?php echo number_format($amt, 2); ?></td>
                                            <td class="p-4 text-right text-slate-500"><?php echo number_format($percentage, 1); ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (isset($expenses_by_category['EPF']) && $expenses_by_category['EPF'] > 0): ?>
                                        <tr class="hover:bg-slate-50/55 transition-colors">
                                            <td class="p-4 font-medium text-slate-900">EPF Contribution</td>
                                            <td class="p-4 text-right font-medium text-slate-800">Rs <?php echo number_format($expenses_by_category['EPF'], 2); ?></td>
                                            <td class="p-4 text-right text-slate-500"><?php echo $total_employee_costs > 0 ? number_format(($expenses_by_category['EPF'] / $total_employee_costs) * 100, 1) : '0.0'; ?>%</td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="bg-slate-50 font-bold border-t border-slate-200">
                                        <td class="p-4 text-slate-800">Total Employee & Labor Costs</td>
                                        <td class="p-4 text-right text-slate-900 font-bold">Rs <?php echo number_format($total_employee_costs, 2); ?></td>
                                        <td class="p-4 text-right text-slate-600">100%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php endif; ?>
            </main>
        </div>

        <!-- Corporate Footer -->
        <footer class="border-t border-slate-200 bg-white py-6 px-8 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center space-x-2">
                <img class="h-6 w-auto opacity-70" src="<?php echo BASE_PATH; ?>/src/assets/images/logo.png" alt="A2Z Logo">
                <span>&copy; <?php echo date('Y'); ?> A2Z Engineering. Internal DBMS Portal.</span>
            </div>
            <div class="flex space-x-6">
                <a href="https://a2zengineering.lk" target="_blank" rel="noopener noreferrer" class="hover:text-slate-900 transition-colors">Corporate Site</a>
                <span class="text-slate-300">|</span>
                <span>DBMS Stable v2.2.0</span>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const container = document.getElementById('container');
            sidebar.classList.toggle('hidden');
            if (window.innerWidth >= 1024) {
                container.classList.toggle('ml-0');
                container.classList.toggle('ml-64');
            }
        }

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
                            '#3b82f6',
                            '#f59e0b',
                            '#ef4444',
                            <?php echo $profit >= 0 ? "'#059669'" : "'#ef4444'"; ?>
                        ],
                        borderRadius: 6,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => 'Rs ' + ctx.parsed.y.toLocaleString() } }
                    },
                    scales: { y: { beginAtZero: true, grid: { borderDash: [4, 4] }, ticks: { callback: value => 'Rs ' + value.toLocaleString() } } }
                }
            });

            // Expense Breakdown Doughnut Chart
            const allCategories = <?php echo json_encode(array_keys($expenses_by_category)); ?>;
            const allAmounts = <?php echo json_encode(array_values($expenses_by_category)); ?>;
            const colors = ['#3b82f6', '#8b5cf6', '#059669', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899', '#6366f1'];

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
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
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