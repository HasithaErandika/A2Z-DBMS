<?php
// src/controllers/AdminController.php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/TableManager.php';
require_once __DIR__ . '/../models/ReportManager.php';

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
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header("Location: " . BASE_PATH . "/login");
            exit;
        }

        $data = [
            'username' => $_SESSION['username'] ?? 'Admin',
            'dbname' => 'operational_db',
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
                'db_name' => 'operational_db',
            ]
        ];

        $this->render('admin/dashboard', $data);
    }

    public function tables() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header("Location: " . BASE_PATH . "/login");
            exit;
        }

        $data = [
            'username' => $_SESSION['username'] ?? 'Admin',
            'dbname' => 'operational_db',
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
            ]
        ];

        $this->render('admin/tables', $data);
    }

    public function records() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header("Location: " . BASE_PATH . "/login");
            exit;
        }
    
        $data = [
            'username' => $_SESSION['username'] ?? 'Admin',
            'dbname' => 'operational_db',
            'reportCards' => [
                ['link' => BASE_PATH . '/records/wages_report', 'icon' => 'fa-money-bill', 'title' => 'Monthly Wages', 'desc' => 'Wage summary'],
                ['link' => BASE_PATH . '/reports/expenses_report', 'icon' => 'fa-file-invoice-dollar', 'title' => 'Expenses Report', 'desc' => 'Expense analysis'],
                ['link' => BASE_PATH . '/reports/cost_calculation', 'icon' => 'fa-chart-pie', 'title' => 'Site Cost Calculation', 'desc' => 'Cost breakdown'],
                ['link' => BASE_PATH . '/reports/material_find', 'icon' => 'fa-cogs', 'title' => 'Material Cost Calculation', 'desc' => 'Material expenses'],
                ['link' => BASE_PATH . '/records/a2z_engineering_jobs', 'icon' => 'fa-cogs', 'title' => 'A2Z Engineering Jobs', 'desc' => 'Job overview'],
            ]
        ];
    
        $this->render('admin/reports', $data);
    }

    public function manageTable($table) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header("Location: " . BASE_PATH . "/login");
            exit;
        }

        if (!in_array($table, $this->tableManager->getAllowedTables())) {
            header("Location: " . BASE_PATH . "/admin");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $columns = $this->tableManager->getColumns($table);
            $idColumn = $columns[0];
    
            if ($action === 'get_records') {
                $draw = (int)($_POST['draw'] ?? 1);
                $start = (int)($_POST['start'] ?? 0);
                $length = (int)($_POST['length'] ?? 10);
                $page = ($start / $length) + 1;
                $searchTerm = $_POST['searchTerm'] ?? '';
                $sortColumn = $_POST['sortColumn'] ?? '';
                $sortOrder = strtoupper($_POST['sortOrder'] ?? 'DESC');

                try {
                    $result = $this->tableManager->getPaginatedRecords($table, $page, $length, $searchTerm, $sortColumn, $sortOrder);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'draw' => $draw,
                        'recordsTotal' => $result['recordsTotal'],
                        'recordsFiltered' => $result['recordsFiltered'],
                        'data' => $result['data']
                    ]);
                } catch (Exception $e) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => $e->getMessage()]);
                }
                exit;
            } elseif ($action === 'create') {
                $data = [];
                foreach ($columns as $column) {
                    if ($column !== $idColumn || !empty($_POST[$column])) {
                        $data[$column] = $_POST[$column] ?? '';
                    }
                }
                $this->tableManager->create($table, $data);
            } elseif ($action === 'update') {
                $id = $_POST['id'] ?? '';
                $data = [];
                foreach ($columns as $column) {
                    $data[$column] = $_POST[$column] ?? '';
                }
                $this->tableManager->update($table, $data, $idColumn, $id);
            } elseif ($action === 'delete') {
                $id = $_POST['id'] ?? '';
                $this->tableManager->delete($table, $idColumn, $id);
            } elseif ($action === 'export_csv') {
                $this->tableManager->exportRecordsToCSV($table, $_POST['start_date'], $_POST['end_date']);
                exit;
            } elseif ($action === 'update_status' && $table === 'jobs') {
                try {
                    $jobId = $_POST['job_id'] ?? '';
                    $newCompletion = $this->tableManager->updateJobStatus($jobId);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'completion' => $newCompletion]);
                } catch (Exception $e) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                }
                exit;
            }

            $page = $_GET['page'] ?? 1;
            header("Location: " . BASE_PATH . "/admin/manageTable/" . urlencode($table) . "?page=$page");
            exit;
        }

        $page = (int)($_GET['page'] ?? 1);
        $perPage = 10;
        $result = $this->tableManager->getPaginatedRecords($table, $page, $perPage);

        $data = [
            'table' => $table,
            'columns' => $this->tableManager->getColumns($table),
            'records' => $result['data'],
            'totalRecords' => $result['recordsTotal'],
            'totalPages' => ceil($result['recordsTotal'] / $perPage),
            'config' => $this->tableManager->getConfig($table),
            'username' => $_SESSION['username'] ?? 'Admin',
            'dbname' => 'operational_db',
            'page' => $page,
            'perPage' => $perPage,
            'tableManager' => $this->tableManager
        ];

        if ($table === 'jobs') {
            $data['totalCapacity'] = $this->tableManager->calculateTotalJobCapacity();
        }

        $this->render('admin/manage_table', $data);
    }

    public function wagesReport() { 
        echo "Monthly Wages Report"; 
    }
    
    public function expensesReport() { // Removed $table parameter
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header("Location: " . BASE_PATH . "/login");
            exit;
        }

        // Prepare data for the view (no table involvement)
        $data = [
            'username' => $_SESSION['username'] ?? 'Admin',
            'dbname' => 'operational_db',
            // Add static data if needed
        ];

        // Render the cost_calculation view
        $this->render('reports/cost_calculation', $data);
    }

    public function costCalculation() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header("Location: " . BASE_PATH . "/login");
            exit;
        }
    
        // Filters from GET request
        $filters = [
            'invoice_no' => $_GET['invoice_id'] ?? '',
            'customer_reference' => $_GET['customer_name'] ?? '',
            'company_reference' => $_GET['client_ref'] ?? '',
            'status' => $_GET['status'] ?? '',
            'from_date' => $_GET['from_date'] ?? '',
            'to_date' => $_GET['to_date'] ?? ''
        ];
    
        try {
            $customerRefs = $this->reportManager->getCustomerRefs();
            $companyRefs = $this->reportManager->getCompanyRefs();
            $jobData = $this->reportManager->getJobCostData($filters);
            $overallSummary = $this->reportManager->getOverallSummary();
    
            // Calculate summaries
            $totalInvoiceAmount = array_sum(array_column($jobData, 'invoice_value'));
            $totalPaidAmount = array_sum(array_column($jobData, 'received_amount'));
            $totalUnpaidAmount = 0;
            $unpaidInvoiceCount = 0;
            $totalExpenses = 0;
            $totalEmployeeCostsSum = 0;
            $totalCapacity = 0;
            $totalNetProfit = 0;
    
            foreach ($jobData as &$row) {
                // Operational expenses (all categories included)
                $operationalExpenses = [];
                if ($row['expense_summary'] !== 'No expenses') {
                    foreach (explode(', ', $row['expense_summary']) as $expense) {
                        [$category, $amount] = explode(': ', $expense);
                        $operationalExpenses[$category] = ($operationalExpenses[$category] ?? 0) + floatval($amount);
                    }
                }
                $row['operational_expenses'] = array_sum($operationalExpenses);
                $row['expense_details'] = $operationalExpenses;
    
                // Employee costs based solely on attendance
                $employeeCosts = $this->reportManager->getEmployeeCosts($row['job_id']);
                $totalEmployeeCosts = 0;
                $row['employee_details'] = [];
    
                // Aggregate attendance by employee
                $employeeBreakdown = [];
                foreach ($employeeCosts as $cost) {
                    $empName = $cost['emp_name'];
                    if (!isset($employeeBreakdown[$empName])) {
                        $employeeBreakdown[$empName] = [
                            'emp_name' => $empName,
                            'total_payment' => 0,
                            'days' => []
                        ];
                    }
                    $payment = ($cost['presence'] ?? 0) * ($cost['effective_rate'] ?? 0);
                    $totalEmployeeCosts += $payment;
                    $employeeBreakdown[$empName]['total_payment'] += $payment;
                    $employeeBreakdown[$empName]['days'][] = [
                        'date' => $cost['attendance_date'],
                        'presence' => $cost['presence'],
                        'payment' => $payment,
                        'rate' => $cost['effective_rate']
                    ];
                }
    
                foreach ($employeeBreakdown as $emp) {
                    $row['employee_details'][] = [
                        'emp_name' => $emp['emp_name'],
                        'payment' => $emp['total_payment'],
                        'days' => $emp['days']
                    ];
                }
                $row['total_employee_costs'] = $totalEmployeeCosts;
    
                // Summary calculations
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
            $overallDueBalance = $overallSummary['total_invoices'] - $overallSummary['total_paid'];
    
            $data = [
                'username' => $_SESSION['username'] ?? 'Admin',
                'dbname' => 'operational_db',
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
                'overall_invoice_amount' => $overallSummary['total_invoices'],
                'overall_paid_amount' => $overallSummary['total_paid'],
                'overall_unpaid_amount' => $overallDueBalance,
                'overall_unpaid_count' => count(array_filter($jobData, fn($row) => floatval($row['received_amount']) == 0)),
                'overall_due_balance' => $overallDueBalance,
                'filters' => $filters
            ];
    
            // Handle CSV download
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
                'username' => $_SESSION['username'] ?? 'Admin',
                'dbname' => 'operational_db',
                'error' => "Error generating report: " . $e->getMessage()
            ]);
        }
    }

    public function materialFind() { 
        echo "Material Cost Calculation"; 
    }
    
    public function a2zEngineeringJobs() { 
        echo "A2Z Engineering Jobs"; 
    }
}