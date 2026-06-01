<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
}

// Helper function — safe to be inside the view
function buildEmployeeDetails($emp) {
    $html = "<div class='p-4 bg-slate-50 border border-slate-200 rounded-xl mt-3 space-y-4'>";
    $paid = $emp['paid_amount'] ?? ['Monthly Salary' => 0, 'Advance' => 0, 'Other' => 0];
    $advance_paid = $emp['advance_details']['paid_amount'] ?? 0;
    $advance_deduction = $emp['advance_details']['deduction_amount'] ?? 0;
    $advance_total = $advance_paid + $advance_deduction;

    $html .= "<div><h4 class='font-bold text-slate-800 mb-1.5 uppercase text-[10px] tracking-wider'>Payment Breakdown</h4><ul class='list-disc pl-5 space-y-0.5 text-slate-650'>";
    foreach (['Monthly Salary', 'Advance', 'Other'] as $key) {
        $html .= "<li><strong>$key:</strong> LKR " . number_format($paid[$key] ?? 0, 2) . "</li>";
    }
    $html .= "</ul></div>";
    
    // Add advance payment details
    if ($advance_total > 0) {
        $html .= "<div><h4 class='font-bold text-slate-800 mb-1.5 uppercase text-[10px] tracking-wider'>Advance Payment Details</h4><ul class='list-disc pl-5 space-y-0.5 text-slate-650'>";
        $html .= "<li><strong>Paid Amount:</strong> LKR " . number_format($advance_paid, 2) . "</li>";
        $html .= "<li><strong>Deduction Amount:</strong> LKR " . number_format($advance_deduction, 2) . "</li>";
        $html .= "<li><strong>Total Advance:</strong> LKR " . number_format($advance_total, 2) . "</li>";
        $html .= "</ul></div>";
    }

    $html .= "<div><h4 class='font-bold text-slate-800 mb-1.5 uppercase text-[10px] tracking-wider'>Attendance Records</h4>";
    if (!empty($emp['attendance_details'])) {
        $html .= "<div class='max-h-40 overflow-y-auto border border-slate-100 rounded-lg p-2 bg-white'><ul class='list-none space-y-1.5 text-[11px]'>";
        foreach ($emp['attendance_details'] as $rec) {
            $site = htmlspecialchars($rec['location'] ?? $rec['customer_reference'] ?? 'N/A');
            $html .= "<li class='flex flex-col sm:flex-row justify-between pb-1 border-b border-slate-50 last:border-0'><span class='font-semibold text-slate-700'>{$rec['date']}</span><span class='text-slate-500'>Presence: {$rec['presence']} | Pay: LKR " . number_format($rec['payment'] ?? 0, 2) . " | Site: $site</span></li>";
        }
        $html .= "</ul></div></div>";
    } else {
        $html .= "<p class='italic text-slate-400 text-xs'>No attendance records found.</p></div>";
    }
    $html .= "</div>";
    return $html;
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
    <?php require_once __DIR__ . '/../partials/theme.php'; ?>
    <!-- DataTables Tailwind CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css" rel="stylesheet">
    <style>
        .dataTables_wrapper .dataTables_length select {
            padding: 0.375rem 1.75rem 0.375rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 0.375rem;
            border-color: #e2e8f0;
            background-color: #fff;
        }
        .dataTables_wrapper .dataTables_filter input {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 0.375rem;
            border-color: #e2e8f0;
            background-color: #fff;
            outline: none;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #10b981;
        }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased overflow-x-hidden min-h-screen">

    <?php 
    $activePage = 'reports';
    $headerTitle = 'Monthly Wages Report';
    $headerSubtitle = 'Summary of daily engineering wages, monthly fixed salaries, and company EPF/ETF costs.';
    $breadcrumb = 'Reports / Wages';
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
                            <i class="ri-arrow-left-line"></i> Back to Reports
                        </a>
                        <button onclick="window.print()" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                            <i class="ri-printer-line"></i> Print
                        </button>
                        <?php if (empty($filters['emp_id'])): ?>
                            <button onclick="generatePDF()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                                <i class="ri-file-pdf-line"></i> PDF Slips
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center space-x-2">
                        <i class="ri-error-warning-line"></i>
                        <span><?php echo htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php else: ?>

                    <!-- SINGLE EMPLOYEE DETAILED SALARY SLIP -->
                    <?php if (!empty($filters['emp_id'])):
                        $selectedEmp = null;
                        foreach ($wage_data as $e) {
                            if ($e['emp_id'] == $filters['emp_id']) {
                                $selectedEmp = $e;
                                break;
                            }
                        }
                        if ($selectedEmp):
                            $isFixed   = strtoupper($selectedEmp['rate_type'] ?? 'DAILY') === 'FIXED';
                            $basic     = $isFixed ? floatval($selectedEmp['basic_salary'] ?? $selectedEmp['rate_amount'] ?? 0) : 0;
                            $days      = $selectedEmp['attendance_summary']['total_presence'] ?? 0;
                            $rate      = $isFixed ? 0 : floatval($selectedEmp['rate_amount'] ?? 0);
                            $earned    = $isFixed ? $basic : $days * $rate;
                            $etf       = $isFixed ? $basic * 0.03 : 0;
                            $epfEmp    = $isFixed ? $basic * 0.08 : 0;
                            $epfComp   = $isFixed ? $basic * 0.12 : 0;
                            $payable   = $isFixed ? $basic : $earned;
                            $paidArr   = $selectedEmp['paid_amount'] ?? ['Monthly Salary'=>0, 'Advance'=>0, 'Other'=>0];
                            $paid      = array_sum($paidArr);
                            $advance_paid = $selectedEmp['advance_details']['paid_amount'] ?? 0;
                            $advance_deduction = $selectedEmp['advance_details']['deduction_amount'] ?? 0;
                            $advance_total = $advance_paid + $advance_deduction;
                            
                            $net_payable = floatval($selectedEmp['net_payable'] ?? 0);
                    ?>
                        <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm max-w-4xl mx-auto mb-10">
                            <div class="flex justify-between items-start border-b border-slate-100 pb-6 mb-6">
                                <div>
                                    <h2 class="text-lg font-bold text-slate-900">Salary Slip</h2>
                                    <p class="text-xs text-slate-500 mt-0.5"><?php echo htmlspecialchars($report_title); ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-slate-500 font-medium">Generated On: <?php echo date('d M Y'); ?></span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 text-xs">
                                <div class="space-y-2">
                                    <p><strong class="text-slate-500">Employee Name:</strong> <span class="text-slate-800 font-semibold"><?php echo htmlspecialchars($selectedEmp['emp_name']); ?></span></p>
                                    <p><strong class="text-slate-500">Employee ID:</strong> <span class="text-slate-800 font-semibold"><?php echo $selectedEmp['emp_id']; ?></span></p>
                                </div>
                                <div class="space-y-2">
                                    <p><strong class="text-slate-500">Rate Type:</strong> <span class="text-slate-800 font-semibold"><?php echo $isFixed ? 'Fixed Salary' : 'Daily Wage'; ?></span></p>
                                </div>
                            </div>

                            <div class="border border-slate-200 rounded-xl overflow-hidden mb-6">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-slate-200">
                                            <th class="p-4 font-bold text-slate-700">Description</th>
                                            <th class="p-4 font-bold text-slate-700 text-right">Earnings (LKR)</th>
                                            <th class="p-4 font-bold text-slate-700 text-right">Deductions (LKR)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr>
                                            <td class="p-4 text-slate-650"><?php echo $isFixed ? 'Basic Salary' : 'Daily Earnings'; ?></td>
                                            <td class="p-4 text-right text-slate-800 font-medium"><?php echo number_format($earned, 2); ?></td>
                                            <td class="p-4 text-right text-slate-800">-</td>
                                        </tr>
                                        <tr>
                                            <td class="p-4 text-slate-650">EPF Company Contribution (12%)</td>
                                            <td class="p-4 text-right text-slate-800 font-medium"><?php echo number_format($epfComp, 2); ?></td>
                                            <td class="p-4 text-right text-slate-800">-</td>
                                        </tr>
                                        <?php if ($advance_total > 0): ?>
                                        <tr>
                                            <td class="p-4 text-slate-650">Advance Payments</td>
                                            <td class="p-4 text-right text-slate-800 font-medium"><?php echo number_format($advance_total, 2); ?></td>
                                            <td class="p-4 text-right text-slate-800">-</td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td class="p-4 text-slate-650">EPF Employee Share (8%) & ETF (3%)</td>
                                            <td class="p-4 text-right text-slate-800">-</td>
                                            <td class="p-4 text-right text-slate-850 font-medium"><?php echo number_format($etf + $epfEmp, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="p-4 text-slate-650">Advances & Other Payments</td>
                                            <td class="p-4 text-right text-slate-800">-</td>
                                            <td class="p-4 text-right text-slate-850 font-medium"><?php echo number_format($paid, 2); ?></td>
                                        </tr>
                                        <tr class="bg-slate-50 font-bold border-t border-slate-200">
                                            <td class="p-4 text-slate-800">NET PAYABLE</td>
                                            <td class="p-4 text-right text-emerald-600 font-bold"><?php echo number_format($net_payable, 2); ?></td>
                                            <td class="p-4"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-center">
                                <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-all inline-flex items-center gap-1.5 shadow-sm" onclick="window.print()">
                                    <i class="ri-printer-line"></i> Print Salary Slip
                                </button>
                            </div>
                        </div>
                    <?php
                        endif;
                    endif; ?>

                    <!-- FULL REPORT WHEN NO SINGLE EMPLOYEE SELECTED -->
                    <?php if (empty($filters['emp_id'])): ?>
                        
                        <!-- Filters Card -->
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-8">
                            <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Report Filters</h3>
                            <form method="GET" id="filterForm" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Year</label>
                                    <select name="year" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                                        <?php for ($y = 2022; $y <= 2030; $y++): ?>
                                            <option value="<?php echo $y; ?>" <?php echo ($filters['year'] ?? date('Y')) == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Month</label>
                                    <select name="month" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                                        <option value="">All Months</option>
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
                                            <option value="<?php echo $m; ?>" <?php echo ($filters['month'] ?? '') == $m ? 'selected' : ''; ?>>
                                                <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Employee</label>
                                    <select name="emp_id" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                                        <option value="">All Employees</option>
                                        <?php foreach ($employee_refs as $e): ?>
                                            <option value="<?php echo htmlspecialchars($e['emp_id']); ?>" <?php echo ($filters['emp_id'] ?? '') === $e['emp_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($e['emp_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]">
                                    <i class="ri-filter-3-line"></i> Apply Filter
                                </button>
                            </form>
                        </div>

                        <!-- Financial Summary Cards -->
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-8">
                            <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Wages</h4>
                                <p class="text-sm font-extrabold text-slate-900">LKR <?php echo number_format($total_wages, 0); ?></p>
                            </div>
                            <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Daily Wages</h4>
                                <p class="text-sm font-extrabold text-slate-900">LKR <?php echo number_format($total_daily_wages, 0); ?></p>
                            </div>
                            <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Fixed Wages</h4>
                                <p class="text-sm font-extrabold text-slate-900">LKR <?php echo number_format($total_fixed_wages, 0); ?></p>
                            </div>
                            <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">EPF Cost</h4>
                                <p class="text-sm font-extrabold text-slate-900">LKR <?php echo number_format($epf_costs, 0); ?></p>
                            </div>
                            <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Advances</h4>
                                <p class="text-sm font-extrabold text-slate-900">LKR <?php echo number_format($total_advance_payments, 0); ?></p>
                            </div>
                            <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Employees</h4>
                                <p class="text-sm font-extrabold text-slate-900"><?php echo $employee_count; ?></p>
                            </div>
                            <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Avg / Head</h4>
                                <p class="text-sm font-extrabold text-slate-900">LKR <?php echo number_format($avg_wage_per_employee, 0); ?></p>
                            </div>
                        </div>

                        <!-- Charts Container -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                            <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                                <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Financial Overview</h3>
                                <div class="h-[240px] flex items-center justify-center">
                                    <canvas id="financialChart"></canvas>
                                </div>
                            </div>
                            <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                                <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Wage Distribution</h3>
                                <div class="h-[240px] flex items-center justify-center">
                                    <canvas id="distributionChart"></canvas>
                                </div>
                            </div>
                            <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                                <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Top 10 Earners</h3>
                                <div class="h-[240px] flex items-center justify-center">
                                    <canvas id="topEarnersChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Daily Wage Employees Table -->
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-8 overflow-hidden animate-fadeIn">
                            <div class="p-6 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                                <h3 class="text-sm font-bold text-slate-900">Daily Wage Employees Registry</h3>
                                <span class="text-xs text-slate-500 font-semibold"><?php echo count($daily_wage_employees); ?> Records</span>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="w-full border-collapse text-xs text-left" id="dailyWagesTable">
                                    <thead>
                                        <tr class="bg-slate-100 border-b border-slate-200 text-slate-700">
                                            <th class="p-4 font-bold">Employee</th>
                                            <th class="p-4 font-bold text-center">Days</th>
                                            <th class="p-4 font-bold text-right">Rate</th>
                                            <th class="p-4 font-bold text-right">Earned</th>
                                            <th class="p-4 font-bold text-right">Advance</th>
                                            <th class="p-4 font-bold text-right">Paid</th>
                                            <th class="p-4 font-bold text-right">Net Payable</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php foreach ($daily_wage_employees as $emp):
                                            $days   = $emp['attendance_summary']['total_presence'] ?? 0;
                                            $rate   = floatval($emp['rate_amount'] ?? 0);
                                            $earned = $days * $rate;
                                            $advance_paid = $emp['advance_details']['paid_amount'] ?? 0;
                                            $advance_deduction = $emp['advance_details']['deduction_amount'] ?? 0;
                                            $advance_total = $advance_paid + $advance_deduction;
                                            $paid   = array_sum($emp['paid_amount'] ?? [0,0,0]);
                                            $net_payable = floatval($emp['net_payable'] ?? 0);
                                        ?>
                                            <tr class="hover:bg-slate-50/55 transition-colors">
                                                <td class="p-4 font-medium text-slate-900">
                                                    <div class="collapsible cursor-pointer flex items-center gap-2 font-medium select-none">
                                                        <span><?php echo htmlspecialchars($emp['emp_name']); ?></span>
                                                        <i class="ri-arrow-down-s-line text-slate-400 transition-transform duration-300"></i>
                                                    </div>
                                                    <div class="details hidden mt-2"><?php echo buildEmployeeDetails($emp); ?></div>
                                                </td>
                                                <td class="p-4 text-center text-slate-650 font-medium"><?php echo $days; ?></td>
                                                <td class="p-4 text-right text-slate-600">LKR <?php echo number_format($rate, 2); ?></td>
                                                <td class="p-4 text-right text-slate-800 font-medium">LKR <?php echo number_format($earned, 2); ?></td>
                                                <td class="p-4 text-right text-slate-600">LKR <?php echo number_format($advance_total, 2); ?></td>
                                                <td class="p-4 text-right text-slate-600">LKR <?php echo number_format($paid, 2); ?></td>
                                                <td class="p-4 text-right font-bold <?php echo $net_payable >= 0 ? 'text-emerald-600' : 'text-red-500'; ?>">
                                                    LKR <?php echo number_format($net_payable, 2); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Fixed Salary Employees Table -->
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-8 overflow-hidden animate-fadeIn">
                            <div class="p-6 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                                <h3 class="text-sm font-bold text-slate-900">Fixed Salary Employees Registry</h3>
                                <span class="text-xs text-slate-500 font-semibold"><?php echo count($fixed_rate_employees); ?> Records</span>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="w-full border-collapse text-xs text-left" id="fixedWagesTable">
                                    <thead>
                                        <tr class="bg-slate-100 border-b border-slate-200 text-slate-700">
                                            <th class="p-4 font-bold">Employee</th>
                                            <th class="p-4 font-bold text-right">Basic</th>
                                            <th class="p-4 font-bold text-right">ETF (3%)</th>
                                            <th class="p-4 font-bold text-right">EPF Emp (8%)</th>
                                            <th class="p-4 font-bold text-right">EPF Comp (12%)</th>
                                            <th class="p-4 font-bold text-right">Payable</th>
                                            <th class="p-4 font-bold text-right">Advance</th>
                                            <th class="p-4 font-bold text-right">Paid</th>
                                            <th class="p-4 font-bold text-right">Net Payable</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php foreach ($fixed_rate_employees as $emp):
                                            $basic     = floatval($emp['basic_salary'] ?? $emp['rate_amount'] ?? 0);
                                            $etf       = $basic * 0.03;
                                            $epfEmp    = $basic * 0.08;
                                            $epfComp   = $basic * 0.12;
                                            $payable   = $basic;
                                            $days      = $emp['attendance_summary']['total_presence'] ?? 0;
                                            $advance_paid = $emp['advance_details']['paid_amount'] ?? 0;
                                            $advance_deduction = $emp['advance_details']['deduction_amount'] ?? 0;
                                            $advance_total = $advance_paid + $advance_deduction;
                                            $paid      = array_sum($emp['paid_amount'] ?? [0,0,0]);
                                            $net_payable = floatval($emp['net_payable'] ?? 0);
                                        ?>
                                            <tr class="hover:bg-slate-50/55 transition-colors">
                                                <td class="p-4 font-medium text-slate-900">
                                                    <div class="collapsible cursor-pointer flex items-center gap-2 font-medium select-none">
                                                        <span><?php echo htmlspecialchars($emp['emp_name']); ?></span>
                                                        <i class="ri-arrow-down-s-line text-slate-400 transition-transform duration-300"></i>
                                                    </div>
                                                    <div class="details hidden mt-2"><?php echo buildEmployeeDetails($emp); ?></div>
                                                </td>
                                                <td class="p-4 text-right text-slate-650">LKR <?php echo number_format($basic, 2); ?></td>
                                                <td class="p-4 text-right text-slate-600">LKR <?php echo number_format($etf, 2); ?></td>
                                                <td class="p-4 text-right text-slate-600">LKR <?php echo number_format($epfEmp, 2); ?></td>
                                                <td class="p-4 text-right text-slate-600">LKR <?php echo number_format($epfComp, 2); ?></td>
                                                <td class="p-4 text-right text-slate-800 font-medium">LKR <?php echo number_format($payable, 2); ?></td>
                                                <td class="p-4 text-right text-slate-600">LKR <?php echo number_format($advance_total, 2); ?></td>
                                                <td class="p-4 text-right text-slate-600">LKR <?php echo number_format($paid, 2); ?></td>
                                                <td class="p-4 text-right font-bold <?php echo $net_payable >= 0 ? 'text-emerald-600' : 'text-red-500'; ?>">
                                                    LKR <?php echo number_format($net_payable, 2); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Labor Wages Summary Table -->
                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-8 overflow-hidden animate-fadeIn">
                            <div class="p-6 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                                <h3 class="text-sm font-bold text-slate-900">Labor Wages Registry Summary</h3>
                                <span class="text-xs text-slate-500 font-semibold"><?php echo count($labor_wages_data['summations'] ?? []); ?> Records</span>
                            </div>
                            <div class="p-4 overflow-x-auto">
                                <table class="w-full border-collapse text-xs text-left" id="laborWagesTable">
                                    <thead>
                                        <tr class="bg-slate-100 border-b border-slate-200 text-slate-700">
                                            <th class="p-4 font-bold">Labor Name</th>
                                            <th class="p-4 font-bold text-center">Total Work Days</th>
                                            <th class="p-4 font-bold text-right">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php foreach ($labor_wages_data['summations'] ?? [] as $sum):
                                            $details = '';
                                            foreach ($labor_wages_data['details'][$sum['labor_name']] ?? [] as $d) {
                                                $details .= "<li>Job {$d['job_id']} | {$d['expensed_date']} | " . htmlspecialchars($d['description'] ?? '') . " | LKR " . number_format($d['expense_amount'] ?? 0, 2) . "</li>";
                                            }
                                            $details = $details ? "<ul class='list-disc pl-5 mt-2 space-y-1 text-slate-500'>$details</ul>" : "<p class='italic text-slate-400 mt-2'>No details</p>";
                                        ?>
                                            <tr class="hover:bg-slate-50/55 transition-colors">
                                                <td class="p-4 font-medium text-slate-900">
                                                    <div class="collapsible cursor-pointer flex items-center gap-2 font-medium select-none">
                                                        <span><?php echo htmlspecialchars($sum['labor_name'] ?? 'Unknown'); ?></span>
                                                        <i class="ri-arrow-down-s-line text-slate-400 transition-transform duration-300"></i>
                                                    </div>
                                                    <div class="details hidden p-4 bg-slate-50/50 border border-slate-100 rounded-xl mt-2"><?php echo $details; ?></div>
                                                </td>
                                                <td class="p-4 text-center text-slate-650 font-medium"><?php echo $sum['total_days'] ?? 0; ?></td>
                                                <td class="p-4 text-right font-bold text-slate-900">LKR <?php echo number_format($sum['total_amount'] ?? 0, 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <?php endif; ?>

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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
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

        $(document).ready(function() {
            // Initialize DataTables
            $('#dailyWagesTable').DataTable({
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                searching: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search daily registry..."
                }
            });

            $('#fixedWagesTable').DataTable({
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                searching: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search fixed registry..."
                }
            });

            $('#laborWagesTable').DataTable({
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                searching: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search labor wages..."
                }
            });

            // Delegated collapsible rows listener
            $(document).on('click', '.collapsible', function() {
                const details = $(this).next('.details');
                const icon = $(this).find('i');
                details.toggleClass('hidden');
                if (details.hasClass('hidden')) {
                    icon.removeClass('ri-arrow-up-s-line').addClass('ri-arrow-down-s-line');
                } else {
                    icon.removeClass('ri-arrow-down-s-line').addClass('ri-arrow-up-s-line');
                }
            });

            // Charts
            <?php if (empty($filters['emp_id'])): ?>
            new Chart(document.getElementById('financialChart'), {
                type: 'bar',
                data: {
                    labels: ['Total', 'Daily', 'Fixed', 'EPF', 'Advance'],
                    datasets: [{
                        label: 'Amount (LKR)',
                        data: [<?php echo $total_wages; ?>, <?php echo $total_daily_wages; ?>, <?php echo $total_fixed_wages; ?>, <?php echo $epf_costs; ?>, <?php echo $total_advance_payments; ?>],
                        backgroundColor: '#059669',
                        borderRadius: 6
                    }]
                },
                options: { 
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { borderDash: [4, 4] } } } 
                }
            });

            new Chart(document.getElementById('distributionChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Daily Wages', 'Fixed Wages', 'EPF Cost', 'Advance Payments'],
                    datasets: [{
                        data: [<?php echo $total_daily_wages; ?>, <?php echo $total_fixed_wages; ?>, <?php echo $epf_costs; ?>, <?php echo $total_advance_payments; ?>],
                        backgroundColor: ['#059669', '#3b82f6', '#f59e0b', '#ef4444']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } } }
                }
            });

            new Chart(document.getElementById('topEarnersChart'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_column(array_slice($top_earners, 0, 10), 'emp_name')); ?>,
                    datasets: [{
                        label: 'Net Payable',
                        data: <?php echo json_encode(array_column(array_slice($top_earners, 0, 10), 'net_payable')); ?>,
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    }]
                },
                options: { 
                    indexAxis: 'y', 
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true, grid: { borderDash: [4, 4] } } } 
                }
            });
            <?php endif; ?>
        });

        function generatePDF() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form));
            params.set('generate_pdf', '1');
            location.href = location.pathname + '?' + params.toString();
        }
    </script>
</body>
</html>