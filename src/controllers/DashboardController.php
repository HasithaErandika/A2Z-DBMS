<?php
// src/controllers/DashboardController.php

require_once 'src/core/Controller.php';

use App\Repositories\EmployeeRepository;
use App\Repositories\JobRepository;
use App\Repositories\ExpenseRepository;

class DashboardController extends Controller {
    private $employeeRepo;
    private $jobRepo;
    private $expenseRepo;

    public function __construct() {
        $this->employeeRepo = new EmployeeRepository();
        $this->jobRepo = new JobRepository();
        $this->expenseRepo = new ExpenseRepository();
    }

    public function dashboard($request = null, $response = null) {
        $data = [
            'username' => $_SESSION['db_username'] ?? 'Admin',
            'dbname' => Database::getDatabaseName(),
            'summary' => [
                'total_employees' => $this->employeeRepo->getTotalEmployees(),
                'active_jobs' => $this->jobRepo->getActiveJobsCount(),
                'total_projects' => 0,
                'total_expenses' => $this->expenseRepo->getTotalExpenses(),
                'total_payments' => $this->expenseRepo->getTotalPayments(),
                'todays_jobs' => $this->jobRepo->getTodaysJobsCount(),
                'todays_expenses' => $this->expenseRepo->getTodaysExpenses(),
            ],
            'system_info' => [
                'php_version' => phpversion(),
                'mysql_version' => '8.0',
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Apache',
                'db_name' => Database::getDatabaseName(),
            ]
        ];

        try {
            $db = Database::getInstance($_SESSION['db_username'], $_SESSION['db_password'])->getConnection();
            $stmt = $db->query("SELECT COUNT(*) FROM projects");
            $data['summary']['total_projects'] = intval($stmt->fetchColumn());
            
            $stmt = $db->query("SELECT VERSION()");
            $data['system_info']['mysql_version'] = $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("Dashboard SQL version/projects count error: " . $e->getMessage());
        }

        $this->render('admin/dashboard', $data);
    }

    public function tables($request = null, $response = null) {
        $data = [
            'username' => $_SESSION['db_username'] ?? 'Admin',
            'dbname' => Database::getDatabaseName(),
            'operationalCards' => [
                ['link' => BASE_PATH . '/admin/manageTable/attendance', 'icon' => 'fa-calendar-check', 'title' => 'Attendance', 'desc' => 'Track employee attendance'],
                ['link' => BASE_PATH . '/admin/manageTable/employees', 'icon' => 'fa-user-tie', 'title' => 'Employees', 'desc' => 'Manage employee records'],
                ['link' => BASE_PATH . '/admin/manageTable/employee_bank_details', 'icon' => 'fa-university', 'title' => 'Employee Bank Details', 'desc' => 'Banking information'],
                ['link' => BASE_PATH . '/admin/manageTable/employee_payment_rates', 'icon' => 'fa-money-check-alt', 'title' => 'Employee Payment Rates', 'desc' => 'Fixed or Daily Wage'],
                ['link' => BASE_PATH . '/admin/manageTable/projects', 'icon' => 'fa-project-diagram', 'title' => 'Projects', 'desc' => 'Project management'],
                ['link' => BASE_PATH . '/admin/manageTable/jobs', 'icon' => 'fa-briefcase', 'title' => 'Jobs', 'desc' => 'Job assignments'],
                ['link' => BASE_PATH . '/admin/manageTable/job_materials', 'icon' => 'fa-boxes', 'title' => 'Job Materials', 'desc' => 'Site-specific material lists and cost calculations'],
                ['link' => BASE_PATH . '/admin/manageTable/maintenance_schedule', 'icon' => 'fa-tools', 'title' => 'Maintenance Schedule', 'desc' => 'Scheduled maintenance cycles'],
                ['link' => BASE_PATH . '/admin/manageTable/operational_expenses', 'icon' => 'fa-receipt', 'title' => 'Operational Expenses', 'desc' => 'Expense tracking'],
                ['link' => BASE_PATH . '/admin/manageTable/invoice_data', 'icon' => 'fa-file-invoice', 'title' => 'Invoice Data', 'desc' => 'Invoice records'],
                ['link' => BASE_PATH . '/admin/manageTable/employee_payments', 'icon' => 'fa-money-check-alt', 'title' => 'Employee Payments', 'desc' => 'Payment history'],
                ['link' => BASE_PATH . '/admin/manageTable/salary_increments', 'icon' => 'fa-money-check-alt', 'title' => 'Salary Increments', 'desc' => 'Salary adjustments'],
                ['link' => BASE_PATH . '/admin/manageTable/cash_hand', 'icon' => 'fa-hand-holding-usd', 'title' => 'Cash In Hand', 'desc' => 'Manage internal cash flow transactions'],
            ]
        ];

        $this->render('admin/tables', $data);
    }
}
