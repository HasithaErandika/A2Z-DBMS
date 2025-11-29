<?php
require_once 'src/core/Model.php';
error_log("ReportManager.php loaded at " . date('Y-m-d H:i:s'));

class ReportManager extends Model {
    /**
 * Retrieves a list of working employee IDs and names, sorted by employee name.
 * Excludes employees who have resigned.
 * 
 * @return array An array of associative arrays containing emp_id and emp_name, or an empty array on error.
 */
public function getEmployeeRefs() {
    try {
        $stmt = $this->db->query("
            SELECT emp_id, emp_name
            FROM employees
            WHERE date_of_resigned = '0000-00-00'
            ORDER BY emp_name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in getEmployeeRefs: " . $e->getMessage());
        return [];
    }
}


    /**
     * Retrieves a list of unique customer references from the jobs table.
     * @return array An array of customer references, or an empty array on error.
     */
    public function getCustomerRefs() {
        try {
            $stmt = $this->db->query("SELECT DISTINCT customer_reference FROM jobs WHERE customer_reference IS NOT NULL");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error in getCustomerRefs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves a list of unique company references from projects linked to jobs.
     * @return array An array of company references, or an empty array on error.
     */
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

    /**
 * Retrieves job cost data with optional filters, limit, and offset for pagination.
 * Includes completion status mapping.
 * 
 * @param array $filters Associative array of filters (e.g., invoice_no, customer_reference, status, completion, etc.).
 * @param int $limit Number of records to return (default: 50).
 * @param int $offset Starting point for pagination (default: 0).
 * @return array An array of job cost data, or an empty array on error.
 */
public function getJobCostData($filters = [], $limit = 50, $offset = 0) {
    $query = "
        SELECT 
            j.job_id, j.date_completed, j.customer_reference, j.location, j.job_capacity, j.engineer,
            p.company_reference,
            COALESCE(es.expense_summary, 'No expenses') AS expense_summary,
            id.invoice_no, id.invoice_value, id.receiving_payment, id.received_amount, id.payment_received_date,
            CASE j.completion
                WHEN 0.0 THEN 'Not Started'
                WHEN 0.1 THEN 'Cancelled'
                WHEN 0.2 THEN 'Started'
                WHEN 0.5 THEN 'Ongoing'
                WHEN 1.0 THEN 'Completed'
                ELSE 'Unknown'
            END AS completion_status
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
            : "(id.received_amount = 0 OR id.received_amount IS NULL)";
    }
    if (!empty($filters['from_date'])) {
        $conditions[] = "(j.date_completed >= :from_date OR j.date_completed = '0000-00-00')";
        $params[':from_date'] = $filters['from_date'];
    }
    if (!empty($filters['to_date'])) {
        $conditions[] = "(j.date_completed <= :to_date OR j.date_completed = '0000-00-00')";
        $params[':to_date'] = $filters['to_date'];
    }
    if (!empty($filters['completion'])) {
        $conditions[] = "j.completion = :completion";
        $params[':completion'] = $filters['completion'];
    }

    if ($conditions) {
        $query .= " AND " . implode(" AND ", $conditions);
    }

    $query .= " 
        GROUP BY j.job_id, j.date_completed, j.customer_reference, j.location, j.job_capacity, j.engineer, 
                 p.company_reference, id.invoice_no, id.invoice_value, id.receiving_payment, id.received_amount, 
                 id.payment_received_date, j.completion
        ORDER BY j.job_id DESC LIMIT :limit OFFSET :offset
    ";

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


    /**
     * Retrieves a summary of total invoices and total paid amounts for all jobs.
     * @return array An associative array with total_invoices and total_paid, or defaults to 0 on error.
     */
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

   /**
 * Retrieves employee/attendance cost details.
 * - If $jobId is provided → behaves like getEmployeeCosts (excludes resigned employees).
 * - If $start_date/$end_date are provided → behaves like getAttendanceCosts (does not exclude resigned employees unless you add that filter).
 * 
 * @param int|null $jobId Job ID for filtering (optional).
 * @param string|null $start_date Start date for filtering (optional).
 * @param string|null $end_date End date for filtering (optional).
 * @return array An array of cost records with calculated actual costs, or an empty array on error.
 */
public function getEmployeeAttendanceCosts($jobId = null, $start_date = null, $end_date = null) {
    $conditions = [];
    $params = [];

    if ($jobId !== null) {
        $conditions[] = "a.job_id = :job_id";
        $params[':job_id'] = $jobId;
    }

    if ($start_date !== null && $end_date !== null) {
        $conditions[] = "a.attendance_date BETWEEN :start_date AND :end_date";
        $params[':start_date'] = $start_date;
        $params[':end_date'] = $end_date;

        // exclude resigned employees (same rule as getWageData)
        $conditions[] = "(e.date_of_resigned = '0000-00-00' OR e.date_of_resigned > :end_date)";
    } else {
        // fallback: exclude anyone resigned on/before the attendance day
        $conditions[] = "(e.date_of_resigned = '0000-00-00' OR e.date_of_resigned > a.attendance_date)";
    }

    // Only count presence = full (1) or half (0.5)
    $conditions[] = "a.presence IN (1, 0.5)";

    $whereClause = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

    $query = "
        SELECT 
            e.emp_id,
            e.emp_name,
            a.attendance_date,
            a.job_id,
            a.presence,

            -- Base daily rate as of the attendance date
            COALESCE((
                SELECT epr2.rate_amount
                FROM employee_payment_rates epr2
                WHERE epr2.emp_id = e.emp_id
                  AND epr2.rate_type = 'Daily'
                  AND epr2.effective_date <= a.attendance_date
                ORDER BY epr2.effective_date DESC
                LIMIT 1
            ), 0) AS base_rate,

            -- Latest increment as of the attendance date
            COALESCE((
                SELECT si.increment_amount
                FROM salary_increments si
                WHERE si.emp_id = e.emp_id
                  AND si.increment_date <= a.attendance_date
                ORDER BY si.increment_date DESC
                LIMIT 1
            ), 0) AS increment_amount
        FROM attendance a
        JOIN employees e ON a.emp_id = e.emp_id
        $whereClause
        ORDER BY e.emp_name, a.attendance_date
    ";

    try {
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($records as &$record) {
            $base = (float)($record['base_rate'] ?? 0);
            $inc  = (float)($record['increment_amount'] ?? 0);
            $presence = (float)($record['presence'] ?? 0);

            $record['total_rate']  = $base + $inc;
            $record['actual_cost'] = $record['total_rate'] * $presence;
        }

        return $records;
    } catch (PDOException $e) {
        error_log("Error in getEmployeeAttendanceCosts: " . $e->getMessage());
        return [];
    }
}




public function getWageData($filters = []) {
    $query = "
        SELECT 
            e.emp_id,
            e.emp_name,
            epr.rate_type,
            epr.rate_amount,
            epr.effective_date,
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
            a.presence,
            a.job_id,
            j.location,
            j.project_id,
            p.company_reference,
            j.customer_reference,
            j.job_capacity,
            p.project_description,
            -- Payment breakdown by type
            COALESCE((SELECT SUM(ep.total_amount) 
                      FROM employee_payments ep 
                      WHERE ep.emp_id = e.emp_id 
                        AND ep.payment_type = 'Monthly Salary'
                        AND ep.payment_date BETWEEN :start_date AND :end_date), 0) AS monthly_salary,
            COALESCE((SELECT SUM(ep.total_amount) 
                      FROM employee_payments ep 
                      WHERE ep.emp_id = e.emp_id 
                        AND ep.payment_type = 'Daily Wage'
                        AND ep.payment_date BETWEEN :start_date AND :end_date), 0) AS daily_wage,
            COALESCE((SELECT SUM(ep.total_amount) 
                      FROM employee_payments ep 
                      WHERE ep.emp_id = e.emp_id 
                        AND ep.payment_type = 'Advance'
                        AND ep.payment_date BETWEEN :start_date AND :end_date), 0) AS advance,
            COALESCE((SELECT SUM(ep.total_amount) 
                      FROM employee_payments ep 
                      WHERE ep.emp_id = e.emp_id 
                        AND ep.payment_type = 'Other'
                        AND ep.payment_date BETWEEN :start_date AND :end_date), 0) AS other_payments
        FROM employees e
        LEFT JOIN (
            SELECT emp_id, rate_type, rate_amount, effective_date
            FROM employee_payment_rates
            WHERE effective_date = (
                SELECT MAX(effective_date)
                FROM employee_payment_rates epr2
                WHERE epr2.emp_id = employee_payment_rates.emp_id
                  AND epr2.effective_date <= :end_date
            )
        ) epr ON e.emp_id = epr.emp_id
        LEFT JOIN attendance a ON e.emp_id = a.emp_id
            AND a.attendance_date BETWEEN :start_date AND :end_date
            AND a.presence IN (1, 0.5)
        LEFT JOIN jobs j ON a.job_id = j.job_id
        LEFT JOIN projects p ON j.project_id = p.project_id
        WHERE 1 = 1
          AND (e.date_of_resigned = '0000-00-00' OR e.date_of_resigned > :end_date)
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
            $rate_type = $row['rate_type'] ?? 'Daily';
            $rate_amount = floatval($row['rate_amount'] ?? 0);
            $increment_amount = floatval($row['increment_amount'] ?? 0);
            $total_rate = $rate_amount + $increment_amount;
            $presence = floatval($row['presence'] ?? 0);

            if ($rate_type === 'Fixed') {
                $payment = $total_rate;
            } else {
                $payment = $total_rate * $presence;
            }

            if (!isset($employee_wages[$emp_id])) {
                $employee_wages[$emp_id] = [
                    'emp_id' => $emp_id,
                    'emp_name' => $row['emp_name'],
                    'rate_type' => $rate_type,
                    'rate_amount' => $total_rate,
                    'total_payment' => 0,
                    'total_days' => 0,
                    'attendance_details' => [],
                    'basic_salary' => $rate_type === 'Fixed' ? $total_rate : 0,
                    'paid_amount' => [
                        'Monthly Salary' => floatval($row['monthly_salary']),
                        'Daily Wage' => floatval($row['daily_wage']),
                        'Advance' => floatval($row['advance']),
                        'Other' => floatval($row['other_payments'])
                    ],
                    'attendance_summary' => [
                        'presence_count' => 0,
                        'attendance_dates' => []
                    ]
                ];
            }

            if (!empty($row['attendance_date'])) {
                if ($rate_type === 'Fixed') {
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

                // Attendance summary
                $employee_wages[$emp_id]['attendance_summary']['presence_count'] += 1;
                $employee_wages[$emp_id]['attendance_summary']['attendance_dates'][] = $row['attendance_date'];
            }
        }

        return array_values($employee_wages);
    } catch (PDOException $e) {
        error_log("Error in getWageData: " . $e->getMessage());
        return [];
    }
}





    /**
     * Retrieves labor wage data for expenses categorized as 'Hiring of labor', optionally filtered by date range.
     * @param string|null $start_date Start date for filtering expenses (optional).
     * @param string|null $end_date End date for filtering expenses (optional).
     * @return array An array with details and summations of labor wages, or empty arrays on error.
     */
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

    /**
     * Retrieves total expenses grouped by category, optionally filtered by date range.
     * @param string|null $start_date Start date for filtering expenses (optional).
     * @param string|null $end_date End date for filtering expenses (optional).
     * @return array An array of expense categories with their total amounts, or an empty array on error.
     */
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

    /**
     * Retrieves a summary of invoice data, including total invoices, total paid, and invoice count, optionally filtered by date range.
     * @param string|null $start_date Start date for filtering invoices (optional).
     * @param string|null $end_date End date for filtering invoices (optional).
     * @return array An associative array with total_invoices, total_paid, and invoice_count, or defaults to 0 on error.
     */
    public function getInvoicesSummary($start_date = null, $end_date = null) {
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
        }
    }

    /**
     * Retrieves a summary of jobs, including job count and total capacity, optionally filtered by date range.
     * @param string|null $start_date Start date for filtering jobs (optional).
     * @param string|null $end_date End date for filtering jobs (optional).
     * @return array An associative array with job_count and total_capacity, or defaults to 0 on error.
     */
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

    /**
 * Calculates total EPF (Employee Provident Fund) costs for active employees,
 * optionally filtered by start date.
 * 
 * @param string|null $start_date Start date for considering active employees (optional).
 * @return float Total EPF costs, or 0 on error.
 */
public function getEPFCosts($start_date = null) {
    try {
        $query = "
            SELECT SUM(basic_salary * 0.12) AS total_epf
            FROM employees
            WHERE date_of_resigned = '0000-00-00'
               OR (:start_date IS NOT NULL AND date_of_resigned > :start_date)
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_epf'] ? floatval($result['total_epf']) : 0.0;

    } catch (PDOException $e) {
        error_log("Error in getEPFCosts: " . $e->getMessage());
        return 0.0;
    }
}

}