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
                ['link' => '/admin/manageTable/maintenance_schedule', 'icon' => 'fa-tools', 'title' => 'Maintenance Schedule', 'desc' => 'Scheduled maintenance cycles'],
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
                ['link' => FULL_BASE_URL . '/reports/maintenance_report', 'icon' => 'fa-tools', 'title' => 'A2Z Maintenance', 'desc' => 'Maintenance schedule and tracking'],
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

            // Handle maintenance schedule status updates (AJAX)
            if ($action === 'set_maintenance_status') {
                try {
                    $jobId = $_POST['job_id'] ?? '';
                    $cycle = intval($_POST['cycle_number'] ?? 0);
                    $scheduledDate = $_POST['scheduled_date'] ?? null;
                    $status = $_POST['status'] ?? 'scheduled';
                    $description = $_POST['description'] ?? null;

                    if (empty($jobId) || $cycle <= 0) {
                        throw new Exception('Job ID and cycle number are required');
                    }

                    $pdo = $db->getConnection();
                    // Check existing
                    $check = $pdo->prepare("SELECT schedule_id FROM maintenance_schedule WHERE job_id = ? AND cycle_number = ? LIMIT 1");
                    $check->execute([$jobId, $cycle]);
                    $existing = $check->fetch(PDO::FETCH_ASSOC);

                    if ($existing) {
                        // Update
                        $updateFields = [];
                        $params = [];
                        if ($scheduledDate !== null) { $updateFields[] = 'scheduled_date = ?'; $params[] = $scheduledDate; }
                        if ($status !== null) { $updateFields[] = 'status = ?'; $params[] = $status; }
                        if ($description !== null) { $updateFields[] = 'description = ?'; $params[] = $description; }
                        if ($status === 'completed') { $updateFields[] = 'actual_date = ?'; $params[] = date('Y-m-d'); }
                        if (!empty($updateFields)) {
                            $params[] = $existing['schedule_id'];
                            $sql = "UPDATE maintenance_schedule SET " . implode(', ', $updateFields) . " WHERE schedule_id = ?";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute($params);
                        }
                        echo json_encode(['success' => true, 'action' => 'updated']);
                    } else {
                        // Insert
                        $actualDate = $status === 'completed' ? date('Y-m-d') : null;
                        $stmt = $pdo->prepare("INSERT INTO maintenance_schedule (job_id, cycle_number, scheduled_date, actual_date, status, description) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$jobId, $cycle, $scheduledDate, $actualDate, $status, $description]);
                        echo json_encode(['success' => true, 'action' => 'inserted', 'id' => $pdo->lastInsertId()]);
                    }
                } catch (Exception $e) {
                    error_log('Error in set_maintenance_status: ' . $e->getMessage());
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                }
                exit;
            }

            if ($action === 'get_records') {
                $searchTerms = isset($_POST['search']['terms']) && is_array($_POST['search']['terms']) ? array_map('trim', $_POST['search']['terms']) : [];
                $sortColumn = $_POST['sortColumn'] ?? '';
                $sortOrder = strtoupper($_POST['sortOrder'] ?? 'DESC');
                $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
                $perPage = isset($_POST['perPage']) ? (int)$_POST['perPage'] : 10;

                try {
                    $result = $this->tableManager->fetchRecords($table, $page, $perPage, $searchTerms, $sortColumn, $sortOrder, true, false);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                } catch (Exception $e) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => $e->getMessage()]);
                }
                exit;
            } elseif ($action === 'generate_maintenance') {
                try {
                    $result = $this->tableManager->generateMaintenanceSchedulesFromJobs();
                    header('Content-Type: application/json');
                    echo json_encode($result);
                } catch (Exception $e) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                }
                exit;
            } elseif ($action === 'generate_maintenance_for_job') {
                try {
                    $jobId = $_POST['job_id'] ?? '';
                    $result = $this->tableManager->generateMaintenanceSchedulesForJob($jobId);
                    header('Content-Type: application/json');
                    echo json_encode($result);
                } catch (Exception $e) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
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
            } elseif ($action === 'get_job_completion' && $table === 'jobs') {
                try {
                    $jobId = $_POST['job_id'] ?? '';
                    $completion = $this->tableManager->getJobCompletion($jobId);
                    header('Content-Type: application/json');
                    echo json_encode(['completion' => $completion]);
                } catch (Exception $e) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => $e->getMessage()]);
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
        $searchTerms = isset($_GET['search']) ? array_map('trim', explode(' ', $_GET['search'])) : [];
        $result = $this->tableManager->fetchRecords($table, 1, 0, $searchTerms, '', 'DESC', false, false);

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

        $start_date = $end_date = null;
        $report_title = "Wages Report (All Time)";

        // Date filtering logic
        if (isset($_GET['year']) && isset($_GET['month']) && $_GET['month'] !== '') {
            $year  = filter_var($_GET['year'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 2020, 'max_range' => 2030]]) ?: date('Y');
            $month = filter_var($_GET['month'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]]);
            if ($month === false) throw new Exception("Invalid month");
            $start_date = sprintf("%d-%02d-01", $year, $month);
            $end_date   = sprintf("%d-%02d-%d", $year, $month, date('t', mktime(0, 0, 0, $month, 1, $year)));
            $month_name = date('F', mktime(0, 0, 0, $month, 1, $year));
            $report_title = "Wages Report – $month_name $year";
        } elseif (isset($_GET['year'])) {
            $year = filter_var($_GET['year'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 2020, 'max_range' => 2030]]) ?: date('Y');
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

        // Top 10 earners - calculate based on net payable
        foreach ($wage_data as &$emp) {
            if (strtoupper($emp['rate_type'] ?? 'DAILY') === 'FIXED') {
                $basic     = floatval($emp['basic_salary'] ?? $emp['rate_amount'] ?? 0);
                $etf       = $basic * 0.03;
                $epfEmp    = $basic * 0.08;
                $epfComp   = $basic * 0.12;
                $payable   = $basic + $epfComp;
                $advance_paid = $emp['advance_details']['paid_amount'] ?? 0;
                $advance_deduction = $emp['advance_details']['deduction_amount'] ?? 0;
                $advance_total = $advance_paid + $advance_deduction;
                $paid      = array_sum($emp['paid_amount'] ?? [0,0,0]);
                // Net Payable = Payable - ETF - EPF Emp - Advance - Other Payments
                $emp['net_payable'] = $payable - $etf - $epfEmp - $advance_total - $paid;
            } else {
                $days   = $emp['attendance_summary']['presence_count'] ?? 0;
                $rate   = floatval($emp['rate_amount'] ?? 0);
                $earned = $days * $rate;
                $advance_paid = $emp['advance_details']['paid_amount'] ?? 0;
                $advance_deduction = $emp['advance_details']['deduction_amount'] ?? 0;
                $advance_total = $advance_paid + $advance_deduction;
                $paid   = array_sum($emp['paid_amount'] ?? [0,0,0]);
                // Net Payable = Earned - Advance - Other Payments
                $emp['net_payable'] = $earned - $advance_total - $paid;
            }
        }
        unset($emp);
        
        // Sort by net payable for Top 10 earners
        usort($wage_data, fn($a, $b) => ($b['net_payable'] ?? 0) <=> ($a['net_payable'] ?? 0));
        $top_earners = array_slice($wage_data, 0, 10);

        // Remove CSV Export
        // if (isset($_GET['download_csv'])) {
        //     $this->generateWagesCSV($report_title, $total_wages, $total_daily_wages, $total_fixed_wages, $epf_costs, $total_advance_payments, $employee_count, $avg_wage_per_employee, $daily_wage_employees, $fixed_rate_employees, $labor_wages_data);
        //     exit;
        // }

        // PDF Salary Slips (All or Single)
        if (isset($_GET['generate_pdf'])) {
            $this->generateWagesPDF($report_title, $wage_data, $filters['emp_id'] ?? '');
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
                'year'   => $_GET['year'] ?? date('Y'),
                'month'  => $_GET['month'] ?? '',
                'emp_id' => $_GET['emp_id'] ?? ''
            ]
        ];

        $this->render('reports/wages_report', $data);

    } catch (Exception $e) {
        error_log("Wages Report Error: " . $e->getMessage());
        $this->render('reports/wages_report', ['error' => $e->getMessage()]);
    }
}

