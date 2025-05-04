<?php
require_once 'src/core/Model.php';
<<<<<<< HEAD
error_log("ReportManager.php loaded at " . date('Y-m-d H:i:s'));

class ReportManager extends Model {
    public function getEmployeeRefs() {
        try {
            $stmt = $this->db->query("SELECT emp_id, emp_name FROM employees ORDER BY emp_name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getEmployeeRefs: " . $e->getMessage());
            return [];
        }
    }
=======
>>>>>>> 9b5516868da7f72121bd4e3861d1314a853078ae

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
                    a.presence,
                    COALESCE(
                        (SELECT si.increment_amount 
                         FROM salary_increments si 
                         WHERE si.emp_id = e.emp_id 
                         AND si.increment_date <= a.attendance_date
                         ORDER BY si.increment_date DESC 
                         LIMIT 1),
                        epr.rate_amount,
                        0
                    ) AS total_rate,
                    epr.rate_amount AS base_rate,
                    (SELECT si.increment_amount 
                     FROM salary_increments si 
                     WHERE si.emp_id = e.emp_id 
                     AND si.increment_date <= a.attendance_date
                     ORDER BY si.increment_date DESC 
                     LIMIT 1) AS increment_amount
                FROM attendance a
                JOIN employees e ON a.emp_id = e.emp_id
                LEFT JOIN employee_payment_rates epr ON e.emp_id = epr.emp_id
                WHERE a.job_id = :job_id
                ORDER BY e.emp_name, a.attendance_date
            ");
            $stmt->bindValue(':job_id', $jobId, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as &$result) {
                $result['actual_cost'] = floatval($result['total_rate']) * floatval($result['presence']);
            }

