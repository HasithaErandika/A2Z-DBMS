<?php
require_once 'src/core/Model.php';

class ReportManager extends Model {
    public function getCustomerRefs() {
        try {
            $stmt = $this->db->query("SELECT DISTINCT customer_reference FROM jobs WHERE customer_reference IS NOT NULL");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error in getCustomerRefs: " . $e->getMessage());
            return [];
        }
    }

    public function getCompanyRefs() {
        try {
            $stmt = $this->db->query("SELECT DISTINCT p.company_reference 
                                      FROM jobs j 
                                      LEFT JOIN projects p ON j.project_id = p.project_id 
                                      WHERE p.company_reference IS NOT NULL");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error in getCompanyRefs: " . $e->getMessage());
            return [];
        }
    }

    public function getJobCostData($filters = [], $limit = 50, $offset = 0) {
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
                    id.payment_received_date ORDER BY j.job_id DESC LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getJobCostData: " . $e->getMessage());
            return [];
        }
    }

    public function getOverallSummary() {
        try {
            $stmt = $this->db->query("
                SELECT SUM(id.invoice_value) AS total_invoices, 
                       SUM(id.received_amount) AS total_paid
                FROM jobs j
                LEFT JOIN invoice_data id ON j.job_id = id.job_id
                WHERE j.job_id != 1
            ");
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getOverallSummary: " . $e->getMessage());
            return ['total_invoices' => 0, 'total_paid' => 0];
        }
    }

<<<<<<< HEAD

public function getEmployeeCosts($jobId) {
    try {
        $stmt = $this->db->prepare("
            SELECT 
                e.emp_name,
                a.attendance_date,
                a.presence,  -- 1 for full day, 0.5 for half day, 0 for absent
                COALESCE(
                    (SELECT si.increment_amount 
                     FROM salary_increments si 
                     WHERE si.emp_id = e.emp_id 
                     AND si.increment_date <= a.attendance_date
                     ORDER BY si.increment_date DESC 
                     LIMIT 1),
                    epr.rate_amount,
                    0  -- Default to 0 if no rate exists
                ) AS rate_amount,
                epr.rate_amount AS base_rate,  -- For debugging
                (SELECT si.increment_amount 
                 FROM salary_increments si 
                 WHERE si.emp_id = e.emp_id 
                 AND si.increment_date <= a.attendance_date
                 ORDER BY si.increment_date DESC 
                 LIMIT 1) AS increment_rate  -- For debugging
            FROM attendance a
            JOIN employees e ON a.emp_id = e.emp_id
            LEFT JOIN employee_payment_rates epr ON e.emp_id = epr.emp_id  -- Simplified join
            WHERE a.job_id = :job_id
            ORDER BY e.emp_name, a.attendance_date
        ");
        $stmt->bindValue(':job_id', $jobId, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate the actual cost (rate_amount * presence)
        foreach ($results as &$result) {
            $result['actual_cost'] = $result['rate_amount'] * $result['presence'];
        }

        // Enhanced logging for debugging
        foreach ($results as $result) {
            $rate = $result['rate_amount'] ?? 'NULL';
            $baseRate = $result['base_rate'] ?? 'NULL';
            $incrementRate = $result['increment_rate'] ?? 'NULL';
            $actualCost = $result['actual_cost'] ?? 'NULL';
            error_log("getEmployeeCosts for job_id $jobId - Employee: {$result['emp_name']}, Date: {$result['attendance_date']}, Presence: {$result['presence']}, Base Rate: $baseRate, Increment Rate: $incrementRate, Final Rate: $rate, Actual Cost: $actualCost");
        }

        if (empty($results)) {
            error_log("No attendance records found for job_id $jobId");
        }

        return $results;
    } catch (PDOException $e) {
        error_log("Error in getEmployeeCosts for job_id $jobId: " . $e->getMessage());
        return [];
    }
}

public function getAttendanceCosts($start_date = null, $end_date = null) {
    // SQL query to fetch attendance costs
    $query = "
        SELECT 
            COALESCE(
                (SELECT si.increment_amount 
                 FROM salary_increments si 
                 WHERE si.emp_id = e.emp_id 
                 AND si.increment_date <= a.attendance_date
                 ORDER BY si.increment_date DESC 
                 LIMIT 1),
                epr.rate_amount,
                0  -- Default to 0 if no rate exists
            ) AS rate_amount,
            a.presence  -- 1 for full day, 0.5 for half day, 0 for absent
        FROM attendance a
        JOIN employees e ON a.emp_id = e.emp_id
        LEFT JOIN employee_payment_rates epr ON e.emp_id = epr.emp_id
            AND epr.rate_type = 'Daily'
            AND (epr.end_date IS NULL OR epr.end_date >= a.attendance_date)
            AND epr.effective_date <= a.attendance_date
        " . ($start_date ? "WHERE a.attendance_date BETWEEN :start_date AND :end_date" : "") . ";
    ";

    try {
        // Prepare the SQL query
        $stmt = $this->db->prepare($query);

        // Bind the date parameters if provided
        if ($start_date) {
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
        }

        // Execute the query
        $stmt->execute();

        // Fetch all results as an associative array
        $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate actual attendance costs by multiplying rate_amount with presence
        foreach ($attendanceRecords as &$record) {
            $record['actual_cost'] = $record['rate_amount'] * $record['presence'];
        }

        return $attendanceRecords;
        
    } catch (PDOException $e) {
        // Log any exceptions that occur and return an empty array
        error_log("Error in getAttendanceCosts: " . $e->getMessage());
        return [];
    }
}

=======
    public function getEmployeeCosts($jobId) {
        try {
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
        } catch (PDOException $e) {
            error_log("Error in getEmployeeCosts for job_id $jobId: " . $e->getMessage());
            return [];
        }
    }
>>>>>>> 831347f461fd7d1dc9e7048f870560e4e0803279

    public function getExpensesByCategory($start_date = null, $end_date = null) {
        $query = "SELECT expenses_category, SUM(expense_amount) AS total_expenses 
                  FROM operational_expenses 
                  " . ($start_date ? "WHERE expensed_date BETWEEN :start_date AND :end_date" : "") . " 
                  GROUP BY expenses_category";
        try {
            $stmt = $this->db->prepare($query);
            if ($start_date) {
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getExpensesByCategory: " . $e->getMessage());
            return [];
        }
    }

    public function getInvoicesSummary($start_date = null, $end_date = null) {
        $query = "SELECT SUM(invoice_value) AS total_invoices, COUNT(invoice_no) AS invoice_count 
                  FROM invoice_data 
<<<<<<< HEAD
                  " . ($start_date ? "WHERE invoice_date BETWEEN :start_date AND :end_date" : "") . "";
=======
                  " . ($start_date ? "WHERE invoice_date BETWEEN :start_date AND :end_date" : "");
>>>>>>> 831347f461fd7d1dc9e7048f870560e4e0803279
        try {
            $stmt = $this->db->prepare($query);
            if ($start_date) {
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
            }
            $stmt->execute();
<<<<<<< HEAD
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
=======
            return $stmt->fetch(PDO::FETCH_ASSOC);
>>>>>>> 831347f461fd7d1dc9e7048f870560e4e0803279
        } catch (PDOException $e) {
            error_log("Error in getInvoicesSummary: " . $e->getMessage());
            return ['total_invoices' => 0, 'invoice_count' => 0];
        }
    }

    public function getJobsSummary($start_date = null, $end_date = null) {
        $query = "SELECT COUNT(job_id) AS job_count, SUM(job_capacity) AS total_capacity 
                  FROM jobs 
<<<<<<< HEAD
                  " . ($start_date ? "WHERE date_completed BETWEEN :start_date AND :end_date" : "") . "";
=======
                  " . ($start_date ? "WHERE date_completed BETWEEN :start_date AND :end_date" : "");
>>>>>>> 831347f461fd7d1dc9e7048f870560e4e0803279
        try {
            $stmt = $this->db->prepare($query);
            if ($start_date) {
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
            }
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getJobsSummary: " . $e->getMessage());
            return ['job_count' => 0, 'total_capacity' => 0];
        }
    }

<<<<<<< HEAD
    

=======
    public function getAttendanceCosts($start_date = null, $end_date = null) {
        $query = "
            SELECT 
                COALESCE(
                    (SELECT si.increment_amount 
                     FROM salary_increments si 
                     WHERE si.emp_id = e.emp_id 
                     AND si.increment_date <= a.attendance_date
                     ORDER BY si.increment_date DESC 
                     LIMIT 1),
                    epr.rate_amount
                ) AS effective_rate,
                a.presence
            FROM attendance a
            JOIN employees e ON a.emp_id = e.emp_id
            LEFT JOIN employee_payment_rates epr ON e.emp_id = epr.emp_id
                AND epr.rate_type = 'Daily'
                AND (epr.end_date IS NULL OR epr.end_date >= a.attendance_date)
                AND epr.effective_date <= a.attendance_date
            " . ($start_date ? "WHERE a.attendance_date BETWEEN :start_date AND :end_date" : "");
        try {
            $stmt = $this->db->prepare($query);
            if ($start_date) {
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAttendanceCosts: " . $e->getMessage());
            return [];
        }
    }
>>>>>>> 831347f461fd7d1dc9e7048f870560e4e0803279

    public function getEPFCosts($start_date = null) {
        $query = "SELECT basic_salary, date_of_resigned FROM employees";
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $epf_costs = 0;
            foreach ($results as $row) {
                $resigned_date = $row['date_of_resigned'] === '0000-00-00' ? null : $row['date_of_resigned'];
                if (!$resigned_date || ($start_date && $resigned_date > $start_date)) {
                    $epf_costs += floatval($row['basic_salary']) * 0.12;
                }
            }
            return $epf_costs;
        } catch (PDOException $e) {
            error_log("Error in getEPFCosts: " . $e->getMessage());
            return 0;
        }
    }
}