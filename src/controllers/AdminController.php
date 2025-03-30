<?php
require_once 'src/core/Controller.php';

class AdminController extends Controller {
    public function dashboard() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header("Location: " . BASE_PATH . "/login");
            exit;
        }

        $data = [
            'username' => $_SESSION['username'],
            'dbname' => 'operational_db',
            'operationalCards' => [
                ['link' => '/admin/manage_table.php?table=Attendance', 'icon' => 'fa-calendar-check', 'title' => 'Attendance', 'desc' => 'Track employee attendance'],
                ['link' => '/admin/manage_table.php?table=Employee', 'icon' => 'fa-user-tie', 'title' => 'Employee', 'desc' => 'Manage employee records'],
                ['link' => '/admin/manage_table.php?table=Employee_Bank_Details', 'icon' => 'fa-university', 'title' => 'Employee Bank Details', 'desc' => 'Banking information'],
                ['link' => '/admin/manage_table.php?table=Projects', 'icon' => 'fa-project-diagram', 'title' => 'Projects', 'desc' => 'Project management'],
                ['link' => '/admin/manage_table.php?table=Jobs', 'icon' => 'fa-briefcase', 'title' => 'Jobs', 'desc' => 'Job assignments'],
                ['link' => '/admin/manage_table.php?table=Operational_Expenses', 'icon' => 'fa-receipt', 'title' => 'Operational Expenses', 'desc' => 'Expense tracking'],
                ['link' => '/admin/manage_table.php?table=Invoice_Data', 'icon' => 'fa-file-invoice', 'title' => 'Invoice Data', 'desc' => 'Invoice records'],
                ['link' => '/admin/manage_table.php?table=Employee_Payments', 'icon' => 'fa-money-check-alt', 'title' => 'Employee Payments', 'desc' => 'Payment history'],
                ['link' => '/admin/manage_table.php?table=Salary_Increments', 'icon' => 'fa-money-check-alt', 'title' => 'Salary Increments', 'desc' => 'Salary adjustments'],
                ['link' => '/admin/manage_table.php?table=Material', 'icon' => 'fa-list', 'title' => 'Material List', 'desc' => 'Material inventory'],
                ['link' => '/admin/manage_table.php?table=Material_List_Per_Site', 'icon' => 'fa-list', 'title' => 'Material List Per Site', 'desc' => 'Site-specific materials'],
            ],
            'reportCards' => [
                ['link' => '/admin/wages_report.php', 'icon' => 'fa-money-bill', 'title' => 'Monthly Wages', 'desc' => 'Wage summary'],
                ['link' => '/admin/expenses_report.php', 'icon' => 'fa-file-invoice-dollar', 'title' => 'Expenses Report', 'desc' => 'Expense analysis'],
                ['link' => '/admin/cost_calculation.php?table=Jobs', 'icon' => 'fa-chart-pie', 'title' => 'Site Cost Calculation', 'desc' => 'Cost breakdown'],
                ['link' => '/admin/material_find.php', 'icon' => 'fa-cogs', 'title' => 'Material Cost Calculation', 'desc' => 'Material expenses'],
                ['link' => '/admin/a2z_engineering_jobs.php', 'icon' => 'fa-cogs', 'title' => 'A2Z Engineering Jobs', 'desc' => 'Job overview'],
            ]
        ];

        include_once "src/views/admin/dashboard.php";
    }

    // Placeholder methods
    public function manageTable() { echo "Manage table: " . htmlspecialchars($_GET['table'] ?? ''); }
    public function wagesReport() { echo "Monthly Wages Report"; }
    public function expensesReport() { echo "Expenses Report"; }
    public function costCalculation() { echo "Site Cost Calculation"; }
    public function materialFind() { echo "Material Cost Calculation"; }
    public function a2zEngineeringJobs() { echo "A2Z Engineering Jobs"; }
    public function users() { echo "Users page"; }
    public function sql() { echo "SQL page"; }
}