// ──────────────────────────────────────────────────────────────
// CSV Export
private function generateWagesCSV($title, $total_wages, $daily, $fixed, $epf, $advance, $count, $avg, $daily_emps, $fixed_emps, $labor) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="wages_report_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, [$title]); fputcsv($out, ['']);
    fputcsv($out, ['Total Wages', number_format($total_wages,2)]);
    fputcsv($out, ['Daily Wages', number_format($daily,2)]);
    fputcsv($out, ['Fixed Wages', number_format($fixed,2)]);
    fputcsv($out, ['EPF Cost', number_format($epf,2)]);
    fputcsv($out, ['Advance Payments', number_format($advance,2)]);
    fputcsv($out, ['Employee Count', $count]);
    fputcsv($out, ['Average Wage', number_format($avg,2)]);
    fclose($out);
    exit;
}

// ──────────────────────────────────────────────────────────────
// PDF Salary Slips (Works even without Dompdf)
private function generateWagesPDF($report_title, $wage_data, $selected_emp_id) {
    $employees = $selected_emp_id
        ? array_filter($wage_data, fn($e) => $e['emp_id'] == $selected_emp_id)
        : $wage_data;

    $dompdfPath = __DIR__ . '/../../vendor/autoload.php';
    if (file_exists($dompdfPath)) {
        require_once $dompdfPath;
        $this->renderPDFWithDompdf($report_title, $employees);
    } else {
        $this->renderPDFWithBrowserPrint($report_title, $employees);
    }
}

