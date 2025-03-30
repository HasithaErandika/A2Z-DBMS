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
            'operationalCards' => [
                ['link' => '/admin/manageTable/Attendance', 'icon' => 'fa-calendar-check', 'title' => 'Attendance', 'desc' => 'Track employee attendance'],
                ['link' => '/admin/manageTable/Employee', 'icon' => 'fa-user-tie', 'title' => 'Employee', 'desc' => 'Manage employee records'],
                ['link' => '/admin/manageTable/Employee_Bank_Details', 'icon' => 'fa-university', 'title' => 'Employee Bank Details', 'desc' => 'Banking information'],
                ['link' => '/admin/manageTable/Projects', 'icon' => 'fa-project-diagram', 'title' => 'Projects', 'desc' => 'Project management'],
                ['link' => '/admin/manageTable/Jobs', 'icon' => 'fa-briefcase', 'title' => 'Jobs', 'desc' => 'Job assignments'],
                ['link' => '/admin/manageTable/Operational_Expenses', 'icon' => 'fa-receipt', 'title' => 'Operational Expenses', 'desc' => 'Expense tracking'],
                ['link' => '/admin/manageTable/Invoice_Data', 'icon' => 'fa-file-invoice', 'title' => 'Invoice Data', 'desc' => 'Invoice records'],
                ['link' => '/admin/manageTable/Employee_Payments', 'icon' => 'fa-money-check-alt', 'title' => 'Employee Payments', 'desc' => 'Payment history'],
                ['link' => '/admin/manageTable/Salary_Increments', 'icon' => 'fa-money-check-alt', 'title' => 'Salary Increments', 'desc' => 'Salary adjustments'],
                ['link' => '/admin/manageTable/Material', 'icon' => 'fa-list', 'title' => 'Material List', 'desc' => 'Material inventory'],
                ['link' => '/admin/manageTable/Material_List_Per_Site', 'icon' => 'fa-list', 'title' => 'Material List Per Site', 'desc' => 'Site-specific materials'],
            ],
            'reportCards' => [
                ['link' => '/admin/wagesReport', 'icon' => 'fa-money-bill', 'title' => 'Monthly Wages', 'desc' => 'Wage summary'],
                ['link' => '/admin/expensesReport', 'icon' => 'fa-file-invoice-dollar', 'title' => 'Expenses Report', 'desc' => 'Expense analysis'],
                ['link' => '/admin/costCalculation/Jobs', 'icon' => 'fa-chart-pie', 'title' => 'Site Cost Calculation', 'desc' => 'Cost breakdown'],
                ['link' => '/admin/materialFind', 'icon' => 'fa-cogs', 'title' => 'Material Cost Calculation', 'desc' => 'Material expenses'],
                ['link' => '/admin/a2zEngineeringJobs', 'icon' => 'fa-cogs', 'title' => 'A2Z Engineering Jobs', 'desc' => 'Job overview'],
            ]
        ];

        $this->render('admin/dashboard', $data);
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

            if ($action === 'create') {
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
            }
            header("Location: " . BASE_PATH . "/admin/manageTable/" . urlencode($table));
            exit;
        }

        $data = [
            'table' => $table,
            'columns' => $this->tableManager->getColumns($table),
            'records' => $this->tableManager->getRecords($table),
            'username' => $_SESSION['username'] ?? 'Admin',
            'dbname' => 'operational_db'
        ];

        $this->render('admin/manage_table', $data);
    }

    // Placeholder methods
    public function wagesReport() { echo "Monthly Wages Report"; }
    public function expensesReport() { echo "Expenses Report"; }
    public function costCalculation($table) { echo "Site Cost Calculation for $table"; }
    public function materialFind() { echo "Material Cost Calculation"; }
    public function a2zEngineeringJobs() { echo "A2Z Engineering Jobs"; }
    public function users() { echo "Users page"; }
    public function sql() { echo "SQL page"; }
}