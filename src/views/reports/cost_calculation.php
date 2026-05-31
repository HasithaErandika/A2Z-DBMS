<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - Cost Calculation</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php require_once __DIR__ . '/../partials/theme.php'; ?>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased overflow-x-hidden min-h-screen">

    <?php 
    $activePage = 'reports';
    $headerTitle = 'Site Cost Calculation';
    $headerSubtitle = 'Comprehensive breakdown of profit, material cost, labor costs, and payments per project site.';
    $breadcrumb = 'Reports / Site Cost';
    require_once __DIR__ . '/../partials/sidebar.php';
    ?>

    <!-- Main Container -->
    <div class="ml-64 transition-all duration-300 min-h-screen flex flex-col justify-between" id="container">
        <div>
            <?php require_once __DIR__ . '/../partials/header.php'; ?>

            <main class="p-8 animate-fadeIn">
                <!-- Action Controls -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="text-xs text-slate-500 font-semibold">Report Generated on: <?php echo date('d M Y'); ?></div>
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

                <?php if (isset($error)): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center space-x-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php else: ?>

                    <!-- Filters Card -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-8">
                        <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Report Filters</h3>
                        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-4 items-end" id="filterForm" action="<?php echo htmlspecialchars(BASE_PATH . '/reports/cost_calculation', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Invoice ID</label>
                                <input type="text" name="invoice_id" value="<?php echo htmlspecialchars($filters['invoice_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Customer</label>
                                <select name="customer_name" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                                    <option value="">All</option>
                                    <?php foreach ($customer_refs as $ref): ?>
                                        <option value="<?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php echo $filters['customer_reference'] === $ref ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Company Ref</label>
                                <select name="client_ref" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                                    <option value="">All</option>
                                    <?php foreach ($company_refs as $ref): ?>
                                        <option value="<?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php echo $filters['company_reference'] === $ref ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Payment Status</label>
                                <select name="status" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                                    <option value="">All</option>
                                    <option value="paid" <?php echo $filters['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                    <option value="unpaid" <?php echo $filters['status'] === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Completion</label>
                                <select name="completion" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                                    <option value="">All</option>
                                    <option value="Completed" <?php echo $filters['completion'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Ongoing" <?php echo $filters['completion'] === 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                    <option value="Started" <?php echo $filters['completion'] === 'Started' ? 'selected' : ''; ?>>Started</option>
                                    <option value="Not Started" <?php echo $filters['completion'] === 'Not Started' ? 'selected' : ''; ?>>Not Started</option>
                                    <option value="Cancelled" <?php echo $filters['completion'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">From Date</label>
                                <input type="date" name="from_date" value="<?php echo htmlspecialchars($filters['from_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="p-2 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">To Date</label>
                                <input type="date" name="to_date" value="<?php echo htmlspecialchars($filters['to_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="p-2 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                            </div>
                            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px] md:col-span-2">
                                <i class="fas fa-filter"></i> Apply Filter
                            </button>
                        </form>
                    </div>

                    <!-- Filtered Summary Cards -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-8">
                        <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Filtered Financial Metrics</h3>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Invoiced</h4>
                                <p class="text-sm font-extrabold text-slate-900">LKR <?php echo number_format($total_invoice_amount, 2); ?></p>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Received Payments</h4>
                                <p class="text-sm font-extrabold text-emerald-600">LKR <?php echo number_format($total_paid_amount, 2); ?></p>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Unpaid Amount</h4>
                                <p class="text-sm font-extrabold text-red-500">LKR <?php echo number_format($total_unpaid_amount, 2); ?></p>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Unpaid Invoices</h4>
                                <p class="text-sm font-extrabold text-slate-900"><?php echo htmlspecialchars($unpaid_invoice_count, ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Due Balance</h4>
                                <p class="text-sm font-extrabold text-red-500">LKR <?php echo number_format($due_balance, 2); ?></p>
                            </div>
                        </div>

                        <h3 class="text-xs font-bold text-slate-900 mb-4 mt-6 uppercase tracking-wider">Filtered Operating Costs & Profit</h3>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Material Expenses</h4>
                                <p class="text-sm font-extrabold text-slate-900">LKR <?php echo number_format($total_expenses, 2); ?></p>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Labor & EPF Costs</h4>
                                <p class="text-sm font-extrabold text-slate-900">LKR <?php echo number_format($total_employee_costs_sum, 2); ?></p>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Jobs Listed</h4>
                                <p class="text-sm font-extrabold text-slate-900"><?php echo htmlspecialchars(array_sum(array_map('count', $job_groups)), ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Capacity</h4>
                                <p class="text-sm font-extrabold text-slate-900"><?php echo number_format($total_capacity, 1); ?> kW</p>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl <?php echo $total_net_profit >= 0 ? 'bg-emerald-50/50 border-emerald-100' : 'bg-red-50/50 border-red-100'; ?>">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Net Margin (Profit)</h4>
                                <p class="text-sm font-extrabold <?php echo $total_net_profit >= 0 ? 'text-emerald-600' : 'text-red-500'; ?>">
                                    LKR <?php echo number_format($total_net_profit, 0); ?> (<?php echo number_format($profit_margin, 1); ?>%)
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Summary Metrics -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-8">
                        <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Overall Summary (Cumulative Lifetime Database Metrics)</h3>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Overall Invoices</h4>
                                <p class="text-sm font-extrabold text-slate-900">LKR <?php echo number_format($overall_invoice_amount, 2); ?></p>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Overall Paid</h4>
                                <p class="text-sm font-extrabold text-emerald-600">LKR <?php echo number_format($overall_paid_amount, 2); ?></p>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Overall Unpaid</h4>
                                <p class="text-sm font-extrabold text-red-500">LKR <?php echo number_format($overall_unpaid_amount, 2); ?></p>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Overall Unpaid Count</h4>
                                <p class="text-sm font-extrabold text-slate-900"><?php echo htmlspecialchars($overall_unpaid_count, ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Overall Balance Due</h4>
                                <p class="text-sm font-extrabold text-red-500">LKR <?php echo number_format($overall_due_balance, 2); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Container -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Filtered Financial Overview</h3>
                            <div class="h-[250px] flex items-center justify-center">
                                <canvas id="financialChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Job Status by Company</h3>
                            <div class="h-[250px] flex items-center justify-center">
                                <canvas id="jobStatusChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Net Profit by Company</h3>
                            <div class="h-[250px] flex items-center justify-center">
                                <canvas id="netProfitChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Job Analysis Status Sections -->
                    <div class="mb-8">
                        <h3 class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wider">Detailed Job Profitability Analysis</h3>
                        
                        <?php foreach ($job_groups as $status => $jobs): ?>
                            <?php if (!empty($jobs)): ?>
                                <div class="mb-5 bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                                    <div class="status-header cursor-pointer bg-slate-50 hover:bg-slate-100/70 p-4 border-b border-slate-200 flex justify-between items-center select-none transition-colors">
                                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full <?php 
                                                echo $status === 'Completed' ? 'bg-emerald-500' : 
                                                    ($status === 'Ongoing' ? 'bg-amber-500' : 
                                                    ($status === 'Cancelled' ? 'bg-rose-500' : 'bg-slate-400')); 
                                            ?>"></span>
                                            <?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?> Jobs (<?php echo count($jobs); ?>)
                                        </h4>
                                        <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-300"></i>
                                    </div>
                                    <div class="status-content hidden overflow-x-auto">
                                        <table class="w-full border-collapse text-xs text-left">
                                            <thead>
                                                <tr class="bg-slate-100 border-b border-slate-200 text-slate-700">
                                                    <th class="p-4 font-bold">Job Details</th>
                                                    <th class="p-4 font-bold">Completed Date</th>
                                                    <th class="p-4 font-bold">Capacity</th>
                                                    <th class="p-4 font-bold">Invoice Details</th>
                                                    <th class="p-4 font-bold">Expenses</th>
                                                    <th class="p-4 font-bold">Employee Costs</th>
                                                    <th class="p-4 font-bold">Total Cost</th>
                                                    <th class="p-4 font-bold">Total Invoiced</th>
                                                    <th class="p-4 font-bold">Outstanding</th>
                                                    <th class="p-4 font-bold">Net Profit</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                <?php $current_company = ''; ?>
                                                <?php foreach ($jobs as $row): ?>
                                                    <?php if ($row['company_reference'] !== $current_company): ?>
                                                        <tr class="bg-slate-50/50">
                                                            <td colspan="10" class="p-4 font-bold text-[10px] uppercase text-slate-550 border-y border-slate-200/60">Company: <?php echo htmlspecialchars($row['company_reference'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        </tr>
                                                        <?php $current_company = $row['company_reference']; ?>
                                                    <?php endif; ?>
                                                    <?php
                                                    $jobDetails = "<ul class='list-disc pl-5 space-y-1 text-slate-500'>";
                                                    $jobDetails .= "<li><strong>Job ID:</strong> " . htmlspecialchars($row['job_id'] ?? '', ENT_QUOTES, 'UTF-8') . "</li>";
                                                    $jobDetails .= "<li><strong>Location:</strong> " . htmlspecialchars($row['location'] ?? '', ENT_QUOTES, 'UTF-8') . "</li>";
                                                    $jobDetails .= "<li><strong>Company Ref:</strong> " . htmlspecialchars($row['company_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</li>";
                                                    $jobDetails .= "<li><strong>Engineer:</strong> " . htmlspecialchars($row['engineer'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</li>";
                                                    $jobDetails .= "</ul>";
                                                    $has_invoice = !empty($row['invoices']);
                                                    $invoiceDetails = "<ul class='list-disc pl-5 space-y-1 text-slate-500'>";
                                                    if ($has_invoice) {
                                                        foreach ($row['invoices'] as $inv) {
                                                            $invoiceDetails .= "<li>";
                                                            $invoiceDetails .= "<strong>No:</strong> " . htmlspecialchars($inv['no'], ENT_QUOTES, 'UTF-8') . "<br>";
                                                            $invoiceDetails .= "<strong>Val:</strong> LKR " . number_format($inv['value'], 2) . "<br>";
                                                            $invoiceDetails .= "<strong>Rec:</strong> LKR " . number_format($inv['received'], 2) . "<br>";
                                                            $invoiceDetails .= "<strong>Paid Date:</strong> " . htmlspecialchars($inv['date_paid'], ENT_QUOTES, 'UTF-8');
                                                            $invoiceDetails .= "</li>";
                                                        }
                                                    } else {
                                                        $invoiceDetails .= "<li>No Invoice</li>";
                                                    }
                                                    $invoiceDetails .= "</ul>";
                                                    $expenseDetails = "<ul class='list-disc pl-5 space-y-1 text-slate-500'>";
                                                    $totalExpensesForJob = floatval($row['operational_expenses'] ?? 0);
                                                    if (!empty($row['expense_details'])) {
                                                        foreach ($row['expense_details'] as $category => $amount) {
                                                            $expenseDetails .= "<li><strong>" . htmlspecialchars($category, ENT_QUOTES, 'UTF-8') . ":</strong> LKR " . number_format(floatval($amount), 2) . "</li>";
                                                        }
                                                    } else {
                                                        $expenseDetails .= "<li>No expenses recorded</li>";
                                                    }
                                                    $expenseDetails .= "</ul>";
                                                    $employeeDetails = "<ul class='list-disc pl-5 space-y-1 text-slate-500'>";
                                                    $totalEmployeeCostsForJob = floatval($row['total_employee_costs'] ?? 0);
                                                    if (!empty($row['employee_details']) && is_array($row['employee_details'])) {
                                                        foreach ($row['employee_details'] as $emp) {
                                                            $employeeDetails .= "<li><strong>" . htmlspecialchars($emp['emp_name'] ?? 'Unknown Employee', ENT_QUOTES, 'UTF-8') . ":</strong> LKR " . number_format(floatval($emp['payment'] ?? 0), 2);
                                                            if (!empty($emp['days']) && is_array($emp['days'])) {
                                                                $employeeDetails .= "<ul class='list-disc pl-5 space-y-1 mt-1'>";
                                                                foreach ($emp['days'] as $day) {
                                                                    $presence = floatval($day['presence'] ?? 0);
                                                                    $presenceText = $presence == 1.0 ? 'Full Day' : ($presence == 0.5 ? 'Half Day' : number_format($presence, 1) . ' Days');
                                                                    $employeeDetails .= "<li>";
                                                                    $employeeDetails .= "<strong>Date:</strong> " . htmlspecialchars($day['date'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "<br>";
                                                                    $employeeDetails .= "<strong>Pres:</strong> " . $presenceText . "<br>";
                                                                    $employeeDetails .= "<strong>Pay:</strong> LKR " . number_format(floatval($day['payment'] ?? 0), 2);
                                                                    $employeeDetails .= "</li>";
                                                                }
                                                                $employeeDetails .= "</ul>";
                                                            } else {
                                                                $employeeDetails .= " (No attendance recorded)";
                                                            }
                                                            $employeeDetails .= "</li>";
                                                        }
                                                    } else {
                                                        $employeeDetails .= "<li>No employee costs</li>";
                                                    }
                                                    $employeeDetails .= "</ul>";
                                                    $totalCost = $totalExpensesForJob + $totalEmployeeCostsForJob;
                                                    $outstanding = floatval($row['invoice_value'] ?? 0) - floatval($row['received_amount'] ?? 0);
                                                    $netProfit = floatval($row['net_profit'] ?? 0);
                                                    $displayDate = $row['date_completed'] === '0000-00-00' ? 'Not Set' : htmlspecialchars($row['date_completed'] ?? '', ENT_QUOTES, 'UTF-8');
                                                    ?>
                                                    <tr class="hover:bg-slate-50/55 transition-colors">
                                                        <td class="p-4 align-top">
                                                            <div class="collapsible cursor-pointer flex items-center gap-2 font-medium select-none text-slate-900">
                                                                <span>Customer: <?php echo htmlspecialchars($row['customer_reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                                <i class="fas fa-chevron-down text-[9px] text-slate-400 transition-transform duration-300"></i>
                                                            </div>
                                                            <div class="details hidden p-4 bg-slate-50/50 border border-slate-200/60 rounded-xl mt-3"><?php echo $jobDetails; ?></div>
                                                        </td>
                                                        <td class="p-4 align-top text-slate-650"><?php echo $displayDate; ?></td>
                                                        <td class="p-4 align-top font-medium text-slate-800"><?php echo htmlspecialchars($row['job_capacity'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="p-4 align-top">
                                                            <div class="collapsible cursor-pointer flex items-center gap-2 font-medium select-none text-slate-900">
                                                                <span><?php echo $has_invoice ? htmlspecialchars($row['invoice_no'], ENT_QUOTES, 'UTF-8') : 'No Invoice'; ?></span>
                                                                <i class="fas fa-chevron-down text-[9px] text-slate-400 transition-transform duration-300"></i>
                                                            </div>
                                                            <div class="details hidden p-4 bg-slate-50/50 border border-slate-200/60 rounded-xl mt-3"><?php echo $invoiceDetails; ?></div>
                                                        </td>
                                                        <td class="p-4 align-top">
                                                            <div class="collapsible cursor-pointer flex items-center gap-2 font-medium select-none text-slate-900">
                                                                <span>LKR <?php echo number_format($totalExpensesForJob, 2); ?></span>
                                                                <i class="fas fa-chevron-down text-[9px] text-slate-400 transition-transform duration-300"></i>
                                                            </div>
                                                            <div class="details hidden p-4 bg-slate-50/50 border border-slate-200/60 rounded-xl mt-3"><?php echo $expenseDetails; ?></div>
                                                        </td>
                                                        <td class="p-4 align-top">
                                                            <div class="collapsible cursor-pointer flex items-center gap-2 font-medium select-none text-slate-900">
                                                                <span>LKR <?php echo number_format($totalEmployeeCostsForJob, 2); ?></span>
                                                                <i class="fas fa-chevron-down text-[9px] text-slate-400 transition-transform duration-300"></i>
                                                            </div>
                                                            <div class="details hidden p-4 bg-slate-50/50 border border-slate-200/60 rounded-xl mt-3"><?php echo $employeeDetails; ?></div>
                                                        </td>
                                                        <td class="p-4 align-top text-slate-700">LKR <?php echo number_format($totalCost, 2); ?></td>
                                                        <td class="p-4 align-top text-slate-800 font-medium">LKR <?php echo number_format(floatval($row['invoice_value'] ?? 0), 2); ?></td>
                                                        <td class="p-4 align-top text-slate-650"><?php echo $has_invoice ? 'LKR ' . number_format($outstanding, 2) : 'N/A'; ?></td>
                                                        <td class="p-4 align-top font-bold <?php echo $netProfit >= 0 ? 'text-emerald-600' : 'text-red-500'; ?>">
                                                            LKR <?php echo number_format($netProfit, 2); ?>
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
            // Row-level collapsible
            document.querySelectorAll('.collapsible').forEach(item => {
                item.addEventListener('click', () => {
                    const details = item.nextElementSibling;
                    const icon = item.querySelector('.fa-chevron-down, .fa-chevron-up');
                    details.classList.toggle('hidden');
                    if (icon) {
                        if (details.classList.contains('hidden')) {
                            icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                        } else {
                            icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                        }
                    }
                });
            });

            // Status section collapsible
            document.querySelectorAll('.status-header').forEach(header => {
                header.addEventListener('click', () => {
                    const content = header.nextElementSibling;
                    const icon = header.querySelector('.fa-chevron-down, .fa-chevron-up');
                    content.classList.toggle('hidden');
                    if (icon) {
                        if (content.classList.contains('hidden')) {
                            icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                        } else {
                            icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                        }
                    }
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
                            '#3b82f6', '#059669',
                            '#f59e0b', '#06b6d4',
                            '<?php echo $total_net_profit >= 0 ? "#059669" : "#ef4444"; ?>'
                        ],
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, grid: { borderDash: [4, 4] } } },
                    plugins: { legend: { display: false } }
                }
            });

            const jobStatusCtx = document.getElementById('jobStatusChart').getContext('2d');
            new Chart(jobStatusCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_keys($company_stats)); ?>,
                    datasets: [
                        { label: 'Completed', data: <?php echo json_encode(array_column($company_stats, 'Completed')); ?>, backgroundColor: '#059669' },
                        { label: 'Ongoing', data: <?php echo json_encode(array_column($company_stats, 'Ongoing')); ?>, backgroundColor: '#f59e0b' },
                        { label: 'Started', data: <?php echo json_encode(array_column($company_stats, 'Started')); ?>, backgroundColor: '#3b82f6' },
                        { label: 'Not Started', data: <?php echo json_encode(array_column($company_stats, 'Not Started')); ?>, backgroundColor: '#94a3b8' },
                        { label: 'Cancelled', data: <?php echo json_encode(array_column($company_stats, 'Cancelled')); ?>, backgroundColor: '#ef4444' }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true, beginAtZero: true, grid: { borderDash: [4, 4] } }
                    },
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 9 } } } }
                }
            });

            const netProfitCtx = document.getElementById('netProfitChart').getContext('2d');
            new Chart(netProfitCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_keys($company_stats)); ?>,
                    datasets: [{
                        label: 'Net Profit',
                        data: <?php echo json_encode(array_column($company_stats, 'net_profit')); ?>,
                        backgroundColor: '#3b82f6',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, grid: { borderDash: [4, 4] } } },
                    plugins: { legend: { display: false } }
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