<?php
// src/models/ReportManager.php

class ReportManager {
    private $db;

    public function __construct() {
        // Adjust connection details as per your setup
        $this->db = new PDO("mysql:host=localhost;dbname=operational_db", "root", "");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Fetch total payments from employee_payments
    public function getTotalPayments() {
        $stmt = $this->db->prepare("SELECT SUM(total_amount) as total FROM employee_payments");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    // Fetch daily wage costs based on attendance and payment rates
    public function getDailyWageCosts() {
        $stmt = $this->db->prepare("
            SELECT SUM(a.presence * epr.rate_amount) as total
            FROM attendance a
            JOIN employee_payment_rates epr ON a.emp_id = epr.emp_id
            WHERE epr.rate_type = 'Daily'
            AND (epr.end_date IS NULL OR epr.end_date >= a.attendance_date)
            AND epr.effective_date <= a.attendance_date
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    // Fetch total salary increments
    public function getTotalIncrements() {
        $stmt = $this->db->prepare("SELECT SUM(increment_amount) as total FROM salary_increments");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    // Fetch per-employee cost breakdown
    public function getEmployeeCostBreakdown() {
        $stmt = $this->db->prepare("
            SELECT 
                e.emp_id, 
                e.emp_name,
                COALESCE(SUM(ep.total_amount), 0) as payment_total,
                COALESCE(SUM(si.increment_amount), 0) as increment_total,
                COALESCE((
                    SELECT SUM(a.presence * epr.rate_amount)
                    FROM attendance a
                    JOIN employee_payment_rates epr ON a.emp_id = epr.emp_id
                    WHERE a.emp_id = e.emp_id
                    AND epr.rate_type = 'Daily'
                    AND (epr.end_date IS NULL OR epr.end_date >= a.attendance_date)
                    AND epr.effective_date <= a.attendance_date
                ), 0) as daily_wage_total
            FROM employees e
            LEFT JOIN employee_payments ep ON e.emp_id = ep.emp_id
            LEFT JOIN salary_increments si ON e.emp_id = si.emp_id
            GROUP BY e.emp_id, e.emp_name
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Calculate overall cost summary
    public function getCostSummary() {
        $totalPayments = $this->getTotalPayments();
        $dailyWageCosts = $this->getDailyWageCosts();
        $totalIncrements = $this->getTotalIncrements();
        $employeeBreakdown = $this->getEmployeeCostBreakdown();

        return [
            'total_payments' => $totalPayments,
            'daily_wage_costs' => $dailyWageCosts,
            'total_increments' => $totalIncrements,
            'total_cost' => $totalPayments + $dailyWageCosts + $totalIncrements,
            'employee_breakdown' => $employeeBreakdown
        ];
    }
}