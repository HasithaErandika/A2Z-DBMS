<?php
// src/controllers/ReportController.php

require_once 'src/core/Controller.php';

use App\Services\WageCalculationService;
use App\Services\ExportService;

class ReportController extends Controller {
    private $reportManager;
    private $wageService;
    private $exportService;

    public function __construct() {
        $this->reportManager = new ReportManager();
        $this->wageService = new WageCalculationService();
        $this->exportService = new ExportService();
    }

    public function records($request = null, $response = null) {
        $data = [
            'username' => $_SESSION['db_username'] ?? 'Admin',
            'dbname' => Database::getDatabaseName(),
            'reportCards' => [
                ['link' => BASE_PATH . '/reports/wages_report', 'icon' => 'fa-money-bill', 'title' => 'Monthly Wages', 'desc' => 'Wage summary'],
                ['link' => BASE_PATH . '/reports/expenses_report', 'icon' => 'fa-file-invoice-dollar', 'title' => 'Expenses Report', 'desc' => 'Expense analysis'],
                ['link' => BASE_PATH . '/reports/cost_calculation', 'icon' => 'fa-chart-pie', 'title' => 'Site Cost Calculation', 'desc' => 'Cost breakdown'],
                ['link' => BASE_PATH . '/reports/material_find', 'icon' => 'fa-cogs', 'title' => 'Material Cost Calculation', 'desc' => 'Material expenses'],
                ['link' => BASE_PATH . '/reports/a2z_engineering_jobs', 'icon' => 'fa-cogs', 'title' => 'A2Z Engineering Jobs', 'desc' => 'Job overview'],
                ['link' => BASE_PATH . '/reports/maintenance_report', 'icon' => 'fa-tools', 'title' => 'A2Z Maintenance', 'desc' => 'Maintenance schedule and tracking'],
            ]
        ];

        $this->render('admin/reports', $data);
    }

