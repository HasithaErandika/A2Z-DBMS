<?php
require_once 'src/core/Controller.php';
require_once 'src/models/TableManager.php';

class AdminController extends Controller {
    private $tableManager;

    public function __construct() {
        if (method_exists('Controller', '__construct')) {
            parent::__construct();
        }
        $this->tableManager = new TableManager();
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
                ['link' => '/admin/wagesReport', 'icon' => 'fa-money-bill', 'title' => 'Monthly Wages', 'desc' => 'Wage summary'],
                ['link' => '/admin/expensesReport', 'icon' => 'fa-file-invoice-dollar', 'title' => 'Expenses Report', 'desc' => 'Expense analysis'],
                ['link' => '/admin/costCalculation/jobs', 'icon' => 'fa-chart-pie', 'title' => 'Site Cost Calculation', 'desc' => 'Cost breakdown'],
                ['link' => '/admin/materialFind', 'icon' => 'fa-cogs', 'title' => 'Material Cost Calculation', 'desc' => 'Material expenses'],
                ['link' => '/admin/a2zEngineeringJobs', 'icon' => 'fa-cogs', 'title' => 'A2Z Engineering Jobs', 'desc' => 'Job overview'],
            ]
        ];

        $this->render('admin/records', $data);
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

        $page = $_GET['page'] ?? 1;
        $perPage = 10;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $columns = $this->tableManager->getColumns($table);
            $idColumn = $columns[0];

            if ($action === 'get_records') {
                // Handle DataTables AJAX request
                $page = isset($_POST['start']) ? ($_POST['start'] / $_POST['length']) + 1 : 1;
                $perPage = $_POST['length'] ?? 10;
                $searchTerm = $_POST['searchTerm'] ?? '';
                $sortColumn = $_POST['sortColumn'] ?? '';
                $sortOrder = $_POST['sortOrder'] ?? 'ASC';

                $result = $this->tableManager->getPaginatedRecords($table, $page, $perPage, $searchTerm, $sortColumn, $sortOrder);

                header('Content-Type: application/json');
                echo json_encode([
                    'draw' => (int)$_POST['draw'],
                    'recordsTotal' => $result['totalRecords'],
                    'recordsFiltered' => $result['totalRecords'], // Adjust if implementing advanced filtering
                    'data' => $result['records']
                ]);
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
            }
            header("Location: " . BASE_PATH . "/admin/manageTable/" . urlencode($table) . "?page=$page");
            exit;
        }

        // Initial page load (GET request)
        $result = $this->tableManager->getPaginatedRecords($table, $page, $perPage);

        $data = [
            'table' => $table,
            'columns' => $this->tableManager->getColumns($table),
            'records' => $result['records'],
            'totalRecords' => $result['totalRecords'],
            'totalPages' => $result['totalPages'],
            'config' => $this->tableManager->getConfig($table),
            'username' => $_SESSION['username'] ?? 'Admin',
            'dbname' => 'operational_db',
            'page' => $page,
            'perPage' => $perPage
        ];

        if ($table === 'jobs') {
            $data['totalCapacity'] = $this->tableManager->calculateTotalJobCapacity();
        }

        $this->render('admin/manage_table', $data);
    }

    // Placeholder methods
    public function wagesReport() { echo "Monthly Wages Report"; }
    public function expensesReport() { echo "Expenses Report"; }
    public function costCalculation($table) { echo "Site Cost Calculation for $table"; }
    public function materialFind() { echo "Material Cost Calculation"; }
    public function a2zEngineeringJobs() { echo "A2Z Engineering Jobs"; }
}