private function renderPDFWithDompdf($title, $employees) {
    $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
    $html = $this->buildSlipHTML($title, $employees);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("Salary_Slips_" . date('Y-m-d') . ".pdf", ['Attachment' => true]);
}

private function renderPDFWithBrowserPrint($title, $employees) {
    echo $this->buildSlipHTML($title, $employees, false);
    echo '<script>window.print(); setTimeout(() => window.close(), 1000);</script>';
    exit;
}

private function buildSlipHTML($title, $employees, $forDompdf = true) {
    $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>
        body{font-family:Arial,sans-serif;margin:15px;font-size:13px;background:#f9f9f9;}
        .slip{width:48%;float:left;margin:1%;border:2px solid #1e40af;padding:20px;background:white;box-sizing:border-box;}
        .header{text-align:center;font-size:18px;font-weight:bold;color:#1e40af;margin-bottom:15px;}
        table{width:100%;border-collapse:collapse;margin:10px 0;}
        th,td{border:1px solid #333;padding:8px;}
        th{background:#1e40af;color:white;text-align:left;}
        .clear{clear:both;height:20px;}
        @page{margin:0.5cm;}
    </style></head><body>';

    $count = 0;
    foreach ($employees as $emp) {
        if ($count % 2 == 0 && $count > 0) $html .= '<div class="clear"></div>';
        if ($count % 2 == 0 && $count > 0 && !$forDompdf) $html .= '<div style="page-break-before:always;"></div>';

        $isFixed = strtoupper($emp['rate_type'] ?? 'DAILY') === 'FIXED';
        $basic   = $isFixed ? floatval($emp['basic_salary'] ?? $emp['rate_amount'] ?? 0) : 0;
        // Use total_presence for correct day calculation including half days
        $days    = $emp['attendance_summary']['total_presence'] ?? 0;
        $rate    = $isFixed ? 0 : floatval($emp['rate_amount'] ?? 0);
        $earned  = $isFixed ? $basic : $days * $rate;

        $etf     = $isFixed ? $basic * 0.03 : 0;
        $epfEmp  = $isFixed ? $basic * 0.08 : 0;
        $epfComp = $isFixed ? $basic * 0.12 : 0;
        $payable = $earned + $epfComp;
        $paid    = array_sum($emp['paid_amount'] ?? [0,0,0,0]);
        $advance_paid = $emp['advance_details']['paid_amount'] ?? 0;
        $advance_deduction = $emp['advance_details']['deduction_amount'] ?? 0;
        $advance_total = $advance_paid + $advance_deduction;
        
        // Net Payable calculation
        if ($isFixed) {
            // For fixed salary: Net Payable = Payable - ETF - EPF Emp - Advance - Other Payments
            $net_payable = $payable - $etf - $epfEmp - $advance_total - $paid;
        } else {
            // For daily wage: Net Payable = Earned - Advance - Other Payments
            $net_payable = $earned - $advance_total - $paid;
        }
        
        $html .= '<div class="slip">
            <div class="header">A2Z Engineering (Pvt) Ltd<br>Salary Slip – ' . htmlspecialchars($title) . '</div>
            <p><strong>Name:</strong> ' . htmlspecialchars($emp['emp_name']) . ' | <strong>ID:</strong> ' . $emp['emp_id'] . '</p>
            <table>
                <tr><th>Earnings</th><th>Amount</th><th>Deductions</th><th>Amount</th></tr>
                <tr><td>Basic / Daily</td><td>' . number_format($earned,2) . '</td><td>ETF (3%)</td><td>' . number_format($etf,2) . '</td></tr>
                <tr><td>EPF Company (12%)</td><td>' . number_format($epfComp,2) . '</td><td>EPF Employee (8%)</td><td>' . number_format($epfEmp,2) . '</td></tr>';
                
        if ($advance_total > 0) {
            $html .= '<tr><td>Advance Payments</td><td>' . number_format($advance_total,2) . '</td><td></td><td></td></tr>';
        }
        
        $html .= '<tr><td></td><td></td><td>Advances/Other</td><td>' . number_format($paid,2) . '</td></tr>
                <tr><td><strong>Gross</strong></td><td><strong>' . number_format($payable,2) . '</strong></td>
                    <td><strong>Total Ded.</strong></td><td><strong>' . number_format($etf+$epfEmp+$paid,2) . '</strong></td></tr>
                <tr><td colspan="4" style="text-align:center;font-size:16px;background:#e6f2ff;">
                    <strong>Net Payable: LKR ' . number_format($net_payable,2) . '</strong>
                </td></tr>
            </table>
            <small>Generated: ' . date('d M Y H:i') . '</small>
        </div>';
        $count++;
    }
    $html .= '</body></html>';
    return $html;
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
            $attendance_data = $this->reportManager->getEmployeeAttendanceCosts(null, $start_date, $end_date);
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

            $total_invoices = floatval($invoices_data['total_invoices'] ?? 0);
            $total_invoices_count = intval($invoices_data['invoice_count'] ?? 0);
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
            'to_date' => $_GET['to_date'] ?? '',
            'completion' => $_GET['completion'] ?? ''
        ];
        /*
        if (empty($filters['from_date']) && empty($filters['to_date'])) {
            $filters['from_date'] = date('Y') . '-01-01';
            $filters['to_date'] = date('Y') . '-12-31';
        }
        */
        // Fetch overall data with wide date range for all-time summary
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
                    } else {
                        error_log("Invalid expense format for job_id {$row['job_id']}: " . $expense);
                        continue;
                    }
                }
            }
            $row['operational_expenses'] = array_sum($operationalExpenses);
            $row['expense_details'] = $operationalExpenses;
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
                if ($presence > 1.0) {
                    error_log("Warning: Invalid presence value {$presence} for job_id {$row['job_id']}, employee: $empName, date: $attendanceDate. Capping at 1.0");
                    $presence = 1.0;
                }
                $payment = floatval($cost['actual_cost'] ?? 0);
                if ($presence > 0 && ($cost['rate_amount'] ?? 0) == 0) {
                    error_log("Warning: No rate or increment found for job_id {$row['job_id']}, employee: $empName, date: $attendanceDate, presence: $presence");
                }
                if (!isset($employeeBreakdown[$empName]['days_by_date'][$attendanceDate])) {
                    $employeeBreakdown[$empName]['payment'] += $payment;
                    $employeeBreakdown[$empName]['days'][] = [
                        'date' => $cost['attendance_date'],
                        'presence' => $presence,
                        'payment' => $payment,
                    ];
                    $employeeBreakdown[$empName]['days_by_date'][$attendanceDate] = true;
                    $totalEmployeeCosts += $payment;
                } else {
                    error_log("Skipping duplicate attendance for job_id {$row['job_id']}, employee: $empName, date: $attendanceDate");
                }
            }
            foreach ($employeeBreakdown as $emp) {
                unset($emp['days_by_date']);
                $row['employee_details'][] = $emp;
            }
            $row['total_employee_costs'] = $totalEmployeeCosts;
            if (empty($row['employee_details']) && $totalEmployeeCosts == 0) {
                error_log("No employee costs calculated for job_id {$row['job_id']} - check attendance or rate data");
            } else {
                error_log("Employee costs for job_id {$row['job_id']}: " . json_encode($row['employee_details']));
            }
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
            $netProfit = $invoiceValue - ($totalEmployeeCosts + $row['operational_expenses']);
            $row['net_profit'] = $netProfit;
            $totalNetProfit += $netProfit;
            // Company stats
            $ref = $row['company_reference'] ?? 'Unknown';
            if (!isset($companyStats[$ref])) {
                $companyStats[$ref] = [
                    'Completed' => 0,
                    'Ongoing' => 0,
                    'Started' => 0,
                    'Not Started' => 0,
                    'Cancelled' => 0,
                    'net_profit' => 0
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
        // Group jobs by completion status
        $job_groups = [
            'Completed' => [],
            'Ongoing' => [],
            'Started' => [],
            'Not Started' => [],
            'Cancelled' => []
        ];
        foreach ($jobData as $row) {
            $status = $row['completion_status'] ?? 'Unknown';
            if (!isset($job_groups[$status])) {
                $job_groups['Unknown'][] = $row;
            } else {
                $job_groups[$status][] = $row;
            }
        }
        // Sort jobs within each group by company_reference
        foreach ($job_groups as $status => &$jobs) {
            usort($jobs, function($a, $b) {
                return strcmp($a['company_reference'] ?? '', $b['company_reference'] ?? '');
            });
        }
        unset($jobs);
        $data = [
            'username' => $_SESSION['db_username'] ?? 'Admin',
            'dbname' => 'suramalr_a2zOperationalDB',
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
        if (isset($_GET['download_csv'])) {
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

    public function maintenanceReport() {
        if (!isset($_SESSION['db_username']) || !isset($_SESSION['db_password'])) {
            header("Location: " . FULL_BASE_URL . "/login");
            exit;
        }

        try {
            $db = Database::getInstance($_SESSION['db_username'], $_SESSION['db_password']);
            
            // Define filters (only show completed jobs for A2Z Engineering - project_id = 5)
            $filters = [
                'job_id' => $_GET['job_id'] ?? '',
                'customer_reference' => $_GET['customer_name'] ?? '',
                'company_reference' => $_GET['client_ref'] ?? '',
                'completion' => '1.0', // force completed jobs only
                'project_id' => 5 // A2Z Engineering
            ];
            
            // Fetch jobs with maintenance data
            $jobs = $this->reportManager->getJobsWithMaintenanceData($filters);

            // Selected year/month filters for cycles
            $selectedYear = isset($_GET['year']) && $_GET['year'] !== '' ? intval($_GET['year']) : null;
            $selectedMonth = isset($_GET['month']) && $_GET['month'] !== '' ? intval($_GET['month']) : null;

            // Fetch maintenance schedule entries for these jobs (if any)
            $jobIds = array_map(function($j){ return $j['job_id']; }, $jobs);
            $maintenanceByJob = [];
            if (!empty($jobIds)) {
                $placeholders = implode(',', array_fill(0, count($jobIds), '?'));
                $pdo = $db->getConnection();
                $stmt = $pdo->prepare("SELECT job_id, cycle_number, DATE_FORMAT(scheduled_date, '%Y-%m-%d') AS scheduled_date, DATE_FORMAT(actual_date, '%Y-%m-%d') AS actual_date, status, description FROM maintenance_schedule WHERE job_id IN ($placeholders) ORDER BY job_id, cycle_number");
                $stmt->execute($jobIds);
                $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($schedules as $s) {
                    // If year/month filter provided, only include cycles matching scheduled_date
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
                            } catch (Exception $e) { /* ignore parse errors */ }
                        }
                        if (!$include) continue;
                    }
                    $maintenanceByJob[$s['job_id']][] = $s;
                }
            }

            // If year/month filter applied, only include jobs that have matching cycles
            if (($selectedYear || $selectedMonth) && !empty($jobs)) {
                $filteredJobs = [];
                foreach ($jobs as $j) {
                    if (!empty($maintenanceByJob[$j['job_id']])) {
                        $filteredJobs[] = $j;
                    }
                }
                $jobs = $filteredJobs;
            }
            
            // Calculate maintenance metrics
            $completed_jobs_count = 0;
            $scheduled_maintenance_count = 0;
            $due_maintenance_count = 0;
            
            foreach ($jobs as $job) {
                if ($job['completion_status'] === 'Completed') {
                    $completed_jobs_count++;
                }
                
                // Calculate maintenance cycles and count due/scheduled
                if (!empty($maintenanceByJob[$job['job_id']])) {
                        foreach ($maintenanceByJob[$job['job_id']] as $ms) {
                            $status = $ms['status'] ?? '';
                            $scheduledDateStr = $ms['scheduled_date'] ?? null;
                            $scheduledDate = null;
                            if ($scheduledDateStr) {
                                try { $scheduledDate = new DateTime($scheduledDateStr); } catch (Exception $e) { $scheduledDate = null; }
                            }

                            // If a scheduled entry's date is past today, treat it as overdue
                            if ($status === 'scheduled' && $scheduledDate && $scheduledDate < new DateTime()) {
                                $status = 'overdue';
                            }

                            if ($status === 'scheduled') {
                                $scheduled_maintenance_count++;
                            } elseif ($status === 'completed') {
                                // completed: do not count as scheduled
                            } elseif ($status === 'overdue') {
                                $due_maintenance_count++;
                            } else {
                                // cancelled or other statuses treat as due/attention
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
            
            // Get customer and company references for filters
            $customerRefs = $this->reportManager->getCustomerRefs();
            $companyRefs = $this->reportManager->getCompanyRefs();
            
            $data = [
                'username' => $_SESSION['db_username'] ?? 'Admin',
                'dbname' => 'suramalr_a2zOperationalDB',
                'jobs' => $jobs,
                'completed_jobs_count' => $completed_jobs_count,
                'scheduled_maintenance_count' => $scheduled_maintenance_count,
                'due_maintenance_count' => $due_maintenance_count,
                'customer_refs' => $customerRefs,
                'company_refs' => $companyRefs,
                'maintenance_by_job' => $maintenanceByJob,
                'filters' => $filters
            ];
            
            // Handle CSV download
            if (isset($_GET['download_csv'])) {
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
                    $installation_date = $job['date_completed'];
                    $maintenance_cycles = [];
                    
                    if ($installation_date && $installation_date !== '0000-00-00') {
                        $install_date = new DateTime($installation_date);
                        
                        for ($i = 1; $i <= 4; $i++) {
                            $cycle_date = clone $install_date;
                            $cycle_date->add(new DateInterval('P' . (6 * $i) . 'M'));
                            $maintenance_cycles[] = $cycle_date->format('Y-m-d');
                        }
                    } else {
                        for ($i = 0; $i < 4; $i++) {
                            $maintenance_cycles[] = 'N/A';
                        }
                    }
                    
                    fputcsv($output, [
                        $job['job_id'],
                        $job['customer_reference'],
                        $job['location'],
                        $job['company_reference'],
                        $job['engineer'],
                        $installation_date !== '0000-00-00' ? $installation_date : 'Not Set',
                        $job['completion_status'],
                        $maintenance_cycles[0],
                        $maintenance_cycles[1],
                        $maintenance_cycles[2],
                        $maintenance_cycles[3]
                    ]);
                }
                
                fclose($output);
                exit;
            }
            
            $this->render('reports/maintenance_report', $data);
        } catch (Exception $e) {
            error_log("Error in maintenanceReport: " . $e->getMessage());
            $this->render('reports/maintenance_report', [
                'username' => $_SESSION['db_username'] ?? 'Admin',
                'dbname' => 'suramalr_a2zOperationalDB',
                'error' => "Error generating report: " . $e->getMessage()
            ]);
        }
    }
}