    public function wagesReport($request = null, $response = null) {
        if (!$request) $request = new App\Helpers\Request();
        if (!$response) $response = new App\Helpers\Response();

        try {
            $filters = [
                'emp_id' => $request->get('emp_id') ?? '',
                'from_date' => '',
                'to_date' => ''
            ];

            $start_date = $end_date = null;
            $report_title = "Wages Report (All Time)";

            // Date filtering logic
            if ($request->get('year') && $request->get('month') !== null && $request->get('month') !== '') {
                $year  = filter_var($request->get('year'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 2020, 'max_range' => 2030]]) ?: date('Y');
                $month = filter_var($request->get('month'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]]);
                if ($month === false) throw new Exception("Invalid month");
                $start_date = sprintf("%d-%02d-01", $year, $month);
                $end_date   = sprintf("%d-%02d-%d", $year, $month, date('t', mktime(0, 0, 0, $month, 1, $year)));
                $month_name = date('F', mktime(0, 0, 0, $month, 1, $year));
                $report_title = "Wages Report – $month_name $year";
            } elseif ($request->get('year')) {
                $year = filter_var($request->get('year'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 2020, 'max_range' => 2030]]) ?: date('Y');
                $start_date = "$year-01-01";
                $end_date   = "$year-12-31";
                $report_title = "Wages Report – $year";
            }

            $filters['from_date'] = $start_date;
            $filters['to_date']   = $end_date;

            // Fetch data
            $employee_refs      = $this->reportManager->getEmployeeRefs($start_date, $end_date) ?? [];
            $wage_data          = $this->reportManager->getWageData($filters) ?? [];
            $labor_wages_data   = $this->reportManager->getLaborWagesData($start_date, $end_date) ?? ['summations' => [], 'details' => []];
            $epf_costs          = $this->reportManager->getEPFCosts($start_date, $end_date) ?? 0;

            // Separate employees
            $daily_wage_employees = [];
            $fixed_rate_employees = [];
            foreach ($wage_data as $emp) {
                if (strtoupper($emp['rate_type'] ?? 'DAILY') === 'FIXED') {
                    $fixed_rate_employees[] = $emp;
                } else {
                    $daily_wage_employees[] = $emp;
                }
            }

            // Totals
            $total_daily_wages = array_sum(array_column($daily_wage_employees, 'total_payment'));
            $total_fixed_wages = array_sum(array_column($fixed_rate_employees, 'total_payment'));
            $total_advance_payments = 0;
            
            // Calculate total advance payments
            foreach ($wage_data as $emp) {
                $advance_paid = $emp['advance_details']['paid_amount'] ?? 0;
                $advance_deduction = $emp['advance_details']['deduction_amount'] ?? 0;
                $total_advance_payments += $advance_paid + $advance_deduction;
            }
            
            $total_wages       = $total_daily_wages + $total_fixed_wages + $epf_costs;
            $employee_count    = count($wage_data);
            $avg_wage_per_employee = $employee_count > 0 ? $total_wages / $employee_count : 0;

            // Calculate wage details using service class
            foreach ($wage_data as &$emp) {
                $advance_paid = $emp['advance_details']['paid_amount'] ?? 0;
                $advance_deduction = $emp['advance_details']['deduction_amount'] ?? 0;
                $advance_total = $advance_paid + $advance_deduction;
                $paidArray = $emp['paid_amount'] ?? [0];

                if (strtoupper($emp['rate_type'] ?? 'DAILY') === 'FIXED') {
                    $basic = floatval($emp['basic_salary'] ?? $emp['rate_amount'] ?? 0);
                    $result = $this->wageService->calculateFixedSalary($basic, $advance_total, $paidArray);
                    $emp['net_payable'] = $result['net_payable'];
                } else {
                    $days = $emp['attendance_summary']['presence_count'] ?? 0;
                    $rate = floatval($emp['rate_amount'] ?? 0);
                    $result = $this->wageService->calculateDailyWage($days, $rate, $advance_total, $paidArray);
                    $emp['net_payable'] = $result['net_payable'];
                }
            }
            unset($emp);
            
            // Sort by net payable for Top 10 earners
            usort($wage_data, fn($a, $b) => ($b['net_payable'] ?? 0) <=> ($a['net_payable'] ?? 0));
            $top_earners = array_slice($wage_data, 0, 10);

            // PDF Salary Slips (All or Single)
            if ($request->get('generate_pdf')) {
                $this->exportService->generateWagesPDF($report_title, $wage_data);
                exit;
            }

            $data = [
                'report_title'           => $report_title,
                'employee_refs'          => $employee_refs,
                'wage_data'              => $wage_data,
                'daily_wage_employees'   => $daily_wage_employees,
                'fixed_rate_employees'   => $fixed_rate_employees,
                'labor_wages_data'       => $labor_wages_data,
                'total_wages'            => $total_wages,
                'total_daily_wages'      => $total_daily_wages,
                'total_fixed_wages'      => $total_fixed_wages,
                'total_advance_payments' => $total_advance_payments,
                'epf_costs'              => $epf_costs,
                'employee_count'         => $employee_count,
                'avg_wage_per_employee'  => $avg_wage_per_employee,
                'top_earners'            => $top_earners,
                'filters'                => [
                    'year'   => $request->get('year') ?? date('Y'),
                    'month'  => $request->get('month') ?? '',
                    'emp_id' => $request->get('emp_id') ?? ''
                ]
            ];

            $this->render('reports/wages_report', $data);

        } catch (Exception $e) {
            error_log("Wages Report Error: " . $e->getMessage());
            $this->render('reports/wages_report', ['error' => $e->getMessage()]);
        }
    }

    public function expenseReport($request = null, $response = null) {
        if (!$request) $request = new App\Helpers\Request();
        if (!$response) $response = new App\Helpers\Response();

        try {
            $start_date = null;
            $end_date = null;
            $report_title = "Full Company Expense Report (All Time)";

            if ($request->isPost() && $request->post('year') && $request->post('month')) {
                $year = filter_var($request->post('year'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 2020, 'max_range' => 2035]]);
                $month = filter_var($request->post('month'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]]);
                if ($year === false || $month === false) {
                    throw new Exception("Invalid year or month selected.");
                }
                $start_date = sprintf("%d-%02d-01", $year, $month);
                $end_date   = sprintf("%d-%02d-%d", $year, $month, date('t', mktime(0, 0, 0, $month, 1, $year)));
                $month_name = date('F', mktime(0, 0, 0, $month, 1, $year));
                $report_title = "Company Expense Report for $month_name $year";
            } elseif ($request->isPost() && $request->post('year')) {
                $year = filter_var($request->post('year'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 2020, 'max_range' => 2035]]);
                if ($year === false) throw new Exception("Invalid year selected.");
                $start_date = "$year-01-01";
                $end_date   = "$year-12-31";
                $report_title = "Company Expense Report for $year";
            }

            // ── Fetch raw data ───────────────────────────────────────────────────────
            $expenses_data     = $this->reportManager->getExpensesByCategory($start_date, $end_date);
            $invoices_data     = $this->reportManager->getInvoicesSummary($start_date, $end_date);
            $jobs_data         = $this->reportManager->getJobsSummary($start_date, $end_date);
            $attendance_data   = $this->reportManager->getEmployeeAttendanceCosts(null, $start_date, $end_date);
            $statutory         = $this->reportManager->getStatutoryBreakdown($start_date, $end_date);
            $detailed_rows     = $this->reportManager->getDetailedExpenseRows($start_date, $end_date);

            // ── Operational expenses by category (excluding Hiring of Labor) ────────
            $total_operational  = 0.0;
            $expenses_by_category = [];
            $hiring_labor_cost   = 0.0;

            foreach ($expenses_data as $row) {
                $category = $row['expenses_category'];
                $amount   = floatval($row['total_expenses']);
                if (strcasecmp($category, 'Hiring of Labor') === 0) {
                    $hiring_labor_cost += $amount;
                } else {
                    $expenses_by_category[$category] = ($expenses_by_category[$category] ?? 0) + $amount;
                    $total_operational += $amount;
                }
            }

            // ── Attendance-based employee costs ──────────────────────────────────────
            $attendance_cost = 0.0;
            foreach ($attendance_data as $row) {
                if (floatval($row['presence'] ?? 0) > 0) {
                    $attendance_cost += floatval($row['actual_cost'] ?? 0);
                }
            }

            // ── Statutory: EPF employer (12%) + ETF (3%) ────────────────────────────
            $total_epf_employer = $statutory['total_epf'];   // company pays 12%
            $total_etf          = $statutory['total_etf'];   // company pays 3%
            $total_statutory    = $statutory['total'];       // EPF+ETF combined
            $statutory_rows     = $statutory['rows'];

            // ── Total employee/labor costs ────────────────────────────────────────────
            $total_employee_costs = $attendance_cost + $hiring_labor_cost + $total_statutory;

            // ── Material Costs for A2Z Engineering Jobs ──────────────────────────────
            $total_material_cost  = $this->reportManager->getMaterialCostSummary($start_date, $end_date);

            // ── Revenue ──────────────────────────────────────────────────────────────
            $total_invoices       = floatval($invoices_data['total_invoices'] ?? 0);
            $total_invoices_count = intval($invoices_data['invoice_count'] ?? 0);
            $total_jobs           = intval($jobs_data['job_count'] ?? 0);
            $total_job_capacity   = floatval($jobs_data['total_capacity'] ?? 0);

            // ── Profit = Revenue − (Operational + Employee/Labor + Statutory + Materials) ──
            $total_costs = $total_operational + $total_employee_costs + $total_material_cost;
            $profit      = $total_invoices - $total_costs;

            // ── Build the unified breakdown for the doughnut chart ───────────────────
            // This merges operational categories + labor cost + statutory + materials into one map
            $full_breakdown = $expenses_by_category;
            if ($total_material_cost > 0) {
                $full_breakdown['Site Material Costs'] = $total_material_cost;
            }
            if ($hiring_labor_cost > 0) {
                $full_breakdown['Hiring of Labor']    = $hiring_labor_cost;
            }
            if ($attendance_cost > 0) {
                $full_breakdown['Attendance Wages']   = $attendance_cost;
            }
            if ($total_epf_employer > 0) {
                $full_breakdown['EPF (Employer 12%)'] = $total_epf_employer;
            }
            if ($total_etf > 0) {
                $full_breakdown['ETF (3%)']           = $total_etf;
            }

            // Employee costs sub-breakdown for the employee costs table
            $employee_costs_by_type = [
                'Attendance-Based Wages' => $attendance_cost,
                'Hiring of Labor'        => $hiring_labor_cost,
                'EPF Employer (12%)'     => $total_epf_employer,
                'ETF (3%)'               => $total_etf,
            ];

            $data = [
                'username'              => $_SESSION['db_username'] ?? 'Admin',
                'dbname'                => Database::getDatabaseName(),
                'report_title'          => $report_title,
                'total_operational'     => $total_operational,
                'total_material_cost'   => $total_material_cost,
                'total_expenses'        => $total_operational,   // kept for view compat
                'total_invoices'        => $total_invoices,
                'total_invoices_count'  => $total_invoices_count,
                'total_jobs'            => $total_jobs,
                'total_job_capacity'    => $total_job_capacity,
                'total_employee_costs'  => $total_employee_costs,
                'total_epf_employer'    => $total_epf_employer,
                'total_etf'             => $total_etf,
                'total_statutory'       => $total_statutory,
                'profit'                => $profit,
                'total_costs'           => $total_costs,
                'expenses_by_category'  => $expenses_by_category,
                'full_breakdown'        => $full_breakdown,
                'employee_costs_by_type'=> $employee_costs_by_type,
                'statutory_rows'        => $statutory_rows,
                'detailed_rows'         => $detailed_rows,
                'filters'               => [
                    'year'  => $request->post('year')  ?? '',
                    'month' => $request->post('month') ?? '',
                ],
            ];

            if ($request->get('download_csv')) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="expense_report_' . date('Y-m-d') . '.csv"');
                $output = fopen('php://output', 'w');
                fputcsv($output, [$report_title]);
                fputcsv($output, ['']);
                fputcsv($output, ['Financial Overview', 'Value (LKR)']);
                fputcsv($output, ['Total Revenue (Invoices)', number_format($total_invoices, 2)]);
                fputcsv($output, ['Total Operational Expenses', number_format($total_operational, 2)]);
                fputcsv($output, ['Total Employee & Labor Costs', number_format($total_employee_costs, 2)]);
                fputcsv($output, ['  Attendance-Based Wages', number_format($attendance_cost, 2)]);
                fputcsv($output, ['  Hiring of Labor', number_format($hiring_labor_cost, 2)]);
                fputcsv($output, ['  EPF Employer (12%)', number_format($total_epf_employer, 2)]);
                fputcsv($output, ['  ETF (3%)', number_format($total_etf, 2)]);
                fputcsv($output, ['Net Profit / (Loss)', number_format($profit, 2)]);
                fputcsv($output, ['']);
                fputcsv($output, ['Operational Expenses by Category', 'Amount (LKR)']);
                foreach ($expenses_by_category as $category => $amount) {
                    fputcsv($output, [$category, number_format($amount, 2)]);
                }
                fputcsv($output, ['']);
                fputcsv($output, ['Statutory Obligations per Employee', 'Basic Salary', 'EPF Employee (8%)', 'EPF Employer (12%)', 'ETF (3%)', 'Total Statutory']);
                foreach ($statutory_rows as $sr) {
                    fputcsv($output, [
                        $sr['emp_name'],
                        number_format($sr['basic_salary'], 2),
                        number_format($sr['epf_employee'], 2),
                        number_format($sr['epf_employer'], 2),
                        number_format($sr['etf'], 2),
                        number_format($sr['total_statutory'], 2),
                    ]);
                }
                fputcsv($output, ['']);
                fputcsv($output, ['Expense Transactions (Auditor Detail)', 'Date', 'Category', 'Description', 'Amount', 'Voucher #', 'Paid', 'Employee', 'Job Location', 'Company']);
                foreach ($detailed_rows as $dr) {
                    fputcsv($output, [
                        $dr['expense_id'], $dr['expensed_date'], $dr['expenses_category'],
                        $dr['description'], number_format($dr['expense_amount'], 2),
                        $dr['voucher_number'], $dr['paid'] ? 'Yes' : 'No',
                        $dr['emp_name'], $dr['job_location'], $dr['company_reference'],
                    ]);
                }
                fclose($output);
                exit();
            }

            $this->render('reports/expenses_report', $data);
        } catch (Exception $e) {
            error_log("Error in expenseReport: " . $e->getMessage());
            $this->render('reports/expenses_report', [
                'username' => $_SESSION['db_username'] ?? 'Admin',
                'dbname'   => Database::getDatabaseName(),
                'error'    => "Error generating report: " . $e->getMessage()
            ]);
        }
    }


    public function costCalculation($request = null, $response = null) {
        if (!$request) $request = new App\Helpers\Request();
        if (!$response) $response = new App\Helpers\Response();

        try {
            $filters = [
                'invoice_no' => $request->get('invoice_id') ?? '',
                'customer_reference' => $request->get('customer_name') ?? '',
                'company_reference' => $request->get('client_ref') ?? '',
                'status' => $request->get('status') ?? '',
                'from_date' => $request->get('from_date') ?? '',
                'to_date' => $request->get('to_date') ?? '',
                'completion' => $request->get('completion') ?? ''
            ];

            $allFilters = [
                'invoice_no' => '',
                'customer_reference' => '',
                'company_reference' => '',
                'status' => '',
                'from_date' => '1900-01-01',
                'to_date' => date('Y-m-d'),
                'completion' => ''
            ];
            $allJobData = $this->reportManager->getJobCostData($allFilters, PHP_INT_MAX, 0);
            
            // Group allJobData to handle multiple invoices per job
            $groupedAllJobData = [];
            foreach ($allJobData as $row) {
                $jobId = $row['job_id'];
                if (!isset($groupedAllJobData[$jobId])) {
                    $groupedAllJobData[$jobId] = $row;
                    $groupedAllJobData[$jobId]['invoices'] = [];
                    $groupedAllJobData[$jobId]['invoice_value'] = 0;
                    $groupedAllJobData[$jobId]['received_amount'] = 0;
                }
                if (!is_null($row['invoice_no'])) {
                    $groupedAllJobData[$jobId]['invoices'][] = [
                        'no' => $row['invoice_no'],
                        'value' => floatval($row['invoice_value'] ?? 0),
                        'received' => floatval($row['received_amount'] ?? 0),
                        'date_paid' => $row['payment_received_date'] ?? ''
                    ];
                    $groupedAllJobData[$jobId]['invoice_value'] += floatval($row['invoice_value'] ?? 0);
                    $groupedAllJobData[$jobId]['received_amount'] += floatval($row['received_amount'] ?? 0);
                }
            }
            $groupedAllJobData = array_values($groupedAllJobData);
            $overall_unpaid_count = 0;
            foreach ($groupedAllJobData as $row) {
                $has_invoice = !empty($row['invoices']);
                $outstanding = $row['invoice_value'] - $row['received_amount'];
                if ($outstanding > 0 || !$has_invoice) {
                    $overall_unpaid_count++;
                }
            }

            $customerRefs = $this->reportManager->getCustomerRefs();
            $companyRefs = $this->reportManager->getCompanyRefs();
            $jobData = $this->reportManager->getJobCostData($filters, PHP_INT_MAX, 0);

            // Group jobData to handle multiple invoices per job
            $groupedJobData = [];
            foreach ($jobData as $row) {
                $jobId = $row['job_id'];
                if (!isset($groupedJobData[$jobId])) {
                    $groupedJobData[$jobId] = $row;
                    $groupedJobData[$jobId]['invoices'] = [];
                    $groupedJobData[$jobId]['invoice_value'] = 0;
                    $groupedJobData[$jobId]['received_amount'] = 0;
                }
                if (!is_null($row['invoice_no'])) {
                    $groupedJobData[$jobId]['invoices'][] = [
                        'no' => $row['invoice_no'],
                        'value' => floatval($row['invoice_value'] ?? 0),
                        'received' => floatval($row['received_amount'] ?? 0),
                        'date_paid' => $row['payment_received_date'] ?? ''
                    ];
                    $groupedJobData[$jobId]['invoice_value'] += floatval($row['invoice_value'] ?? 0);
                    $groupedJobData[$jobId]['received_amount'] += floatval($row['received_amount'] ?? 0);
                }
            }
            $jobData = array_values($groupedJobData);
            $overallSummary = $this->reportManager->getOverallSummary();

            $totalInvoiceAmount = 0;
            $totalPaidAmount = 0;
            $totalUnpaidAmount = 0;
            $unpaidInvoiceCount = 0;
            $totalExpenses = 0;
            $totalEmployeeCostsSum = 0;
            $totalCapacity = 0;
            $totalNetProfit = 0;
            $companyStats = [];
            $totalMaterialCostsOverall = 0.0;
            $materialCostsMap = $this->reportManager->getJobMaterialCosts();

            foreach ($jobData as &$row) {
                $row['invoice_no'] = implode(', ', array_column($row['invoices'], 'no'));
                $operationalExpenses = [];
                $hiringLaborCost = 0;
                if ($row['expense_summary'] !== 'No expenses') {
                    foreach (explode(', ', $row['expense_summary']) as $expense) {
                        $parts = explode(': ', $expense);
                        if (count($parts) === 2) {
                            $category = trim($parts[0]);
                            $amount = floatval(trim($parts[1]));
                            if (strcasecmp($category, 'Hiring of Labor') === 0) {
                                $hiringLaborCost += $amount;
                            } else {
                                $operationalExpenses[$category] = ($operationalExpenses[$category] ?? 0) + $amount;
                            }
                        }
                    }
                }
                $row['operational_expenses'] = array_sum($operationalExpenses);
                $row['expense_details'] = $operationalExpenses;

                $materialCost = $materialCostsMap[$row['job_id']] ?? 0.0;
                $row['material_cost'] = $materialCost;
                $totalMaterialCostsOverall += $materialCost;

                // Format material details HTML
                $materialDetails = "<ul class='list-disc pl-5 space-y-1 text-slate-500'>";
                $materialItems = $this->reportManager->getMaterialsForJob($row['job_id']);
                if (!empty($materialItems)) {
                    foreach ($materialItems as $item) {
                        $materialDetails .= "<li><strong>" . htmlspecialchars($item['material_name'], ENT_QUOTES, 'UTF-8') . "</strong> (" . floatval($item['quantity']) . " @ " . number_format($item['unit_price'], 2) . ") + " . floatval($item['profit_margin']) . "% Margin = LKR " . number_format($item['final_price'], 2) . "</li>";
                    }
                } else {
                    $materialDetails .= "<li>No materials recorded</li>";
                }
                $materialDetails .= "</ul>";
                $row['material_details_html'] = $materialDetails;

                $employeeCosts = $this->reportManager->getEmployeeAttendanceCosts($row['job_id']);
                $totalEmployeeCosts = $hiringLaborCost;
                $row['employee_details'] = [];
                if ($hiringLaborCost > 0) {
                    $row['employee_details'][] = [
                        'emp_name' => 'Hiring of Labor',
                        'payment' => $hiringLaborCost,
                        'days' => []
                    ];
                }

                $employeeBreakdown = [];
                foreach ($employeeCosts as $cost) {
                    $empName = $cost['emp_name'];
                    $attendanceDate = $cost['attendance_date'];
                    if (!isset($employeeBreakdown[$empName])) {
                        $employeeBreakdown[$empName] = [
                            'emp_name' => $empName,
                            'payment' => 0,
                            'days' => [],
                            'days_by_date' => []
                        ];
                    }
                    $presence = floatval($cost['presence'] ?? 0);
                    $payment = floatval($cost['actual_cost'] ?? 0);

                    if (!isset($employeeBreakdown[$empName]['days_by_date'][$attendanceDate])) {
                        $employeeBreakdown[$empName]['payment'] += $payment;
                        $employeeBreakdown[$empName]['days'][] = [
                            'date' => $cost['attendance_date'],
                            'presence' => $presence,
                            'payment' => $payment,
                        ];
                        $employeeBreakdown[$empName]['days_by_date'][$attendanceDate] = true;
                        $totalEmployeeCosts += $payment;
                    }
                }

                foreach ($employeeBreakdown as $emp) {
                    unset($emp['days_by_date']);
                    $row['employee_details'][] = $emp;
                }
                $row['total_employee_costs'] = $totalEmployeeCosts;

                $has_invoice = !empty($row['invoices']);
                $invoiceValue = floatval($row['invoice_value']);
                $receivedAmount = floatval($row['received_amount']);
                $outstanding = $invoiceValue - $receivedAmount;

                $totalInvoiceAmount += $invoiceValue;
                $totalPaidAmount += $receivedAmount;
                $totalUnpaidAmount += $outstanding;
                if ($outstanding > 0 || !$has_invoice) {
                    $unpaidInvoiceCount++;
                }

                $totalExpenses += $row['operational_expenses'];
                $totalEmployeeCostsSum += $totalEmployeeCosts;
                $totalCapacity += floatval($row['job_capacity'] ?? 0);
                $netProfit = $invoiceValue - ($totalEmployeeCosts + $row['operational_expenses'] + $materialCost);
                $row['net_profit'] = $netProfit;
                $totalNetProfit += $netProfit;

                // Company stats
                $ref = $row['company_reference'] ?? 'Unknown';
                if (!isset($companyStats[$ref])) {
                    $companyStats[$ref] = [
                        'Completed' => 0, 'Ongoing' => 0, 'Started' => 0,
                        'Not Started' => 0, 'Cancelled' => 0, 'net_profit' => 0
                    ];
                }
                $status = $row['completion_status'] ?? 'Unknown';
                if (array_key_exists($status, $companyStats[$ref])) {
                    $companyStats[$ref][$status]++;
                }
                $companyStats[$ref]['net_profit'] += $netProfit;
            }
            unset($row);

            $dueBalance = $totalUnpaidAmount;
            $profitMargin = $totalInvoiceAmount > 0 ? ($totalNetProfit / $totalInvoiceAmount) * 100 : 0;
            $overallDueBalance = floatval($overallSummary['total_invoices'] ?? 0) - floatval($overallSummary['total_paid'] ?? 0);

            // Group jobs
            $job_groups = ['Completed' => [], 'Ongoing' => [], 'Started' => [], 'Not Started' => [], 'Cancelled' => []];
            foreach ($jobData as $row) {
                $status = $row['completion_status'] ?? 'Unknown';
                if (isset($job_groups[$status])) {
                    $job_groups[$status][] = $row;
                }
            }

            foreach ($job_groups as $status => &$jobs) {
                usort($jobs, fn($a, $b) => strcmp($a['company_reference'] ?? '', $b['company_reference'] ?? ''));
            }
            unset($jobs);

            $data = [
                'username' => $_SESSION['db_username'] ?? 'Admin',
                'dbname' => Database::getDatabaseName(),
                'customer_refs' => $customerRefs,
                'company_refs' => $companyRefs,
                'job_data' => $jobData,
                'job_groups' => $job_groups,
                'total_invoice_amount' => $totalInvoiceAmount,
                'total_paid_amount' => $totalPaidAmount,
                'total_unpaid_amount' => $totalUnpaidAmount,
                'unpaid_invoice_count' => $unpaidInvoiceCount,
                'due_balance' => $dueBalance,
                'total_expenses' => $totalExpenses,
                'total_material_cost' => $totalMaterialCostsOverall,
                'total_employee_costs_sum' => $totalEmployeeCostsSum,
                'total_capacity' => $totalCapacity,
                'total_net_profit' => $totalNetProfit,
                'profit_margin' => $profitMargin,
                'overall_invoice_amount' => floatval($overallSummary['total_invoices'] ?? 0),
                'overall_paid_amount' => floatval($overallSummary['total_paid'] ?? 0),
                'overall_unpaid_amount' => $overallDueBalance,
                'overall_unpaid_count' => $overall_unpaid_count,
                'overall_due_balance' => $overallDueBalance,
                'filters' => $filters,
                'company_stats' => $companyStats
            ];

            if ($request->get('download_csv')) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="cost_calculation_' . date('Y-m-d') . '.csv"');
                $output = fopen('php://output', 'w');
                fputcsv($output, [
                    'Job ID', 'Date', 'Customer', 'Location', 'Company Ref', 'Engineer', 'Capacity',
                    'Invoice No', 'Invoice Value', 'Received', 'Date Paid', 'Expenses',
                    'Employee Costs', 'Outstanding', 'Net Profit', 'Completion Status'
                ]);
                foreach ($jobData as $row) {
                    $outstanding = floatval($row['invoice_value'] ?? 0) - floatval($row['received_amount'] ?? 0);
                    fputcsv($output, [
                        $row['job_id'], $row['date_completed'], $row['customer_reference'], $row['location'],
                        $row['company_reference'] ?? 'N/A', $row['engineer'] ?? 'N/A', $row['job_capacity'],
                        $row['invoice_no'], $row['invoice_value'], $row['received_amount'], $row['payment_received_date'],
                        $row['operational_expenses'], $row['total_employee_costs'], $outstanding, $row['net_profit'],
                        $row['completion_status'] ?? 'Unknown'
                    ]);
                }
                fclose($output);
                exit;
            }

            $this->render('reports/cost_calculation', $data);
        } catch (Exception $e) {
            error_log("Error in costCalculation: " . $e->getMessage());
            $this->render('reports/cost_calculation', [
                'username' => $_SESSION['db_username'] ?? 'Admin',
                'dbname' => Database::getDatabaseName(),
                'error' => "Error generating report: " . $e->getMessage()
            ]);
        }
    }

    public function materialFind($request = null, $response = null) {
        if (!$request) $request = new App\Helpers\Request();
        if (!$response) $response = new App\Helpers\Response();

        $action = trim($request->get('action') ?: ($request->post('action') ?: ''));
        $jobId  = intval($request->get('job_id') ?: ($request->post('job_id') ?: 0));
        $error   = null;
        $success = null;
        $importResult = null;

        // ── Template download (GET) ─────────────────────────────────────────
        if ($action === 'download_template') {
            require_once __DIR__ . '/../../vendor/autoload.php';
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Materials');
            $labels  = ['Material Name', 'Quantity', 'Unit Price (LKR)', 'Profit Margin (%)'];
            foreach ($labels as $col => $label) {
                $cell = chr(65 + $col) . '1';
                $sheet->setCellValue($cell, $label);
                $sheet->getStyle($cell)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '1E3A5F']],
                    'alignment' => ['horizontal' => 'center'],
                ]);
                $sheet->getColumnDimensionByColumn($col + 1)->setAutoSize(true);
            }
            $samples = [
                ['Solis 5kW Hybrid Inverter', 1, 380000, 15],
                ['Jinko 550W Solar Panel',    9,  65000, 15],
                ['Mounting Structure & Bolts',1,  85000, 15],
                ['AC/DC Distribution Board',  1,  75000, 12],
                ['Premium AC & DC Cables',    1,  95000, 15],
            ];
            foreach ($samples as $r => $row) {
                foreach ($row as $c => $val) {
                    $sheet->setCellValue(chr(65 + $c) . ($r + 2), $val);
                }
            }
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="A2Z_Material_Import_Template.xlsx"');
            header('Cache-Control: max-age=0');
            (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save('php://output');
            exit;
        }

        // ── POST actions ────────────────────────────────────────────────────
        if ($request->isPost()) {

            if ($action === 'import') {
                if ($jobId <= 0) {
                    $error = "Please select a job before importing.";
                } elseif (empty($_FILES['excel_file']['tmp_name'])) {
                    $error = "No file uploaded. Please choose an Excel (.xlsx, .xls) or CSV file.";
                } else {
                    $file    = $_FILES['excel_file'];
                    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $allowed = ['xlsx', 'xls', 'csv'];

                    if (!in_array($ext, $allowed)) {
                        $error = "Unsupported file type '.{$ext}'. Please upload .xlsx, .xls or .csv.";
                    } elseif ($file['size'] > 5 * 1024 * 1024) {
                        $error = "File too large (max 5 MB).";
                    } else {
                        require_once __DIR__ . '/../../vendor/autoload.php';
                        try {
                            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file['tmp_name']);
                            $reader->setReadDataOnly(true);
                            $spreadsheet = $reader->load($file['tmp_name']);
                            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

                            if (count($rows) < 2) {
                                $error = "The file has no data rows (only a header row, or is empty).";
                            } else {
                                // Normalise headers: lowercase, collapse runs of non-alphanumeric into one _, trim edge _
                                $rawHeaders = array_map(
                                    fn($h) => trim(strtolower(preg_replace('/[^a-z0-9]+/i', '_', trim($h ?? ''))), '_'),
                                    $rows[0]
                                );
                                $aliasMap = [
                                    'material_name' => ['material_name','material','name','item','item_name','description'],
                                    'quantity'      => ['quantity','qty','amount'],
                                    'unit_price'    => ['unit_price','unit_price_lkr','price','unit_cost','cost','lkr'],
                                    'profit_margin' => ['profit_margin','margin','profit'],
                                ];
                                $colMap = [];
                                foreach ($aliasMap as $canonical => $aliases) {
                                    foreach ($rawHeaders as $idx => $h) {
                                        if (in_array($h, $aliases, true)) {
                                            $colMap[$canonical] = $idx;
                                            break;
                                        }
                                    }
                                }
                                $missing = array_diff(array_keys($aliasMap), array_keys($colMap));
                                if (!empty($missing)) {
                                    $error = "Missing required columns: " . implode(', ', $missing) .
                                             ". Expected headers: Material Name, Quantity, Unit Price (LKR), Profit Margin (%).";
                                } else {
                                    $dataRows = [];
                                    for ($i = 1; $i < count($rows); $i++) {
                                        $r = $rows[$i];
                                        if (empty(array_filter($r, fn($v) => $v !== null && $v !== ''))) continue;
                                        $dataRows[] = [
                                            'material_name' => $r[$colMap['material_name']] ?? '',
                                            'quantity'      => $r[$colMap['quantity']] ?? 0,
                                            'unit_price'    => $r[$colMap['unit_price']] ?? 0,
                                            'profit_margin' => $r[$colMap['profit_margin']] ?? 0,
                                        ];
                                    }
                                    if (empty($dataRows)) {
                                        $error = "No valid data rows found in the file.";
                                    } else {
                                        $importResult = $this->reportManager->importJobMaterials($jobId, $dataRows);
                                        if ($importResult['inserted'] > 0) {
                                            $success = "Import complete — {$importResult['inserted']} item(s) added" .
                                                       ($importResult['skipped'] > 0 ? ", {$importResult['skipped']} skipped." : ".");
                                        } else {
                                            $error = "No items were imported. " . implode(' ', $importResult['errors'] ?? []);
                                        }
                                    }
                                }
                            }
                        } catch (\Throwable $ex) {
                            $error = "Could not read file: " . $ex->getMessage();
                            error_log("Excel import error: " . $ex->getMessage());
                        }
                    }
                }

            } elseif ($action === 'add') {
                $name      = trim($request->post('material_name') ?? '');
                $qty       = floatval($request->post('quantity') ?? 0);
                $unitPrice = floatval($request->post('unit_price') ?? 0);
                $margin    = floatval($request->post('profit_margin') ?? 0);

                if (!empty($name) && $qty > 0 && $unitPrice > 0 && $jobId > 0) {
                    if ($this->reportManager->addJobMaterialItem($jobId, $name, $qty, $unitPrice, $margin)) {
                        $success = "Material item added successfully!";
                    } else {
                        $error = "Failed to add material item.";
                    }
                } else {
                    $error = "Please fill in all required fields with valid numbers.";
                }

            } elseif ($action === 'update') {
                $itemId    = intval($request->post('item_id') ?? 0);
                $qty       = floatval($request->post('quantity') ?? 0);
                $unitPrice = floatval($request->post('unit_price') ?? 0);
                $margin    = floatval($request->post('profit_margin') ?? 0);
                $isAjax    = intval($request->get('ajax') ?: ($request->post('ajax') ?: 0)) === 1;

                if ($itemId > 0 && $qty > 0 && $unitPrice > 0) {
                    if ($this->reportManager->updateJobMaterialItem($itemId, $qty, $unitPrice, $margin)) {
                        if ($isAjax) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => true, 'message' => 'Material item updated successfully!']);
                            exit;
                        }
                        $success = "Material item updated successfully!";
                    } else {
                        if ($isAjax) {
                            header('Content-Type: application/json');
                            http_response_code(500);
                            echo json_encode(['success' => false, 'message' => 'Failed to update material item.']);
                            exit;
                        }
                        $error = "Failed to update material item.";
                    }
                } else {
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        http_response_code(400);
                        echo json_encode(['success' => false, 'message' => 'Invalid quantity or unit price.']);
                        exit;
                    }
                    $error = "Invalid quantity or unit price.";
                }

            } elseif ($action === 'delete') {
                $itemId = intval($request->post('item_id') ?? 0);
                if ($itemId > 0) {
                    if ($this->reportManager->deleteJobMaterialItem($itemId)) {
                        $success = "Material item deleted successfully!";
                    } else {
                        $error = "Failed to delete material item.";
                    }
                }

            } elseif ($action === 'clear') {
                if ($jobId > 0) {
                    if ($this->reportManager->clearJobMaterialItems($jobId)) {
                        $success = "All material items cleared for this job!";
                    } else {
                        $error = "Failed to clear material items.";
                    }
                }

            } elseif ($action === 'prepopulate') {
                if ($jobId > 0) {
                    $this->reportManager->clearJobMaterialItems($jobId);
                    $items = [
                        ['Solis 5kW Single Phase Hybrid Inverter (with WiFi/LAN)', 1, 380000.0, 15.0],
                        ['Jinko 550W Mono Facial Solar Panels', 9, 65000.0, 15.0],
                        ['Complete Aluminum Mounting Structure & Hanger Bolts', 1, 85000.0, 15.0],
                        ['AC/DC Distribution Board with SPDs, MCBs & Enclosure', 1, 75000.0, 15.0],
                        ['Premium AC & DC Cables (6mm2, 4mm2) & Conduits', 1, 95000.0, 15.0],
                        ['Copper Earth Rod, Earth Pit & Grounding Accessories', 1, 35000.0, 15.0],
                        ['Site Engineering Labor, Testing & Grid Connection Application Fee', 1, 120000.0, 10.0],
                        ['Transport, Site Logistics, Meals & Accommodation', 1, 45000.0, 0.0],
                    ];
                    $ok = true;
                    foreach ($items as $item) {
                        if (!$this->reportManager->addJobMaterialItem($jobId, $item[0], $item[1], $item[2], $item[3])) {
                            $ok = false;
                        }
                    }
                    $success = $ok ? "Standard 5kW Solar Hybrid Package loaded successfully!"
                                   : "Some package items failed to load.";
                }

            } elseif ($action === 'set_selling_price') {
                $rawPrice = $request->post('selling_price');
                if ($jobId > 0) {
                    // Allow clearing (empty string = null)
                    $price = ($rawPrice !== null && $rawPrice !== '') ? floatval($rawPrice) : null;
                    if ($this->reportManager->setJobSellingPrice($jobId, $price)) {
                        $success = $price !== null
                            ? 'Total system selling price set to LKR ' . number_format($price, 2) . '.'
                            : 'Selling price cleared.';
                    } else {
                        $error = 'Failed to save selling price.';
                    }
                }
            }
        }

        // ── Render ──────────────────────────────────────────────────────────
        $jobsList    = $this->reportManager->getActiveJobsList();
        $materials   = [];
        $selectedJob = null;

        $sellingPrice = null;
        if ($jobId > 0) {
            $materials    = $this->reportManager->getMaterialsForJob($jobId);
            $sellingPrice = $this->reportManager->getJobSellingPrice($jobId);
            foreach ($jobsList as $j) {
                if (intval($j['job_id']) === $jobId) { $selectedJob = $j; break; }
            }
        }

        $this->render('reports/material_find', [
            'username'      => $_SESSION['db_username'] ?? 'Admin',
            'dbname'        => Database::getDatabaseName(),
            'jobs_list'     => $jobsList,
            'materials'     => $materials,
            'job_id'        => $jobId,
            'selected_job'  => $selectedJob,
            'selling_price' => $sellingPrice,
            'error'         => $error,
            'success'       => $success,
            'import_result' => $importResult,
        ]);
    }

    public function a2zEngineeringJobs($request = null, $response = null) {
        if (!$request) $request = new App\Helpers\Request();
        $data = [
            'username' => $_SESSION['db_username'] ?? 'Admin',
            'dbname' => Database::getDatabaseName(),
        ];
        $this->render('reports/a2z_engineering_jobs', $data);
    }

    public function maintenanceReport($request = null, $response = null) {
        if (!$request) $request = new App\Helpers\Request();
        if (!$response) $response = new App\Helpers\Response();

        try {
            $filters = [
                'job_id' => $request->get('job_id') ?? '',
                'customer_reference' => $request->get('customer_name') ?? '',
                'company_reference' => $request->get('client_ref') ?? '',
                'completion' => '1.0',
                'project_id' => 5
            ];
            
            $jobs = $this->reportManager->getJobsWithMaintenanceData($filters);

            $selectedYear = $request->get('year') !== null && $request->get('year') !== '' ? intval($request->get('year')) : null;
            $selectedMonth = $request->get('month') !== null && $request->get('month') !== '' ? intval($request->get('month')) : null;

            $jobIds = array_map(fn($j) => $j['job_id'], $jobs);
            $maintenanceByJob = [];
            if (!empty($jobIds)) {
                $placeholders = implode(',', array_fill(0, count($jobIds), '?'));
                $db = Database::getInstance($_SESSION['db_username'], $_SESSION['db_password']);
                $pdo = $db->getConnection();
                $stmt = $pdo->prepare("SELECT schedule_id, job_id, cycle_number, DATE_FORMAT(scheduled_date, '%Y-%m-%d') AS scheduled_date, DATE_FORMAT(actual_date, '%Y-%m-%d') AS actual_date, status, description FROM maintenance_schedule WHERE job_id IN ($placeholders) ORDER BY job_id, cycle_number");
                $stmt->execute($jobIds);
                $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($schedules as $s) {
                    if ($selectedYear || $selectedMonth) {
                        $include = false;
                        if (!empty($s['scheduled_date']) && $s['scheduled_date'] !== '0000-00-00') {
                            try {
                                $dt = new DateTime($s['scheduled_date']);
                                if ($selectedYear && $selectedMonth) {
                                    if ((int)$dt->format('Y') === $selectedYear && (int)$dt->format('n') === $selectedMonth) {
                                        $include = true;
                                    }
                                } elseif ($selectedYear) {
                                    if ((int)$dt->format('Y') === $selectedYear) $include = true;
                                } elseif ($selectedMonth) {
                                    if ((int)$dt->format('n') === $selectedMonth) $include = true;
                                }
                            } catch (Exception $e) {}
                        }
                        if (!$include) continue;
                    }
                    $maintenanceByJob[$s['job_id']][] = $s;
                }
            }

            if (($selectedYear || $selectedMonth) && !empty($jobs)) {
                $filteredJobs = [];
                foreach ($jobs as $j) {
                    if (!empty($maintenanceByJob[$j['job_id']])) {
                        $filteredJobs[] = $j;
                    }
                }
                $jobs = $filteredJobs;
            }
            
            $completed_jobs_count = 0;
            $scheduled_maintenance_count = 0;
            $due_maintenance_count = 0;
            
            foreach ($jobs as $job) {
                if ($job['completion_status'] === 'Completed') {
                    $completed_jobs_count++;
                }
                
                if (!empty($maintenanceByJob[$job['job_id']])) {
                    foreach ($maintenanceByJob[$job['job_id']] as $ms) {
                        $status = $ms['status'] ?? '';
                        $scheduledDateStr = $ms['scheduled_date'] ?? null;
                        $scheduledDate = null;
                        if ($scheduledDateStr) {
                            try { $scheduledDate = new DateTime($scheduledDateStr); } catch (Exception $e) { $scheduledDate = null; }
                        }

                        if ($status === 'scheduled' && $scheduledDate && $scheduledDate < new DateTime()) {
                            $status = 'overdue';
                        }

                        if ($status === 'scheduled') {
                            $scheduled_maintenance_count++;
                        } elseif ($status === 'overdue') {
                            $due_maintenance_count++;
                        } else {
                            $due_maintenance_count++;
                        }
                    }
                } else {
                    $installation_date = $job['date_completed'];
                    if ($installation_date && $installation_date !== '0000-00-00') {
                        $install_date = new DateTime($installation_date);
                        for ($i = 1; $i <= 4; $i++) {
                            $cycle_date = clone $install_date;
                            $cycle_date->add(new DateInterval('P' . (6 * $i) . 'M'));
                            if ($cycle_date > new DateTime()) {
                                $scheduled_maintenance_count++;
                            } else {
                                $due_maintenance_count++;
                            }
                        }
                    }
                }
            }
            
            $customerRefs = $this->reportManager->getCustomerRefs(5);
            $companyRefs = $this->reportManager->getCompanyRefs();
            
            $data = [
                'username' => $_SESSION['db_username'] ?? 'Admin',
                'dbname' => Database::getDatabaseName(),
                'jobs' => $jobs,
                'completed_jobs_count' => $completed_jobs_count,
                'scheduled_maintenance_count' => $scheduled_maintenance_count,
                'due_maintenance_count' => $due_maintenance_count,
                'customer_refs' => $customerRefs,
                'company_refs' => $companyRefs,
                'maintenance_by_job' => $maintenanceByJob,
                'filters' => $filters
            ];
            
            if ($request->get('download_csv')) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="maintenance_report_' . date('Y-m-d') . '.csv"');
                $output = fopen('php://output', 'w');
                
                fputcsv($output, ['A2Z Maintenance Report']);
                fputcsv($output, ['']);
                fputcsv($output, [
                    'Job ID', 'Customer', 'Location', 'Company Reference', 'Engineer', 
                    'Installation Date', 'Completion Status', 'Maintenance Cycle 1', 
                    'Maintenance Cycle 2', 'Maintenance Cycle 3', 'Maintenance Cycle 4'
                ]);
                
                foreach ($jobs as $job) {
                    $row = [
                        $job['job_id'], $job['customer_reference'], $job['location'], 
                        $job['company_reference'], $job['engineer'], $job['date_completed'], 
                        $job['completion_status']
                    ];
                    
                    for ($i = 1; $i <= 4; $i++) {
                        $status = 'Not Scheduled';
                        if (!empty($maintenanceByJob[$job['job_id']])) {
                            foreach ($maintenanceByJob[$job['job_id']] as $ms) {
                                if (intval($ms['cycle_number']) === $i) {
                                    $status = ucfirst($ms['status']);
                                    if (!empty($ms['actual_date']) && $ms['actual_date'] !== '0000-00-00') {
                                        $status .= " (" . $ms['actual_date'] . ")";
                                    } elseif (!empty($ms['scheduled_date'])) {
                                        $status .= " (Due " . $ms['scheduled_date'] . ")";
                                    }
                                    break;
                                }
                            }
                        }
                        $row[] = $status;
                    }
                    fputcsv($output, $row);
                }
                fclose($output);
                exit;
            }
            
            $this->render('reports/maintenance_report', $data);
        } catch (Exception $e) {
            error_log("Error in maintenanceReport: " . $e->getMessage());
            $this->render('reports/maintenance_report', [
                'username' => $_SESSION['db_username'] ?? 'Admin',
                'dbname' => Database::getDatabaseName(),
                'error' => "Error generating report: " . $e->getMessage()
            ]);
        }
    }
}
