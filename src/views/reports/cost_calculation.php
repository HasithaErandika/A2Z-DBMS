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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-poppins bg-gray-50 text-gray-900 leading-relaxed overflow-x-hidden">
    <div class="container mx-auto p-6 min-h-screen bg-[url('data:image/svg+xml,%3Csvg xmlns=\"http://www.w3.org/2000/svg\" width=\"120\" height=\"120\" viewBox=\"0 0 120 120\"%3E%3Cg fill=\"%231E3A8A\" fill-opacity=\"0.03\"%3E%3Cpath d=\"M60 20 L80 60 L60 100 L40 60 Z\"/%3E%3C/g%3E%3C/svg%3E')] bg-[length:240px]">
        <div class="header bg-gradient-to-br from-blue-900 to-blue-500 p-8 rounded-xl shadow-2xl flex items-center justify-between mb-10 text-white relative overflow-hidden hover:after:opacity-20 after:content-[''] after:absolute after:top-[-50%] after:left-[-50%] after:w-[200%] after:h-[200%] after:bg-white/10 after:rotate-30 after:transition-all after:duration-500 after:opacity-0 after:z-0">
            <h1 class="text-3xl font-semibold z-10">Cost Calculation Report</h1>
            <div class="header-controls flex gap-3 z-10">
                <button class="btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30" onclick="window.print()"><i class="ri-printer-line"></i> Print</button>
                <button class="btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30" onclick="window.location.href='<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>'"><i class="ri-arrow-left-line"></i> Go Back</button>
                <button class="btn bg-transparent text-blue-900 border-2 border-blue-900 px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:bg-blue-900 hover:text-white hover:translate-y-[-2px]" onclick="downloadCSV()"><i class="ri-download-line"></i> Download CSV</button>
            </div>
        </div>
        <?php if (isset($error)): ?>
            <div class="error-message bg-red-500 text-white p-4 rounded-xl mb-5 text-center"><?php echo htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>
            <div class="filter-card bg-white p-6 rounded-xl shadow-lg mb-10 transition-all duration-300 hover:shadow-2xl">
                <form method="GET" class="filter-form grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-4 items-end" id="filterForm" action="<?php echo htmlspecialchars(FULL_BASE_URL . '/reports/cost_calculation', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Invoice ID</label>
                        <input type="text" name="invoice_id" value="<?php echo htmlspecialchars($filters['invoice_no'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Customer</label>
                        <select name="customer_name" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                            <option value="">All</option>
                            <?php foreach ($customer_refs as $ref): ?>
                                <option value="<?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php echo $filters['customer_reference'] === $ref ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Company Ref</label>
                        <select name="client_ref" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                            <option value="">All</option>
                            <?php foreach ($company_refs as $ref): ?>
                                <option value="<?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php echo $filters['company_reference'] === $ref ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Payment Status</label>
                        <select name="status" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                            <option value="">All</option>
                            <option value="paid" <?php echo $filters['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="unpaid" <?php echo $filters['status'] === 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                        </select>
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Completion Status</label>
                        <select name="completion" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                            <option value="">All</option>
                            <option value="Completed" <?php echo $filters['completion'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="Ongoing" <?php echo $filters['completion'] === 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                            <option value="Started" <?php echo $filters['completion'] === 'Started' ? 'selected' : ''; ?>>Started</option>
                            <option value="Not Started" <?php echo $filters['completion'] === 'Not Started' ? 'selected' : ''; ?>>Not Started</option>
                            <option value="Cancelled" <?php echo $filters['completion'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">From Date</label>
                        <input type="date" name="from_date" value="<?php echo htmlspecialchars($filters['from_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">To Date</label>
                        <input type="date" name="to_date" value="<?php echo htmlspecialchars($filters['to_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                    </div>
                    <button type="submit" class="btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30"><i class="ri-filter-line"></i> Filter</button>
                </form>
            </div>
            <div class="summary-card bg-white p-6 rounded-xl shadow-lg mb-10 transition-all duration-300 hover:shadow-2xl">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Filtered Summary</h2>
                <div class="summary-grid grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-6">
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-amber-500">
                        <h3 class="text-base text-gray-500 mb-2">Total Invoices</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo number_format($total_invoice_amount, 2); ?></p>
                    </div>
                    <div class="summary-item paid bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-green-500">
                        <h3 class="text-base text-gray-500 mb-2">Paid Amount</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo number_format($total_paid_amount, 2); ?></p>
                    </div>
                    <div class="summary-item unpaid bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-red-500">
                        <h3 class="text-base text-gray-500 mb-2">Unpaid Amount</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo number_format($total_unpaid_amount, 2); ?></p>
                    </div>
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-amber-500">
                        <h3 class="text-base text-gray-500 mb-2">Unpaid Count</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($unpaid_invoice_count, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="summary-item unpaid bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-red-500">
                        <h3 class="text-base text-gray-500 mb-2">Due Balance</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo number_format($due_balance, 2); ?></p>
                    </div>
                </div>
                <div class="section mt-6">
                    <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Additional Metrics</h2>
                    <div class="summary-grid grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-6">
                        <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-amber-500">
                            <h3 class="text-base text-gray-500 mb-2">Total Expenses</h3>
                            <p class="text-lg font-semibold text-gray-900"><?php echo number_format($total_expenses, 2); ?></p>
                        </div>
                        <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-amber-500">
                            <h3 class="text-base text-gray-500 mb-2">Total Employee Costs</h3>
                            <p class="text-lg font-semibold text-gray-900"><?php echo number_format($total_employee_costs_sum, 2); ?></p>
                        </div>
                        <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-amber-500">
                            <h3 class="text-base text-gray-500 mb-2">Total Jobs</h3>
                            <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars(array_sum(array_map('count', $job_groups)), ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-amber-500">
                            <h3 class="text-base text-gray-500 mb-2">Total Capacity</h3>
                            <p class="text-lg font-semibold text-gray-900"><?php echo number_format($total_capacity, 2); ?></p>
                        </div>
                        <div class="summary-item <?php echo $total_net_profit >= 0 ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500'; ?> bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg">
                            <h3 class="text-base text-gray-500 mb-2">Net Profit</h3>
                            <p class="text-lg font-semibold text-gray-900"><?php echo number_format($total_net_profit, 2); ?></p>
                        </div>
                        <div class="summary-item <?php echo $profit_margin >= 0 ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500'; ?> bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg">
                            <h3 class="text-base text-gray-500 mb-2">Profit Margin</h3>
                            <p class="text-lg font-semibold text-gray-900"><?php echo number_format($profit_margin, 2); ?>%</p>
                        </div>
                    </div>
                </div>
                <div class="section overall mt-6">
                    <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Overall Summary</h2>
                    <div class="summary-grid grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-6">
                        <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-amber-500">
                            <h3 class="text-base text-gray-500 mb-2">Total Invoices</h3>
                            <p class="text-lg font-semibold text-gray-900"><?php echo number_format($overall_invoice_amount, 2); ?></p>
                        </div>
                        <div class="summary-item paid bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-green-500">
                            <h3 class="text-base text-gray-500 mb-2">Paid Amount</h3>
                            <p class="text-lg font-semibold text-gray-900"><?php echo number_format($overall_paid_amount, 2); ?></p>
                        </div>
                        <div class="summary-item unpaid bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-red-500">
                            <h3 class="text-base text-gray-500 mb-2">Unpaid Amount</h3>
                            <p class="text-lg font-semibold text-gray-900"><?php echo number_format($overall_unpaid_amount, 2); ?></p>
                        </div>
                        <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-amber-500">
                            <h3 class="text-base text-gray-500 mb-2">Unpaid Count</h3>
                            <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($overall_unpaid_count, ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <div class="summary-item unpaid bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-red-500">
                            <h3 class="text-base text-gray-500 mb-2">Due Balance</h3>
                            <p class="text-lg font-semibold text-gray-900"><?php echo number_format($overall_due_balance, 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="charts-container flex flex-row gap-6 mb-10 flex-wrap">
                <div class="chart-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl flex-1 min-w-[300px]">
                    <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Financial Overview</h2>
                    <div class="chart-container max-w-full h-[300px]">
                        <canvas id="financialChart"></canvas>
                    </div>
                </div>
                <div class="chart-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl flex-1 min-w-[300px]">
                    <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Job Status by Company</h2>
                    <div class="chart-container max-w-full h-[300px]">
                        <canvas id="jobStatusChart"></canvas>
                    </div>
                </div>
                <div class="chart-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl flex-1 min-w-[300px]">
                    <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Net Profit by Company</h2>
                    <div class="chart-container max-w-full h-[300px]">
                        <canvas id="netProfitChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="table-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl overflow-x-auto">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Detailed Job Analysis</h2>
                <?php foreach ($job_groups as $status => $jobs): ?>
                    <?php if (!empty($jobs)): ?>
                        <div class="status-section mb-5">
                            <div class="status-header cursor-pointer bg-gray-100 p-2.5 rounded-md flex justify-between items-center">
                                <h2 class="text-base font-semibold m-0"><?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?> Jobs (<?php echo count($jobs); ?>)</h2>
                                <i class="ri-arrow-down-s-line transition-transform duration-300"></i>
                            </div>
                            <div class="status-content hidden">
                                <table class="table w-full border-collapse text-base">
                                    <thead>
                                        <tr>
                                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Job Details</th>
                                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Date</th>
                                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Capacity</th>
                                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Invoice Details</th>
                                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Expenses</th>
                                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Employee Costs</th>
                                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Total Cost</th>
                                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Total Invoiced</th>
                                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Outstanding</th>
                                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Net Profit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $current_company = ''; ?>
                                        <?php foreach ($jobs as $row): ?>
                                            <?php if ($row['company_reference'] !== $current_company): ?>
                                                <tr>
                                                    <td colspan="10" class="bg-gray-200 p-3 font-bold">Company: <?php echo htmlspecialchars($row['company_reference'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></td>
                                                </tr>
                                                <?php $current_company = $row['company_reference']; ?>
                                            <?php endif; ?>
                                            <?php
                                            $jobDetails = "<ul class='list-disc pl-5'>";
                                            $jobDetails .= "<li>Job ID: " . htmlspecialchars($row['job_id'] ?? '', ENT_QUOTES, 'UTF-8') . "</li>";
                                            $jobDetails .= "<li>Location: " . htmlspecialchars($row['location'] ?? '', ENT_QUOTES, 'UTF-8') . "</li>";
                                            $jobDetails .= "<li>Company Ref: " . htmlspecialchars($row['company_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</li>";
                                            $jobDetails .= "<li>Engineer: " . htmlspecialchars($row['engineer'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</li>";
                                            $jobDetails .= "</ul>";
                                            $has_invoice = !empty($row['invoices']);
                                            $invoiceDetails = "<ul class='list-disc pl-5'>";
                                            if ($has_invoice) {
                                                foreach ($row['invoices'] as $inv) {
                                                    $invoiceDetails .= "<li>";
                                                    $invoiceDetails .= "Invoice No: " . htmlspecialchars($inv['no'], ENT_QUOTES, 'UTF-8') . "<br>";
                                                    $invoiceDetails .= "Value: " . number_format($inv['value'], 2) . "<br>";
                                                    $invoiceDetails .= "Received: " . number_format($inv['received'], 2) . "<br>";
                                                    $invoiceDetails .= "Date Paid: " . htmlspecialchars($inv['date_paid'], ENT_QUOTES, 'UTF-8');
                                                    $invoiceDetails .= "</li>";
                                                }
                                            } else {
                                                $invoiceDetails .= "<li>No Invoice</li>";
                                            }
                                            $invoiceDetails .= "</ul>";
                                            $expenseDetails = "<ul class='list-disc pl-5'>";
                                            $totalExpensesForJob = floatval($row['operational_expenses'] ?? 0);
                                            if (!empty($row['expense_details'])) {
                                                foreach ($row['expense_details'] as $category => $amount) {
                                                    $expenseDetails .= "<li>" . htmlspecialchars($category, ENT_QUOTES, 'UTF-8') . ": " . number_format(floatval($amount), 2) . "</li>";
                                                }
                                            } else {
                                                $expenseDetails .= "<li>No expenses recorded</li>";
                                            }
                                            $expenseDetails .= "</ul>";
                                            $employeeDetails = "<ul class='list-disc pl-5'>";
                                            $totalEmployeeCostsForJob = floatval($row['total_employee_costs'] ?? 0);
                                            if (!empty($row['employee_details']) && is_array($row['employee_details'])) {
                                                foreach ($row['employee_details'] as $emp) {
                                                    $employeeDetails .= "<li>" . htmlspecialchars($emp['emp_name'] ?? 'Unknown Employee', ENT_QUOTES, 'UTF-8') . ": " . number_format(floatval($emp['payment'] ?? 0), 2);
                                                    if (!empty($emp['days']) && is_array($emp['days'])) {
                                                        $employeeDetails .= "<ul class='list-disc pl-5'>";
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
                                            } else {
                                                $employeeDetails .= "<li>No employee costs recorded for Job ID: " . htmlspecialchars($row['job_id'] ?? '', ENT_QUOTES, 'UTF-8') . " (Check attendance or rate data)</li>";
                                            }
                                            $employeeDetails .= "</ul>";
                                            $totalCost = $totalExpensesForJob + $totalEmployeeCostsForJob;
                                            $outstanding = floatval($row['invoice_value'] ?? 0) - floatval($row['received_amount'] ?? 0);
                                            $netProfit = floatval($row['net_profit'] ?? 0);
                                            $displayDate = $row['date_completed'] === '0000-00-00' ? 'Not Set' : htmlspecialchars($row['date_completed'] ?? '', ENT_QUOTES, 'UTF-8');
                                            ?>
                                            <tr>
                                                <td class="p-3 border-b border-gray-200 align-top">
                                                    <div class="collapsible cursor-pointer flex items-center gap-2 font-medium">
                                                        <span class="total">Customer: <?php echo htmlspecialchars($row['customer_reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                        <i class="ri-arrow-down-s-line icon transition-transform duration-300"></i>
                                                    </div>
                                                    <div class="details hidden p-3 bg-blue-50/50 rounded-lg mt-2"><?php echo $jobDetails; ?></div>
                                                </td>
                                                <td class="p-3 border-b border-gray-200 align-top"><?php echo $displayDate; ?></td>
                                                <td class="p-3 border-b border-gray-200 align-top"><?php echo htmlspecialchars($row['job_capacity'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="p-3 border-b border-gray-200 align-top">
                                                    <div class="collapsible cursor-pointer flex items-center gap-2 font-medium">
                                                        <span class="total"><?php echo $has_invoice ? htmlspecialchars($row['invoice_no'], ENT_QUOTES, 'UTF-8') : 'No Invoice'; ?></span>
                                                        <i class="ri-arrow-down-s-line icon transition-transform duration-300"></i>
                                                    </div>
                                                    <div class="details hidden p-3 bg-blue-50/50 rounded-lg mt-2"><?php echo $invoiceDetails; ?></div>
                                                </td>
                                                <td class="p-3 border-b border-gray-200 align-top">
                                                    <div class="collapsible cursor-pointer flex items-center gap-2 font-medium">
                                                        <span class="total">Total: <?php echo number_format($totalExpensesForJob, 2); ?></span>
                                                        <i class="ri-arrow-down-s-line icon transition-transform duration-300"></i>
                                                    </div>
                                                    <div class="details hidden p-3 bg-blue-50/50 rounded-lg mt-2"><?php echo $expenseDetails; ?></div>
                                                </td>
                                                <td class="p-3 border-b border-gray-200 align-top">
                                                    <div class="collapsible cursor-pointer flex items-center gap-2 font-medium">
                                                        <span class="total">Total: <?php echo number_format($totalEmployeeCostsForJob, 2); ?></span>
                                                        <i class="ri-arrow-down-s-line icon transition-transform duration-300"></i>
                                                    </div>
                                                    <div class="details hidden p-3 bg-blue-50/50 rounded-lg mt-2"><?php echo $employeeDetails; ?></div>
                                                </td>
                                                <td class="p-3 border-b border-gray-200 align-top"><?php echo number_format($totalCost, 2); ?></td>
                                                <td class="p-3 border-b border-gray-200 align-top"><?php echo number_format(floatval($row['invoice_value'] ?? 0), 2); ?></td>
                                                <td class="p-3 border-b border-gray-200 align-top"><?php echo $has_invoice ? number_format($outstanding, 2) : 'N/A'; ?></td>
                                                <td class="p-3 border-b border-gray-200 align-top <?php echo $netProfit >= 0 ? 'text-green-500' : 'text-red-500'; ?>">
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
                    details.classList.toggle('hidden');
                });
            });
            // Status section collapsible
            document.querySelectorAll('.status-header').forEach(header => {
                header.addEventListener('click', () => {
                    const content = header.nextElementSibling;
                    header.classList.toggle('active');
                    const icon = header.querySelector('i');
                    icon.classList.toggle('rotate-180');
                    content.classList.toggle('hidden');
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
            const jobStatusCtx = document.getElementById('jobStatusChart').getContext('2d');
            new Chart(jobStatusCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_keys($company_stats)); ?>,
                    datasets: [
                        { label: 'Completed', data: <?php echo json_encode(array_column($company_stats, 'Completed')); ?>, backgroundColor: 'rgba(16, 185, 129, 0.6)' },
                        { label: 'Ongoing', data: <?php echo json_encode(array_column($company_stats, 'Ongoing')); ?>, backgroundColor: 'rgba(255, 193, 7, 0.6)' },
                        { label: 'Started', data: <?php echo json_encode(array_column($company_stats, 'Started')); ?>, backgroundColor: 'rgba(0, 123, 255, 0.6)' },
                        { label: 'Not Started', data: <?php echo json_encode(array_column($company_stats, 'Not Started')); ?>, backgroundColor: 'rgba(108, 117, 125, 0.6)' },
                        { label: 'Cancelled', data: <?php echo json_encode(array_column($company_stats, 'Cancelled')); ?>, backgroundColor: 'rgba(239, 68, 68, 0.6)' }
                    ]
                },
                options: {
                    scales: {
                        x: { stacked: true },
                        y: { stacked: true, beginAtZero: true }
                    },
                    plugins: { legend: { display: true }, title: { display: true, text: 'Job Status by Company' } }
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
                        backgroundColor: 'rgba(30, 144, 255, 0.6)',
                        borderColor: 'rgba(30, 144, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true } },
                    plugins: { legend: { display: false }, title: { display: true, text: 'Net Profit by Company' } }
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