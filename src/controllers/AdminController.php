<?php
// src/controllers/AdminController.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_log('Error reporting enabled in AdminController.php');
error_reporting(E_ALL);

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/TableManager.php';
require_once __DIR__ . '/../models/ReportManager.php';
require_once __DIR__ . '/../core/Database.php';

if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
}

class AdminController extends Controller {
    private $tableManager;
    private $reportManager;

    public function __construct() {
        if (method_exists('Controller', '__construct')) {
            parent::__construct();
        }
        $this->tableManager = new TableManager();
        $this->reportManager = new ReportManager();
    }

    public function dashboard() {
        if (!isset($_SESSION['db_username']) || !isset($_SESSION['db_password'])) {
            header("Location: " . FULL_BASE_URL . "/login");
            exit;
        }

        try {
            $db = Database::getInstance($_SESSION['db_username'], $_SESSION['db_password']);
        } catch (Exception $e) {
            error_log("DB connection failed in dashboard: " . $e->getMessage());
            header("Location: " . FULL_BASE_URL . "/login");
            exit;
        }

        $data = [
            'username' => $_SESSION['db_username'] ?? 'Admin',
            'dbname' => 'suramalr_a2zOperationalDB',
            'summary' => [
                'total_employees' => $this->tableManager->getTotalEmployees(),
                'active_jobs' => $this->tableManager->getActiveJobs(),
                'total_projects' => $this->tableManager->getTotalProjects(),
                'total_expenses' => $this->tableManager->getTotalExpenses(),
                'total_payments' => $this->tableManager->getTotalPayments(),
                'todays_jobs' => $this->tableManager->getTodaysJobs(),
                'todays_expenses' => $this->tableManager->getTodaysExpenses(),
            ],
            'system_info' => [
                'php_version' => phpversion(),
                'mysql_version' => $this->tableManager->getMySQLVersion(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'],
                'db_name' => 'suramalr_a2zOperationalDB',
            ]
        ];

        $this->render('admin/dashboard', $data);
    }

    public function tables() {
        if (!isset($_SESSION['db_username']) || !isset($_SESSION['db_password'])) {
            header("Location: " . FULL_BASE_URL . "/login");
            exit;
        }

        try {
            $db = Database::getInstance($_SESSION['db_username'], $_SESSION['db_password']);
        } catch (Exception $e) {
            error_log("DB connection failed in tables: " . $e->getMessage());
            header("Location: " . FULL_BASE_URL . "/login");
            exit;
        }

        $data = [
            'username' => $_SESSION['db_username'] ?? 'Admin',
            'dbname' => 'suramalr_a2zOperationalDB',
            'operationalCards' => [
                ['link' => '/admin/manageTable/attendance', 'icon' => 'fa-calendar-check', 'title' => 'Attendance', 'desc' => 'Track employee attendance'],
                ['link' => '/admin/manageTable/employees', 'icon' => 'fa-user-tie', 'title' => 'Employees', 'desc' => 'Manage employee records'],
                ['link' => '/admin/manageTable/employee_bank_details', 'icon' => 'fa-university', 'title' => 'Employee Bank Details', 'desc' => 'Banking information'],
                ['link' => '/admin/manageTable/employee_payment_rates', 'icon' => 'fa-money-check-alt', 'title' => 'Employee Payment Rates', 'desc' => 'Fixed or Daily Wage'],
                ['link' => '/admin/manageTable/projects', 'icon' => 'fa-project-diagram', 'title' => 'Projects', 'desc' => 'Project management'],
                ['link' => '/admin/manageTable/jobs', 'icon' => 'fa-briefcase', 'title' => 'Jobs', 'desc' => 'Job assignments'],
                ['link' => '/admin/manageTable/operational_expenses', 'icon' => 'fa-receipt', 'title' => 'Operational Expenses', 'desc' => 'Expense tracking'],
                ['link' => '/admin/manageTable/invoice_data', 'icon' => 'fa-file-invoice', 'title' => 'Invoice Data', 'desc' => 'Invoice records'],
                ['link' => '/admin/manageTable/employee_payments', 'icon' => 'fa-money-check-alt', 'title' => 'Employee Payments', 'desc' => 'Payment history'],
                ['link' => '/admin/manageTable/salary_increments', 'icon' => 'fa-money-check-alt', 'title' => 'Salary Increments', 'desc' => 'Salary adjustments'],
                ['link' => '/admin/manageTable/cash_hand', 'icon' => 'fa-hand-holding-usd', 'title' => 'Cash In Hand', 'desc' => 'Manage internal cash flow transactions'],
            ]
        ];

        $this->render('admin/tables', $data);
    }

    public function records() {
        if (!isset($_SESSION['db_username']) || !isset($_SESSION['db_password'])) {
            header("Location: " . FULL_BASE_URL . "/login");
            exit;
        }

        try {
            $db = Database::getInstance($_SESSION['db_username'], $_SESSION['db_password']);
        } catch (Exception $e) {
            error_log("DB connection failed in records: " . $e->getMessage());
            header("Location: " . FULL_BASE_URL . "/login");
            exit;
        }

        $data = [
            'username' => $_SESSION['db_username'] ?? 'Admin',
            'dbname' => 'suramalr_a2zOperationalDB',
            'reportCards' => [
                ['link' => FULL_BASE_URL . '/reports/wages_report', 'icon' => 'fa-money-bill', 'title' => 'Monthly Wages', 'desc' => 'Wage summary'],
                ['link' => FULL_BASE_URL . '/reports/expenses_report', 'icon' => 'fa-file-invoice-dollar', 'title' => 'Expenses Report', 'desc' => 'Expense analysis'],
                ['link' => FULL_BASE_URL . '/reports/cost_calculation', 'icon' => 'fa-chart-pie', 'title' => 'Site Cost Calculation', 'desc' => 'Cost breakdown'],
                ['link' => FULL_BASE_URL . '/reports/material_find', 'icon' => 'fa-cogs', 'title' => 'Material Cost Calculation', 'desc' => 'Material expenses'],
                ['link' => FULL_BASE_URL . '/reports/a2z_engineering_jobs', 'icon' => 'fa-cogs', 'title' => 'A2Z Engineering Jobs', 'desc' => 'Job overview'],
            ]
        ];

        $this->render('admin/reports', $data);
    }

    public function manageTable($table) {
        if (!isset($_SESSION['db_username']) || !isset($_SESSION['db_password'])) {
            header("Location: " . FULL_BASE_URL . "/login");
            exit;
        }

        try {
            $db = Database::getInstance($_SESSION['db_username'], $_SESSION['db_password']);
        } catch (Exception $e) {
            error_log("DB connection failed in manageTable: " . $e->getMessage());
            header("Location: " . FULL_BASE_URL . "/login");
            exit;
        }

        if (!in_array($table, $this->tableManager->getAllowedTables())) {
            header("Location: " . FULL_BASE_URL . "/admin");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $columns = $this->tableManager->getColumns($table);
            $idColumn = $this->tableManager->getPrimaryKey($table);

            if ($action === 'get_records') {
                $searchTerm = $_POST['search']['value'] ?? '';
                $sortColumn = $_POST['sortColumn'] ?? '';
                $sortOrder = strtoupper($_POST['sortOrder'] ?? 'DESC');
                $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
                $perPage = isset($_POST['perPage']) ? (int)$_POST['perPage'] : 10;

                try {
                    $result = $this->tableManager->fetchRecords($table, $page, $perPage, $searchTerm, $sortColumn, $sortOrder, true, false);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                } catch (Exception $e) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => $e->getMessage()]);
                }
                exit;
            } elseif ($action === 'create') {
                $data = [];
                foreach ($columns as $column) {
                    if ($column !== $idColumn) {
                        $data[$column] = $_POST[$column] ?? '';
                    }
                }
                error_log("Create data for $table: " . json_encode($data));
                try {
                    $newId = $this->tableManager->create($table, $data);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Record created successfully!', 'id' => $newId]);
                } catch (Exception $e) {
                    $error = "Error creating record: " . $e->getMessage();
                    error_log($error);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $error]);
                }
                exit;
            } elseif ($action === 'update') {
                $id = $_POST['id'] ?? '';
                $data = [];
                foreach ($columns as $column) {
                    $data[$column] = $_POST[$column] ?? '';
                }
                try {
                    $this->tableManager->update($table, $data, $idColumn, $id);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Record updated successfully!']);
                } catch (Exception $e) {
                    $error = "Error updating record: " . $e->getMessage();
                    error_log($error);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $error]);
                }
                exit;
            } elseif ($action === 'delete') {
                $id = $_POST['id'] ?? '';
                if (empty($id)) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => 'Record ID is required']);
                    exit;
                }
                error_log("Attempting to delete record from $table with $idColumn = $id");
                try {
                    $this->tableManager->delete($table, $idColumn, $id);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Record deleted successfully!']);
                } catch (Exception $e) {
                    $error = "Error deleting record: " . $e->getMessage();
                    error_log($error);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $error]);
                }
                exit;
            } elseif ($action === 'export_csv') {
                $this->tableManager->exportRecordsToCSV($table, $_POST['start_date'], $_POST['end_date']);
                exit;
            } elseif ($action === 'update_status' && $table === 'jobs') {
                try {
                    $jobId = $_POST['job_id'] ?? '';
                    $newCompletion = $_POST['completion'] ?? null;
                    $result = $this->tableManager->updateJobStatus($jobId, $newCompletion);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                } catch (Exception $e) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                }
                exit;
            } elseif ($action === 'get_invoice_details' && $table === 'jobs') {
                try {
                    $jobId = $_POST['job_id'] ?? '';
                    if (empty($jobId)) {
                        throw new Exception("Job ID is required");
                    }
                    $invoiceData = $this->tableManager->getInvoiceDetailsByJobId($jobId);
                    header('Content-Type: application/json');
                    echo json_encode($invoiceData ?? []);
                } catch (Exception $e) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => $e->getMessage()]);
                }
                exit;
            }
        }

        // Fetch records for initial page load
        $searchTerm = $_GET['search'] ?? '';
        $result = $this->tableManager->fetchRecords($table, 1, 0, $searchTerm, '', 'DESC', false, false);

        $data = [
            'table' => $table,
            'columns' => $this->tableManager->getColumns($table),
            'records' => $result['data'],
            'totalRecords' => $result['recordsTotal'],
            'config' => $this->tableManager->getConfig($table),
            'username' => $_SESSION['db_username'] ?? 'Admin',
            'dbname' => 'suramalr_a2zOperationalDB',
            'tableManager' => $this->tableManager,
            'message' => $_GET['message'] ?? '',
            'error' => $_GET['error'] ?? ''
        ];

        if ($table === 'jobs') {
            $data['totalCapacity'] = $this->tableManager->calculateTotalJobCapacity();
        }

        $this->render('admin/manage_table', $data);
    }

    public function wagesReport() {
        if (!isset($_SESSION['db_username']) || !isset($_SESSION['db_password'])) {
            header("Location: " . FULL_BASE_URL . "/login");
            exit;
        }

        try {
            $db = Database::getInstance($_SESSION['db_username'], $_SESSION['db_password']);
            $filters = [
                'emp_id' => $_GET['emp_id'] ?? '',
                'from_date' => '',
                'to_date' => ''
            ];

            $start_date = null;
            $end_date = null;
            $report_title = "Wages Report (All Time)";

            if (isset($_GET['year']) && isset($_GET['month']) && $_GET['month'] !== '') {
                $year = filter_var($_GET['year'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 2024, 'max_range' => 2025]]);
                $month = filter_var($_GET['month'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]]);
                if ($year === false || $month === false) {
                    throw new Exception("Invalid year or month selected.");
                }
                $start_date = sprintf("%d-%02d-01", $year, $month);
                $end_date = sprintf("%d-%02d-%d", $year, $month, date('t', mktime(0, 0, 0, $month, 1, $year)));
                $filters['from_date'] = $start_date;
                $filters['to_date'] = $end_date;
                $month_name = date('F', mktime(0, 0, 0, $month, 1, $year));
                $report_title = "Wages Report for $month_name $year";
            } elseif (isset($_GET['year'])) {
                $year = filter_var($_GET['year'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 2024, 'max_range' => 2025]]) ?: date('Y');
                $start_date = sprintf("%d-01-01", $year);
                $end_date = sprintf("%d-12-31", $year);
                $filters['from_date'] = $start_date;
                $filters['to_date'] = $end_date;
                $report_title = "Wages Report for $year";
            }

            if (!method_exists($this->reportManager, 'getEmployeeRefs')) {
                throw new Exception("ReportManager::getEmployeeRefs method is missing.");
            }
            $employee_refs = $this->reportManager->getEmployeeRefs();
            $wage_data = $this->reportManager->getWageData($filters);
            $labor_wages_data = $this->reportManager->getLaborWagesData($start_date, $end_date);
            $epf_costs = $this->reportManager->getEPFCosts($start_date);

            $total_wages = 0;
            $total_daily_wages = 0;
            $total_working_days = 0;
            $employee_count = count(array_unique(array_column($wage_data, 'emp_id')));
            foreach ($wage_data as &$row) {
                $total_daily_wages += $row['total_payment'];
                $total_working_days += $row['total_days'];
            }
            unset($row);
            $total_wages = $total_daily_wages + $epf_costs;
            $avg_wage_per_employee = $employee_count > 0 ? $total_wages / $employee_count : 0;

            if (isset($_GET['download_csv'])) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="wages_report_' . date('Y-m-d') . '.csv"');
                $output = fopen('php://output', 'w');
                fputcsv($output, [$report_title]);
                fputcsv($output, ['']);
                fputcsv($output, ['Financial Overview', 'Value']);
                fputcsv($output, ['Total Wages', number_format($total_wages, 2)]);
                fputcsv($output, ['Total Daily Wages', number_format($total_daily_wages, 2)]);
                fputcsv($output, ['EPF Costs', number_format($epf_costs, 2)]);
                fputcsv($output, ['']);
                fputcsv($output, ['Summary Statistics', 'Value']);
                fputcsv($output, ['Total Working Days', number_format($total_working_days, 2)]);
                fputcsv($output, ['Employee Count', $employee_count]);
                fputcsv($output, ['Average Wage per Employee', number_format($avg_wage_per_employee, 2)]);
                fputcsv($output, ['']);
                fputcsv($output, ['Employee Wages Breakdown', 'Employee Name', 'Total Days', 'Total Payment']);
                foreach ($wage_data as $emp) {
                    fputcsv($output, ['', $emp['emp_name'], $emp['total_days'], number_format($emp['total_payment'], 2)]);
                }
                fputcsv($output, ['']);
                fputcsv($output, ['Attendance Details', 'Employee Name', 'Date', 'Presence', 'Payment', 'Customer Reference', 'Location', 'Job Capacity', 'Project Description', 'Company Reference']);
                foreach ($wage_data as $emp) {
                    foreach ($emp['attendance_details'] as $detail) {
                        if ($detail['presence'] == 0.0) continue;
                        fputcsv($output, [
                            '', 
                            $emp['emp_name'], 
                            $detail['date'], 
                            $detail['presence'], 
                            number_format($detail['payment'], 2), 
                            $detail['customer_reference'], 
                            $detail['location'], 
                            $detail['job_capacity'], 
                            $detail['project_description'], 
                            $detail['company_reference']
                        ]);
                    }
                }
                fputcsv($output, ['']);
                fputcsv($output, ['Labor Wages Summation', 'Labor Name', 'Total Days', 'Total Amount']);
                foreach ($labor_wages_data['summations'] as $sum) {
                    fputcsv($output, ['', $sum['labor_name'], $sum['total_days'], number_format($sum['total_amount'], 2)]);
                }
                fputcsv($output, ['']);
                fputcsv($output, ['Labor Wages Details', 'Labor Name', 'Job ID', 'Expensed Date', 'Description', 'Amount']);
                foreach ($labor_wages_data['details'] as $labor_name => $details) {
                    foreach ($details as $labor) {
                        fputcsv($output, [
                            '', 
                            $labor_name, 
                            $labor['job_id'], 
                            $labor['expensed_date'], 
                            $labor['description'], 
                            number_format($labor['expense_amount'], 2)
                        ]);
                    }
                }
                fclose($output);
                exit();
            }

            $data = [
                'username' => $_SESSION['db_username'] ?? 'Admin',
                'dbname' => 'suramalr_a2zOperationalDB',
                'report_title' => $report_title,
                'employee_refs' => $employee_refs,
                'wage_data' => $wage_data,
                'labor_wages_data' => $labor_wages_data,
                'total_wages' => $total_wages,
                'total_daily_wages' => $total_daily_wages,
                'epf_costs' => $epf_costs,
                'total_working_days' => $total_working_days,
                'employee_count' => $employee_count,
                'avg_wage_per_employee' => $avg_wage_per_employee,
                'filters' => [
                    'year' => $_GET['year'] ?? '',
                    'month' => $_GET['month'] ?? '',
                    'emp_id' => $_GET['emp_id'] ?? ''
                ]
            ];

            $this->render('reports/wages_report', $data);
        } catch (Exception $e) {
            error_log("Error in wagesReport: " . $e->getMessage());
            $this->render('reports/wages_report', [
                'username' => $_SESSION['db_username'] ?? 'Admin',
                'dbname' => 'suramalr_a2zOperationalDB',
                'error' => "Error generating report: " . $e->getMessage()
            ]);
        }
    }

    public function expenseReport() {
        if (!isset($_SESSION['db_username']) || !isset($_SESSION['db_password'])) {
            header("Location: " . FULL_BASE_URL . "/login");
            exit;
        }

        try {
            $db = Database::getInstance($_SESSION['db_username'], $_SESSION['db_password']);
            $start_date = null;
            $end_date = null;
            $report_title = "Full Company Expense Report (All Time)";

            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['year'], $_POST['month'])) {
                $year = filter_var($_POST['year'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 2024, 'max_range' => 2025]]);
                $month = filter_var($_POST['month'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]]);
                if ($year === false || $month === false) {
                    throw new Exception("Invalid year or month selected.");
                }
                $start_date = sprintf("%d-%02d-01", $year, $month);
                $end_date = sprintf("%d-%02d-%d", $year, $month, date('t', mktime(0, 0, 0, $month, 1, $year)));
                $month_name = date('F', mktime(0, 0, 0, $month, 1, $year));
                $report_title = "Company Expense Report for $month_name $year";
            }

            $expenses_data = $this->reportManager->getExpensesByCategory($start_date, $end_date);
            $invoices_data = $this->reportManager->getInvoicesSummary($start_date, $end_date);
            $jobs_data = $this->reportManager->getJobsSummary($start_date, $end_date);
            $attendance_data = $this->reportManager->getAttendanceCosts($start_date, $end_date);
            $epf_costs = $this->reportManager->getEPFCosts($start_date);

            $total_expenses = 0;
            $total_employee_costs = 0;
            $expenses_by_category = [];
            $employee_costs_by_type = ['Attendance-Based' => 0, 'Hiring of Labor' => 0];

            foreach ($expenses_data as $row) {
                $category = $row['expenses_category'];
                $amount = floatval($row['total_expenses']);
                if (strcasecmp($category, 'Hiring of Labor') === 0) {
                    $employee_costs_by_type['Hiring of Labor'] = $amount;
                    $total_employee_costs += $amount;
                } else {
                    $expenses_by_category[$category] = $amount;
                    $total_expenses += $amount;
                }
            }

            foreach ($attendance_data as $row) {
                $payment = floatval($row['actual_cost'] ?? 0);
                if ($row['presence'] > 0) {
                    $employee_costs_by_type['Attendance-Based'] += $payment;
                    $total_employee_costs += $payment;
                }
            }

            $total_employee_costs += $epf_costs;
            $expenses_by_category['EPF'] = $epf_costs;

            $total_invoices = floatval($invoices_data[0]['total_invoices'] ?? 0);
            $total_invoices_count = intval($invoices_data[0]['invoice_count'] ?? 0);
            $total_jobs = intval($jobs_data['job_count'] ?? 0);
            $total_job_capacity = floatval($jobs_data['total_capacity'] ?? 0);

            $profit = $total_invoices - ($total_expenses + $total_employee_costs);

            $data = [
                'username' => $_SESSION['db_username'] ?? 'Admin',
                'dbname' => 'suramalr_a2zOperationalDB',
                'report_title' => $report_title,
                'total_expenses' => $total_expenses,
                'total_invoices' => $total_invoices,
                'total_invoices_count' => $total_invoices_count,
                'total_jobs' => $total_jobs,
                'total_job_capacity' => $total_job_capacity,
                'total_employee_costs' => $total_employee_costs,
                'profit' => $profit,
                'expenses_by_category' => $expenses_by_category,
                'employee_costs_by_type' => $employee_costs_by_type,
                'filters' => ['year' => $_POST['year'] ?? '', 'month' => $_POST['month'] ?? '']
            ];

            if (isset($_GET['download_csv'])) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="expense_report_' . date('Y-m-d') . '.csv"');
                $output = fopen('php://output', 'w');
                fputcsv($output, [$report_title]);
                fputcsv($output, ['']);
                fputcsv($output, ['Financial Overview', 'Value']);
                fputcsv($output, ['Total Invoices', number_format($total_invoices, 2)]);
                fputcsv($output, ['Total Operational Expenses', number_format($total_expenses, 2)]);
                fputcsv($output, ['Total Employee Costs', number_format($total_employee_costs, 2)]);
                fputcsv($output, ['Net Profit', number_format($profit, 2)]);
                fputcsv($output, ['']);
                fputcsv($output, ['Operational Expenses by Category', 'Amount']);
                foreach ($expenses_by_category as $category => $amount) {
                    fputcsv($output, [$category, number_format($amount, 2)]);
                }
                fputcsv($output, ['']);
                fputcsv($output, ['Employee Costs by Type', 'Amount']);
                foreach ($employee_costs_by_type as $type => $amount) {
                    fputcsv($output, [$type, number_format($amount, 2)]);
                }
                fputcsv($output, ['']);
                fputcsv($output, ['Additional Metrics', 'Value']);
                fputcsv($output, ['Invoice Count', $total_invoices_count]);
                fputcsv($output, ['Total Jobs', $total_jobs]);
                fputcsv($output, ['Total Job Capacity', number_format($total_job_capacity, 2)]);
                fclose($output);
                exit();
            }

            $this->render('reports/expenses_report', $data);
        } catch (Exception $e) {
            error_log("Error in expenseReport: " . $e->getMessage());
            $this->render('reports/expenses_report', [
                'username' => $_SESSION['db_username'] ?? 'Admin',
                'dbname' => 'suramalr_a2zOperationalDB',
                'error' => "Error generating report: " . $e->getMessage()
            ]);
        }
    }

    public function costCalculation() {
    if (!isset($_SESSION['db_username']) || !isset($_SESSION['db_password'])) {
        header("Location: " . FULL_BASE_URL . "/login");
        exit;
    }

    try {
        $db = Database::getInstance($_SESSION['db_username'], $_SESSION['db_password']);
        $filters = [
            'invoice_no' => $_GET['invoice_id'] ?? '',
            'customer_reference' => $_GET['customer_name'] ?? '',
            'company_reference' => $_GET['client_ref'] ?? '',
            'status' => $_GET['status'] ?? '',
            'from_date' => $_GET['from_date'] ?? '',
            'to_date' => $_GET['to_date'] ?? ''
        ];

        $customerRefs = $this->reportManager->getCustomerRefs();
        $companyRefs = $this->reportManager->getCompanyRefs();
        $jobData = $this->reportManager->getJobCostData($filters, PHP_INT_MAX, 0);
        $overallSummary = $this->reportManager->getOverallSummary();

        $totalInvoiceAmount = array_sum(array_column($jobData, 'invoice_value'));
        $totalPaidAmount = array_sum(array_column($jobData, 'received_amount'));
        $totalUnpaidAmount = 0;
        $unpaidInvoiceCount = 0;
        $totalExpenses = 0;
        $totalEmployeeCostsSum = 0;
        $totalCapacity = 0;
        $totalNetProfit = 0;

        foreach ($jobData as &$row) {
            $operationalExpenses = [];
            $hiringLaborCost = 0;
            if ($row['expense_summary'] !== 'No expenses') {
                foreach (explode(', ', $row['expense_summary']) as $expense) {
                    [$category, $amount] = explode(': ', $expense);
                    if (strcasecmp($category, 'Hiring of Labor') === 0) {
                        $hiringLaborCost += floatval($amount);
                    } else {
                        $operationalExpenses[$category] = ($operationalExpenses[$category] ?? 0) + floatval($amount);
                    }
                }
            }
            $row['operational_expenses'] = array_sum($operationalExpenses);
            $row['expense_details'] = $operationalExpenses;

            $employeeCosts = $this->reportManager->getEmployeeCosts($row['job_id']);
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
                $key = "$empName-$attendanceDate"; // Unique key for attendance record

                if (!isset($employeeBreakdown[$empName])) {
                    $employeeBreakdown[$empName] = [
                        'emp_name' => $empName,
                        'payment' => 0,
                        'days' => [],
                        'days_by_date' => [] // Temporary tracking for deduplication
                    ];
                }

                $presence = floatval($cost['presence'] ?? 0);
                // Cap presence at 1.0 per day to prevent invalid values like 2.0
                if ($presence > 1.0) {
                    error_log("Warning: Invalid presence value {$presence} for job_id {$row['job_id']}, employee: $empName, date: $attendanceDate. Capping at 1.0");
                    $presence = 1.0;
                }
                $payment = floatval($cost['actual_cost'] ?? 0);

                if ($presence > 0 && ($cost['rate_amount'] ?? 0) == 0) {
                    error_log("Warning: No rate or increment found for job_id {$row['job_id']}, employee: $empName, date: $attendanceDate, presence: $presence");
                }

                // Only add if not already processed for this date
                if (!isset($employeeBreakdown[$empName]['days_by_date'][$attendanceDate])) {
                    $employeeBreakdown[$empName]['payment'] += $payment;
                    $employeeBreakdown[$empName]['days'][] = [
                        'date' => $cost['attendance_date'],
                        'presence' => $presence,
                        'rate_amount' => $cost['rate_amount'] ?? 0,
                        'increment_amount' => $cost['increment_amount'] ?? 0,
                        'total_rate' => $cost['rate_amount'] ?? 0
                    ];
                    $employeeBreakdown[$empName]['days_by_date'][$attendanceDate] = true;
                    $totalEmployeeCosts += $payment;
                } else {
                    error_log("Skipping duplicate attendance for job_id {$row['job_id']}, employee: $empName, date: $attendanceDate");
                }
            }

            foreach ($employeeBreakdown as $emp) {
                unset($emp['days_by_date']); // Clean up temporary tracking array
                $row['employee_details'][] = [
                    'emp_name' => $emp['emp_name'],
                    'payment' => $emp['payment'],
                    'days' => $emp['days']
                ];
            }
            $row['total_employee_costs'] = $totalEmployeeCosts;

            if (empty($row['employee_details']) && $totalEmployeeCosts == 0) {
                error_log("No employee costs calculated for job_id {$row['job_id']} - check attendance or rate data");
            } else {
                error_log("Employee costs for job_id {$row['job_id']}: " . json_encode($row['employee_details']));
            }

            if (floatval($row['received_amount']) == 0) {
                $totalUnpaidAmount += floatval($row['invoice_value']);
                $unpaidInvoiceCount++;
            }
            $totalExpenses += $row['operational_expenses'];
            $totalEmployeeCostsSum += $totalEmployeeCosts;
            $totalCapacity += floatval($row['job_capacity'] ?? 0);
            $netProfit = floatval($row['invoice_value']) - ($totalEmployeeCosts + $row['operational_expenses']);
            $row['net_profit'] = $netProfit;
            $totalNetProfit += $netProfit;
        }
        unset($row);

        $dueBalance = $totalInvoiceAmount - $totalPaidAmount;
        $profitMargin = $totalInvoiceAmount > 0 ? ($totalNetProfit / $totalInvoiceAmount) * 100 : 0;
        $overallDueBalance = floatval($overallSummary['total_invoices'] ?? 0) - floatval($overallSummary['total_paid'] ?? 0);

        $data = [
            'username' => $_SESSION['db_username'] ?? 'Admin',
            'dbname' => 'suramalr_a2zOperationalDB',
            'customer_refs' => $customerRefs,
            'company_refs' => $companyRefs,
            'job_data' => $jobData,
            'total_invoice_amount' => $totalInvoiceAmount,
            'total_paid_amount' => $totalPaidAmount,
            'total_unpaid_amount' => $totalUnpaidAmount,
            'unpaid_invoice_count' => $unpaidInvoiceCount,
            'due_balance' => $dueBalance,
            'total_expenses' => $totalExpenses,
            'total_employee_costs_sum' => $totalEmployeeCostsSum,
            'total_capacity' => $totalCapacity,
            'total_net_profit' => $totalNetProfit,
            'profit_margin' => $profitMargin,
            'overall_invoice_amount' => floatval($overallSummary['total_invoices'] ?? 0),
            'overall_paid_amount' => floatval($overallSummary['total_paid'] ?? 0),
            'overall_unpaid_amount' => $overallDueBalance,
            'overall_due_balance' => $overallDueBalance,
            'filters' => $filters
        ];

        if (isset($_GET['download_csv'])) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="cost_calculation_' . date('Y-m-d') . '.csv"');
            $output = fopen('php://output', 'w');
            fputcsv($output, [
                'Job ID', 'Date', 'Customer', 'Location', 'Company Ref', 'Engineer', 'Capacity',
                'Invoice No', 'Invoice Value', 'Received', 'Date Paid', 'Expenses', 'Employee Costs', 'Outstanding', 'Net Profit'
            ]);
            foreach ($jobData as $row) {
                $outstanding = floatval($row['invoice_value']) - floatval($row['received_amount']);
                fputcsv($output, [
                    $row['job_id'], $row['date_completed'], $row['customer_reference'], $row['location'],
                    $row['company_reference'] ?? 'N/A', $row['engineer'] ?? 'N/A', $row['job_capacity'],
                    $row['invoice_no'], $row['invoice_value'], $row['received_amount'], $row['payment_received_date'],
                    $row['operational_expenses'], $row['total_employee_costs'], $outstanding, $row['net_profit']
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
            'dbname' => 'suramalr_a2zOperationalDB',
            'error' => "Error generating report: " . $e->getMessage()
        ]);
    }
}
    public function materialFind() {
        if (!isset($_SESSION['db_username']) || !isset($_SESSION['db_password'])) {
            header("Location: " . FULL_BASE_URL . "/login");
            exit;
        }
        echo "Material Cost Calculation";
    }

    public function a2zEngineeringJobs() {
        if (!isset($_SESSION['db_username']) || !isset($_SESSION['db_password'])) {
            header("Location: " . FULL_BASE_URL . "/login");
            exit;
        }
        echo "A2Z Engineering Jobs";
    }
}