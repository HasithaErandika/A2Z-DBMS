<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
}

// Helper function — safe to be inside the view
function buildEmployeeDetails($emp) {
    $html = "<div class='p-3 bg-blue-50/50 rounded-lg mt-2'>";
    $paid = $emp['paid_amount'] ?? ['Monthly Salary' => 0, 'Advance' => 0, 'Other' => 0];

    $html .= "<h4 class='font-medium text-gray-800 mb-2'>Payment Breakdown</h4><ul class='list-disc pl-5'>";
    foreach (['Monthly Salary', 'Advance', 'Other'] as $key) {
        $html .= "<li><strong>$key:</strong> LKR " . number_format($paid[$key] ?? 0, 2) . "</li>";
    }
    $html .= "</ul>";

    $html .= "<h4 class='font-medium text-gray-800 mt-3 mb-2'>Attendance Records</h4>";
    if (!empty($emp['attendance_details'])) {
        $html .= "<div class='max-h-40 overflow-y-auto'><ul class='list-disc pl-5'>";
        foreach ($emp['attendance_details'] as $rec) {
            $site = htmlspecialchars($rec['location'] ?? $rec['customer_reference'] ?? 'N/A');
            $html .= "<li><strong>{$rec['date']}</strong> → Presence: {$rec['presence']} | Pay: LKR " . number_format($rec['payment'] ?? 0, 2) . " | Site: $site</li>";
        }
        $html .= "</ul></div>";
    } else {
        $html .= "<p class='italic text-gray-500'>No attendance records found.</p>";
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-poppins bg-gray-50 text-gray-900 leading-relaxed overflow-x-hidden">
    <div class="container mx-auto p-6 min-h-screen bg-[url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120"%3E%3Cg fill="%231E3A8A" fill-opacity="0.03"%3E%3Cpath d="M60 20 L80 60 L60 100 L40 60 Z"/%3E%3C/g%3E%3C/svg%3E')] bg-[length:240px]">
        <div class="header bg-gradient-to-br from-blue-900 to-blue-500 p-8 rounded-xl shadow-2xl flex items-center justify-between mb-10 text-white relative overflow-hidden hover:after:opacity-20 after:content-[''] after:absolute after:top-[-50%] after:left-[-50%] after:w-[200%] after:h-[200%] after:bg-white/10 after:rotate-30 after:transition-all after:duration-500 after:opacity-0 after:z-0">
            <h1 class="text-3xl font-semibold z-10">Wages Report</h1>
            <div class="header-controls flex gap-3 z-10">
                <button class="btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30" onclick="window.print()"><i class="ri-printer-line"></i> Print</button>
                <button class="btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30" onclick="window.location.href='<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>'"><i class="ri-arrow-left-line"></i> Go Back</button>
                <button class="btn bg-transparent text-blue-900 border-2 border-blue-900 px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:bg-blue-900 hover:text-white hover:translate-y-[-2px]" onclick="downloadCSV()"><i class="ri-download-line"></i> Download CSV</button>
                <?php if (empty($filters['emp_id'])): ?>
                    <button class="btn bg-gradient-to-br from-green-600 to-green-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-green-900/30" onclick="generatePDF()"><i class="ri-file-pdf-line"></i> PDF Slips</button>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message bg-red-500 text-white p-4 rounded-xl mb-5 text-center"><?php echo htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
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
                    $days      = $selectedEmp['attendance_summary']['presence_count'] ?? 0;
                    $rate      = $isFixed ? 0 : floatval($selectedEmp['rate_amount'] ?? 0);
                    $earned    = $isFixed ? $basic : $days * $rate;
                    $etf       = $isFixed ? $basic * 0.03 : 0;
                    $epfEmp    = $isFixed ? $basic * 0.08 : 0;
                    $epfComp   = $isFixed ? $basic * 0.12 : 0;
                    $payable   = $earned + $epfComp;
                    $paidArr   = $selectedEmp['paid_amount'] ?? ['Monthly Salary'=>0, 'Advance'=>0, 'Other'=>0];
                    $paid      = array_sum($paidArr);
                    $net       = $payable - $etf - $epfEmp - $paid;
            ?>
                <div class="salary-slip bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl mb-10">
                    <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Salary Slip</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <p><strong class="text-gray-600">Employee Name:</strong> <?php echo htmlspecialchars($selectedEmp['emp_name']); ?></p>
                            <p><strong class="text-gray-600">Employee ID:</strong> <?php echo $selectedEmp['emp_id']; ?></p>
                            <p><strong class="text-gray-600">Rate Type:</strong> <?php echo $isFixed ? 'Fixed Salary' : 'Daily Wage'; ?></p>
                        </div>
                        <div>
                            <p><strong class="text-gray-600">Period:</strong> <?php echo htmlspecialchars($report_title); ?></p>
                            <p><strong class="text-gray-600">Generated On:</strong> <?php echo date('d F Y'); ?></p>
                        </div>
                    </div>

                    <div class="overflow-x-auto mb-6">
                        <table class="table w-full border-collapse text-base">
                            <thead>
                                <tr>
                                    <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-left">Description</th>
                                    <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Earnings (LKR)</th>
                                    <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Deductions (LKR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="p-3 border-b border-gray-200"><?php echo $isFixed ? 'Basic Salary' : 'Daily Earnings'; ?></td>
                                    <td class="p-3 border-b border-gray-200 text-right font-medium"><?php echo number_format($earned, 2); ?></td>
                                    <td class="p-3 border-b border-gray-200 text-right"><?php echo number_format($etf + $epfEmp, 2); ?></td>
                                </tr>
                                <tr>
                                    <td class="p-3 border-b border-gray-200">EPF Company Contribution (12%)</td>
                                    <td class="p-3 border-b border-gray-200 text-right font-medium"><?php echo number_format($epfComp, 2); ?></td>
                                    <td class="p-3 border-b border-gray-200 text-right">-</td>
                                </tr>
                                <tr>
                                    <td class="p-3 border-b border-gray-200">Advances & Other Payments</td>
                                    <td class="p-3 border-b border-gray-200 text-right">-</td>
                                    <td class="p-3 border-b border-gray-200 text-right font-medium"><?php echo number_format($paid, 2); ?></td>
                                </tr>
                                <tr class="bg-blue-50 font-bold">
                                    <td class="p-3 text-center">NET PAYABLE</td>
                                    <td class="p-3 text-right text-green-600"><?php echo number_format($net, 2); ?></td>
                                    <td class="p-3"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center">
                        <button class="btn bg-gradient-to-br from-green-600 to-green-500 text-white px-6 py-3 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 mx-auto hover:translate-y-[-2px] hover:shadow-xl hover:shadow-green-900/30" onclick="window.print()">
                            <i class="ri-printer-line"></i> Print Salary Slip
                        </button>
                    </div>
                </div>
                <?php
                // Stop rendering the rest of the page for single employee
                return;
                endif;
            endif; ?>

            <!-- FULL REPORT WHEN NO SINGLE EMPLOYEE SELECTED -->

            <!-- Filters -->
            <div class="filter-card bg-white p-6 rounded-xl shadow-lg mb-10 transition-all duration-300 hover:shadow-2xl">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Report Filters</h2>
                <form method="GET" id="filterForm" class="filter-form grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-4 items-end">
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Year</label>
                        <select name="year" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                            <?php for ($y = 2023; $y <= 2030; $y++): ?>
                                <option value="<?php echo $y; ?>" <?php echo ($filters['year'] ?? date('Y')) == $y ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Month</label>
                        <select name="month" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                            <option value="">All Months</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php echo $m; ?>" <?php echo ($filters['month'] ?? '') == $m ? 'selected' : ''; ?>>
                                    <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Employee</label>
                        <select name="emp_id" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                            <option value="">All Employees</option>
                            <?php foreach ($employee_refs as $e): ?>
                                <option value="<?php echo htmlspecialchars($e['emp_id']); ?>" <?php echo ($filters['emp_id'] ?? '') === $e['emp_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($e['emp_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30">
                        <i class="ri-filter-line"></i> Apply Filter
                    </button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="summary-card bg-white p-6 rounded-xl shadow-lg mb-10 transition-all duration-300 hover:shadow-2xl">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Financial Summary</h2>
                <div class="summary-grid grid grid-cols-[repeat(auto-fit,minmax(150px,1fr))] gap-4">
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-amber-500">
                        <h3 class="text-base text-gray-500 mb-2">Total Wages</h3>
                        <p class="text-lg font-semibold text-gray-900">LKR <?php echo number_format($total_wages, 2); ?></p>
                    </div>
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-green-500">
                        <h3 class="text-base text-gray-500 mb-2">Daily Wages</h3>
                        <p class="text-lg font-semibold text-gray-900">LKR <?php echo number_format($total_daily_wages, 2); ?></p>
                    </div>
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-blue-500">
                        <h3 class="text-base text-gray-500 mb-2">Fixed Wages</h3>
                        <p class="text-lg font-semibold text-gray-900">LKR <?php echo number_format($total_fixed_wages, 2); ?></p>
                    </div>
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-purple-500">
                        <h3 class="text-base text-gray-500 mb-2">EPF Cost</h3>
                        <p class="text-lg font-semibold text-gray-900">LKR <?php echo number_format($epf_costs, 2); ?></p>
                    </div>
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-indigo-500">
                        <h3 class="text-base text-gray-500 mb-2">Employees</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo $employee_count; ?></p>
                    </div>
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-pink-500">
                        <h3 class="text-base text-gray-500 mb-2">Avg Wage</h3>
                        <p class="text-lg font-semibold text-gray-900">LKR <?php echo number_format($avg_wage_per_employee, 2); ?></p>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="charts-container flex flex-row gap-6 mb-10 flex-wrap">
                <div class="chart-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl flex-1 min-w-[300px]">
                    <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Financial Overview</h2>
                    <div class="chart-container max-w-full h-[300px]">
                        <canvas id="financialChart"></canvas>
                    </div>
                </div>
                <div class="chart-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl flex-1 min-w-[300px]">
                    <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Wage Distribution</h2>
                    <div class="chart-container max-w-full h-[300px]">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>
                <div class="chart-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl flex-1 min-w-[300px]">
                    <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Top 10 Earners</h2>
                    <div class="chart-container max-w-full h-[300px]">
                        <canvas id="topEarnersChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Daily Wage Table -->
            <div class="table-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl mb-10 overflow-x-auto">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Daily Wage Employees</h2>
                <table class="table w-full border-collapse text-base">
                    <thead>
                        <tr>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-left">Employee</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-center">Days</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Rate</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Earned</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Paid</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daily_wage_employees as $emp):
                            $days   = $emp['attendance_summary']['presence_count'] ?? 0;
                            $rate   = floatval($emp['rate_amount'] ?? 0);
                            $earned = $days * $rate;
                            $paid   = array_sum($emp['paid_amount'] ?? [0,0,0]);
                            $net    = $earned - $paid;
                        ?>
                            <tr>
                                <td class="p-3 border-b border-gray-200 align-top">
                                    <div class="collapsible cursor-pointer flex items-center gap-2 font-medium">
                                        <span class="total"><?php echo htmlspecialchars($emp['emp_name']); ?></span>
                                        <i class="ri-arrow-down-s-line icon transition-transform duration-300"></i>
                                    </div>
                                    <div class="details hidden"><?php echo buildEmployeeDetails($emp); ?></div>
                                </td>
                                <td class="p-3 border-b border-gray-200 text-center font-medium"><?php echo $days; ?></td>
                                <td class="p-3 border-b border-gray-200 text-right">LKR <?php echo number_format($rate, 2); ?></td>
                                <td class="p-3 border-b border-gray-200 text-right font-medium">LKR <?php echo number_format($earned, 2); ?></td>
                                <td class="p-3 border-b border-gray-200 text-right">LKR <?php echo number_format($paid, 2); ?></td>
                                <td class="p-3 border-b border-gray-200 text-right font-bold <?php echo $net >= 0 ? 'text-green-500' : 'text-red-500'; ?>">
                                    LKR <?php echo number_format($net, 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Fixed Rate Table -->
            <div class="table-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl mb-10 overflow-x-auto">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Fixed Salary Employees</h2>
                <table class="table w-full border-collapse text-base">
                    <thead>
                        <tr>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-left">Employee</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Basic</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">ETF (3%)</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">EPF Emp (8%)</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">EPF Comp (12%)</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Payable</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Paid</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fixed_rate_employees as $emp):
                            $basic     = floatval($emp['basic_salary'] ?? $emp['rate_amount'] ?? 0);
                            $etf       = $basic * 0.03;
                            $epfEmp    = $basic * 0.08;
                            $epfComp   = $basic * 0.12;
                            $payable   = $basic + $epfComp;
                            $paid      = array_sum($emp['paid_amount'] ?? [0,0,0]);
                            $net       = $payable - $etf - $epfEmp - $paid;
                        ?>
                            <tr>
                                <td class="p-3 border-b border-gray-200 align-top">
                                    <div class="collapsible cursor-pointer flex items-center gap-2 font-medium">
                                        <span class="total"><?php echo htmlspecialchars($emp['emp_name']); ?></span>
                                        <i class="ri-arrow-down-s-line icon transition-transform duration-300"></i>
                                    </div>
                                    <div class="details hidden"><?php echo buildEmployeeDetails($emp); ?></div>
                                </td>
                                <td class="p-3 border-b border-gray-200 text-right">LKR <?php echo number_format($basic, 2); ?></td>
                                <td class="p-3 border-b border-gray-200 text-right">LKR <?php echo number_format($etf, 2); ?></td>
                                <td class="p-3 border-b border-gray-200 text-right">LKR <?php echo number_format($epfEmp, 2); ?></td>
                                <td class="p-3 border-b border-gray-200 text-right">LKR <?php echo number_format($epfComp, 2); ?></td>
                                <td class="p-3 border-b border-gray-200 text-right font-medium">LKR <?php echo number_format($payable, 2); ?></td>
                                <td class="p-3 border-b border-gray-200 text-right">LKR <?php echo number_format($paid, 2); ?></td>
                                <td class="p-3 border-b border-gray-200 text-right font-bold <?php echo $net >= 0 ? 'text-green-500' : 'text-red-500'; ?>">
                                    LKR <?php echo number_format($net, 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Labor Wages Summary -->
            <div class="table-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl overflow-x-auto">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Labor Wages Summary</h2>
                <table class="table w-full border-collapse text-base">
                    <thead>
                        <tr>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-left">Labor Name</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-center">Total Days</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10 text-right">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($labor_wages_data['summations'] ?? [] as $sum):
                            $details = '';
                            foreach ($labor_wages_data['details'][$sum['labor_name']] ?? [] as $d) {
                                $details .= "<li>Job {$d['job_id']} | {$d['expensed_date']} | " . htmlspecialchars($d['description'] ?? '') . " | LKR " . number_format($d['expense_amount'] ?? 0, 2) . "</li>";
                            }
                            $details = $details ? "<ul class='list-disc pl-5'>$details</ul>" : "<p class='italic text-gray-500'>No details</p>";
                        ?>
                            <tr>
                                <td class="p-3 border-b border-gray-200 align-top">
                                    <div class="collapsible cursor-pointer flex items-center gap-2 font-medium">
                                        <span class="total"><?php echo htmlspecialchars($sum['labor_name'] ?? 'Unknown'); ?></span>
                                        <i class="ri-arrow-down-s-line icon transition-transform duration-300"></i>
                                    </div>
                                    <div class="details hidden p-3 bg-blue-50/50 rounded-lg mt-2"><?php echo $details; ?></div>
                                </td>
                                <td class="p-3 border-b border-gray-200 text-center font-medium"><?php echo $sum['total_days'] ?? 0; ?></td>
                                <td class="p-3 border-b border-gray-200 text-right font-bold">LKR <?php echo number_format($sum['total_amount'] ?? 0, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>

    <script>
        // Collapsible rows
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.collapsible').forEach(item => {
                item.addEventListener('click', () => {
                    const details = item.nextElementSibling;
                    item.classList.toggle('active');
                    details.classList.toggle('hidden');
                });
            });

            // Charts
            new Chart(document.getElementById('financialChart'), {
                type: 'bar',
                data: {
                    labels: ['Total', 'Daily', 'Fixed', 'EPF'],
                    datasets: [{
                        label: 'Amount (LKR)',
                        data: [<?php echo $total_wages; ?>, <?php echo $total_daily_wages; ?>, <?php echo $total_fixed_wages; ?>, <?php echo $epf_costs; ?>],
                        backgroundColor: 'rgba(30, 144, 255, 0.7)'
                    }]
                },
                options: { scales: { y: { beginAtZero: true } } }
            });

            new Chart(document.getElementById('distributionChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Daily Wages', 'Fixed Wages', 'EPF Cost'],
                    datasets: [{
                        data: [<?php echo $total_daily_wages; ?>, <?php echo $total_fixed_wages; ?>, <?php echo $epf_costs; ?>],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
                    }]
                }
            });

            new Chart(document.getElementById('topEarnersChart'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_column(array_slice($top_earners, 0, 10), 'emp_name')); ?>,
                    datasets: [{
                        label: 'Total Payment',
                        data: <?php echo json_encode(array_column(array_slice($top_earners, 0, 10), 'total_payment')); ?>,
                        backgroundColor: '#1d4ed8'
                    }]
                },
                options: { indexAxis: 'y', scales: { x: { beginAtZero: true } } }
            });
        });

        function downloadCSV() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form));
            params.set('download_csv', '1');
            location.href = location.pathname + '?' + params.toString();
        }

        function generatePDF() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form));
            params.set('generate_pdf', '1');
            location.href = location.pathname + '?' + params.toString();
        }
    </script>
</body>
</html>