            return $results;

        } catch (PDOException $e) {
            error_log("Error in getEmployeeCosts for job_id $jobId: " . $e->getMessage());
            return [];
        }
    }

    public function getAttendanceCosts($start_date = null, $end_date = null) {
        $query = "
=======

public function getEmployeeCosts($jobId) {
    try {
        $stmt = $this->db->prepare("
>>>>>>> 9b5516868da7f72121bd4e3861d1314a853078ae
            SELECT 
                e.emp_id,
                e.emp_name,
                a.attendance_date,
<<<<<<< HEAD
                a.job_id,
                a.presence,
=======
                a.presence,  -- 1 for full day, 0.5 for half day, 0 for absent
>>>>>>> 9b5516868da7f72121bd4e3861d1314a853078ae
                COALESCE(
                    (SELECT si.increment_amount 
                     FROM salary_increments si 
                     WHERE si.emp_id = e.emp_id 
                     AND si.increment_date <= a.attendance_date
                     ORDER BY si.increment_date DESC 
                     LIMIT 1),
                    epr.rate_amount,
<<<<<<< HEAD
                    0
                ) AS rate_amount
            FROM attendance a
            JOIN employees e ON a.emp_id = e.emp_id
            LEFT JOIN employee_payment_rates epr ON e.emp_id = e.emp_id
                AND epr.rate_type = 'Daily'
                AND (epr.end_date IS NULL OR epr.end_date >= a.attendance_date)
                AND epr.effective_date <= a.attendance_date
            " . ($start_date ? "WHERE a.attendance_date BETWEEN :start_date AND :end_date" : "") . "
            ORDER BY e.emp_name, a.attendance_date
        ";

        try {
            $stmt = $this->db->prepare($query);
            if ($start_date) {
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
            }
            $stmt->execute();
            $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($attendanceRecords as &$record) {
                $record['actual_cost'] = $record['rate_amount'] * $record['presence'];
            }

            return $attendanceRecords;
        } catch (PDOException $e) {
            error_log("Error in getAttendanceCosts: " . $e->getMessage());
            return [];
        }
    }

    public function getWageData($filters = [])
{
    $query = "
        SELECT 
            e.emp_id,
            e.emp_name,
            e.payment_type AS emp_payment_type,
            epr.rate_type AS rate_type,
            epr.rate_amount,
            COALESCE(
                (SELECT si.increment_amount 
                 FROM salary_increments si 
                 WHERE si.emp_id = e.emp_id 
                 AND si.increment_date <= :end_date
                 ORDER BY si.increment_date DESC 
                 LIMIT 1),
                0
            ) AS increment_amount,
            a.attendance_date,
            a.job_id,
            a.presence,
            j.customer_reference,
            j.location,
            j.job_capacity,
            p.project_description,
            p.company_reference
        FROM employees e
        LEFT JOIN (
            SELECT emp_id, rate_type, rate_amount
            FROM employee_payment_rates epr1
            WHERE effective_date = (
                SELECT MAX(effective_date)
                FROM employee_payment_rates epr2
                WHERE epr2.emp_id = epr1.emp_id
                AND epr2.effective_date <= :end_date
                AND (epr2.end_date IS NULL OR epr2.end_date >= :start_date)
            )
        ) epr ON e.emp_id = epr.emp_id
        LEFT JOIN attendance a ON e.emp_id = a.emp_id
            AND a.attendance_date BETWEEN :start_date AND :end_date
        LEFT JOIN jobs j ON a.job_id = j.job_id
        LEFT JOIN projects p ON j.project_id = p.project_id
        WHERE 1 = 1
    ";

    $conditions = [];
    $params = [];

    if (!empty($filters['emp_id'])) {
        $conditions[] = "e.emp_id = :emp_id";
        $params[':emp_id'] = $filters['emp_id'];
    }

    $params[':start_date'] = $filters['from_date'] ?? '2000-01-01';
    $params[':end_date'] = $filters['to_date'] ?? '2099-12-31';

    if ($conditions) {
        $query .= " AND " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY e.emp_name, a.attendance_date";

    try {
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $employee_wages = [];

        foreach ($results as $row) {
            $emp_id = $row['emp_id'];
            $rate_type = $row['rate_type'] ?? $row['emp_payment_type'] ?? null;
            $rate_amount = floatval($row['rate_amount'] ?? 0);
            $increment_amount = floatval($row['increment_amount'] ?? 0);
            $total_rate = $rate_amount + $increment_amount;
            $presence = floatval($row['presence'] ?? 0);

            // Determine payment based on rate_type
            if ($rate_type === 'Fixed') {
                $payment = $total_rate; // fixed amount per month/day
            } else {
                $payment = $total_rate * $presence; // Daily
            }

            if (!isset($employee_wages[$emp_id])) {
                $employee_wages[$emp_id] = [
                    'emp_id' => $emp_id,
                    'emp_name' => $row['emp_name'],
                    'rate_type' => $rate_type,
                    'rate_amount' => $total_rate,
                    'total_payment' => 0,
                    'total_days' => 0,
                    'attendance_details' => []
                ];

                if (!$rate_type) {
                    error_log("Warning: Missing rate_type for emp_id {$emp_id}");
                }
            }

            if (!empty($row['attendance_date'])) {
                if ($rate_type === 'Fixed') {
                    // Count only one record for fixed payment (optional logic)
                    $employee_wages[$emp_id]['total_payment'] = $total_rate;
                    $employee_wages[$emp_id]['total_days'] = 0;
                } else {
                    $employee_wages[$emp_id]['total_payment'] += $payment;
                    $employee_wages[$emp_id]['total_days'] += $presence;
                }

                $employee_wages[$emp_id]['attendance_details'][] = [
                    'date' => $row['attendance_date'],
                    'presence' => $presence,
                    'payment' => $payment,
                    'customer_reference' => $row['customer_reference'] ?? 'N/A',
                    'location' => $row['location'] ?? 'N/A',
                    'job_capacity' => $row['job_capacity'] ?? 'N/A',
                    'project_description' => $row['project_description'] ?? 'N/A',
                    'company_reference' => $row['company_reference'] ?? 'N/A'
                ];
            }

            error_log("getWageData - Employee: {$row['emp_name']}, Rate Type: " . ($rate_type ?? 'NULL') . ", Rate Amount: {$rate_amount}, Increment: {$increment_amount}, Total Rate: {$total_rate}, Presence: {$presence}, Payment: {$payment}");
        }

        foreach ($employee_wages as $emp) {
            error_log("getWageData - Summary for Employee: {$emp['emp_name']}, Rate Type: {$emp['rate_type']}, Rate Amount: {$emp['rate_amount']}, Total Payment: {$emp['total_payment']}, Total Days: {$emp['total_days']}");
        }

        return array_values($employee_wages);
    } catch (PDOException $e) {
        error_log("Error in getWageData: " . $e->getMessage());
=======
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
>>>>>>> 9b5516868da7f72121bd4e3861d1314a853078ae
        return [];
    }
}

<<<<<<< HEAD
    public function getLaborWagesData($start_date = null, $end_date = null) {
        $query = "
            SELECT 
                job_id,
                expensed_date,
                expenses_category,
                description,
                expense_amount
            FROM operational_expenses
            WHERE expenses_category = 'Hiring of labor'
            AND description REGEXP '\([^)]+\)'
            " . ($start_date ? "AND expensed_date BETWEEN :start_date AND :end_date" : "") . "
            ORDER BY expensed_date
        ";

        try {
            $stmt = $this->db->prepare($query);
            if ($start_date) {
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
            }
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $labor_wages = [];
            $labor_sums = [];
            $labor_days = [];
            foreach ($results as $row) {
                $labor_name = 'N/A';
                if (preg_match('/\(([^)]+)\)/', $row['description'], $matches)) {
                    $labor_name = $matches[1];
                }
                $labor_wages[$labor_name][] = [
                    'job_id' => $row['job_id'],
                    'expensed_date' => $row['expensed_date'],
                    'expenses_category' => $row['expenses_category'],
                    'labor_name' => $labor_name,
                    'description' => $row['description'],
                    'expense_amount' => $row['expense_amount']
                ];
                $labor_sums[$labor_name] = ($labor_sums[$labor_name] ?? 0) + $row['expense_amount'];
                $labor_days[$labor_name][] = $row['expensed_date'];
                error_log("getLaborWagesData - Labor: $labor_name, Amount: {$row['expense_amount']}, Job ID: {$row['job_id']}, Date: {$row['expensed_date']}");
            }
            foreach ($labor_sums as $name => $total) {
                $unique_days = count(array_unique($labor_days[$name] ?? []));
                error_log("getLaborWagesData - Total for $name: $total, Days: $unique_days");
            }
            return [
                'details' => $labor_wages,
                'summations' => array_map(function($name) use ($labor_sums, $labor_days) {
                    return [
                        'labor_name' => $name,
                        'total_amount' => $labor_sums[$name],
                        'total_days' => count(array_unique($labor_days[$name] ?? []))
                    ];
                }, array_keys($labor_sums))
            ];
        } catch (PDOException $e) {
            error_log("Error in getLaborWagesData: " . $e->getMessage());
            return ['details' => [], 'summations' => []];
        }
    }
=======
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

>>>>>>> 9b5516868da7f72121bd4e3861d1314a853078ae

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
<<<<<<< HEAD
        try {
            $query = "SELECT 
                        SUM(invoice_value) AS total_invoices, 
                        SUM(received_amount) AS total_paid, 
                        COUNT(invoice_no) AS invoice_count 
                      FROM invoice_data";

            if (!empty($start_date) && !empty($end_date)) {
                $query .= " WHERE invoice_date BETWEEN :start_date AND :end_date";
            }

            $stmt = $this->db->prepare($query);

            if (!empty($start_date) && !empty($end_date)) {
                $stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
                $stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'total_invoices' => floatval($result['total_invoices'] ?? 0),
                'total_paid' => floatval($result['total_paid'] ?? 0),
                'invoice_count' => intval($result['invoice_count'] ?? 0)
            ];

        } catch (PDOException $e) {
            error_log("Error in getInvoicesSummary: " . $e->getMessage());
            return [
                'total_invoices' => 0,
                'total_paid' => 0,
                'invoice_count' => 0
            ];
=======
        $query = "SELECT SUM(invoice_value) AS total_invoices, COUNT(invoice_no) AS invoice_count 
                  FROM invoice_data 
                  " . ($start_date ? "WHERE invoice_date BETWEEN :start_date AND :end_date" : "") . "";
        try {
            $stmt = $this->db->prepare($query);
            if ($start_date) {
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getInvoicesSummary: " . $e->getMessage());
            return ['total_invoices' => 0, 'invoice_count' => 0];
>>>>>>> 9b5516868da7f72121bd4e3861d1314a853078ae
        }
    }

    public function getJobsSummary($start_date = null, $end_date = null) {
        $query = "SELECT COUNT(job_id) AS job_count, SUM(job_capacity) AS total_capacity 
                  FROM jobs 
                  " . ($start_date ? "WHERE date_completed BETWEEN :start_date AND :end_date" : "") . "";
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

    

>>>>>>> 9b5516868da7f72121bd4e3861d1314a853078ae

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