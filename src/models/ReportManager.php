<?php
// src/models/ReportManager.php

class ReportManager {
    private $db;

    public function __construct() {
        $this->db = new PDO("mysql:host=localhost;dbname=operational_db", "root", "");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getCustomerRefs() {
        $stmt = $this->db->query("SELECT DISTINCT customer_reference FROM jobs WHERE customer_reference IS NOT NULL");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getCompanyRefs() {
        $stmt = $this->db->query("SELECT DISTINCT p.company_reference 
                                  FROM jobs j 
                                  LEFT JOIN projects p ON j.project_id = p.project_id 
                                  WHERE p.company_reference IS NOT NULL");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getJobCostData($filters = []) { // Removed $limit and $offset parameters
        $query = "
            SELECT 
                j.job_id, j.date_completed, j.customer_reference, j.location, j.job_capacity, j.engineer,
                p.company_reference,
                COALESCE(es.expense_summary, 'No expenses') AS expense_summary,
                id.invoice_no, id.invoice_value, id.receiving_payment, id.received_amount, id.payment_received_date
            FROM jobs j
            LEFT JOIN projects p ON j.project_id = p.project_id
            LEFT JOIN (
                SELECT job_id, GROUP_CONCAT(CONCAT(expenses_category, ': ', expense_amount) SEPARATOR ', ') AS expense_summary
                FROM operational_expenses 
                GROUP BY job_id
            ) es ON j.job_id = es.job_id
            LEFT JOIN invoice_data id ON j.job_id = id.job_id
            WHERE j.job_id != 1
        ";

        $conditions = [];
        $params = [];
        if (!empty($filters['invoice_no'])) {
            $conditions[] = "id.invoice_no LIKE :invoice_no";
            $params[':invoice_no'] = "%{$filters['invoice_no']}%";
        }
        if (!empty($filters['customer_reference'])) {
            $conditions[] = "j.customer_reference = :customer_reference";
            $params[':customer_reference'] = $filters['customer_reference'];
        }
        if (!empty($filters['company_reference'])) {
            $conditions[] = "p.company_reference = :company_reference";
            $params[':company_reference'] = $filters['company_reference'];
        }
        if (isset($filters['status'])) {
            $conditions[] = $filters['status'] === 'paid' 
                ? "id.received_amount > 0" 
                : "id.received_amount = 0 OR id.received_amount IS NULL";
        }
        if (!empty($filters['from_date'])) {
            $conditions[] = "j.date_completed >= :from_date";
            $params[':from_date'] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $conditions[] = "j.date_completed <= :to_date";
            $params[':to_date'] = $filters['to_date'];
        }

        if ($conditions) {
            $query .= " AND " . implode(" AND ", $conditions);
        }
        $query .= " GROUP BY j.job_id, j.date_completed, j.customer_reference, j.location, j.job_capacity, j.engineer, 
                    p.company_reference, id.invoice_no, id.invoice_value, id.receiving_payment, id.received_amount, 
                    id.payment_received_date ORDER BY j.job_id DESC"; // Removed LIMIT and OFFSET

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOverallSummary() {
        $stmt = $this->db->query("
            SELECT SUM(id.invoice_value) AS total_invoices, 
                   SUM(id.received_amount) AS total_paid
            FROM jobs j
            LEFT JOIN invoice_data id ON j.job_id = id.job_id
            WHERE j.job_id != 1
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getEmployeeCosts($jobId) {
        $stmt = $this->db->prepare("
            SELECT 
                e.emp_name,
                a.attendance_date,
                a.presence,
                COALESCE(
                    (SELECT si.increment_amount 
                     FROM salary_increments si 
                     WHERE si.emp_id = e.emp_id 
                     AND si.increment_date <= a.attendance_date
                     ORDER BY si.increment_date DESC 
                     LIMIT 1),
                    epr.rate_amount
                ) AS effective_rate
            FROM attendance a
            JOIN employees e ON a.emp_id = e.emp_id
            LEFT JOIN employee_payment_rates epr ON e.emp_id = epr.emp_id
                AND epr.rate_type = 'Daily'
                AND (epr.end_date IS NULL OR epr.end_date >= a.attendance_date)
                AND epr.effective_date <= a.attendance_date
            WHERE a.job_id = :job_id
            ORDER BY e.emp_name, a.attendance_date
        ");
        $stmt->bindValue(':job_id', $jobId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}