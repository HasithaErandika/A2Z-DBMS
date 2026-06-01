<?php
require_once 'src/core/Model.php';

class ReportManager extends Model {
    /** Job ID to exclude from reports (e.g. test/placeholder job) */
    const EXCLUDED_JOB_ID = 1;

/**
 * Retrieves a list of working employee IDs and names, sorted by employee name.
 * Excludes employees who resigned before the given date range.
 * 
 * @param string|null $start_date Start date for filtering (optional).
 * @param string|null $end_date End date for filtering (optional).
 * @return array An array of associative arrays containing emp_id and emp_name, or an empty array on error.
 */
public function getEmployeeRefs($start_date = null, $end_date = null) {
    try {
        $query = "
            SELECT emp_id, emp_name
            FROM employees
            WHERE (date_of_resigned IS NULL OR date_of_resigned < '1970-01-02'";
        
        $params = [];
        if ($end_date) {
            $query .= " OR date_of_resigned > :end_date";
            $params[':end_date'] = $end_date;
        }
        
        $query .= ")
            ORDER BY emp_name";
        
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in getEmployeeRefs: " . $e->getMessage());
        return [];
    }
}


    public function getCustomerRefs($projectId = null) {
        try {
            if ($projectId !== null) {
                $stmt = $this->db->prepare("SELECT DISTINCT customer_reference FROM jobs WHERE customer_reference IS NOT NULL AND project_id = ?");
                $stmt->execute([$projectId]);
            } else {
                $stmt = $this->db->query("SELECT DISTINCT customer_reference FROM jobs WHERE customer_reference IS NOT NULL");
            }
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
                WHEN 0.3 THEN 'Postponed'
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
        WHERE j.job_id != " . self::EXCLUDED_JOB_ID . "
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
    if (isset($filters['status']) && $filters['status'] !== '') {
        $conditions[] = $filters['status'] === 'paid' 
            ? "id.received_amount > 0" 
            : "(id.received_amount = 0 OR id.received_amount IS NULL)";
    }
    if (!empty($filters['from_date'])) {
        $conditions[] = "(j.date_completed >= :from_date OR j.date_completed IS NULL OR j.date_completed < '1970-01-02')";
        $params[':from_date'] = $filters['from_date'];
    }
    if (!empty($filters['to_date'])) {
        $conditions[] = "(j.date_completed <= :to_date OR j.date_completed IS NULL OR j.date_completed < '1970-01-02')";
        $params[':to_date'] = $filters['to_date'];
    }
    if (!empty($filters['completion'])) {
        $completionMap = [
            'Not Started' => 0.0,
            'Cancelled' => 0.1,
            'Started' => 0.2,
            'Postponed' => 0.3,
            'Ongoing' => 0.5,
            'Completed' => 1.0
        ];
        if (isset($completionMap[$filters['completion']])) {
            $conditions[] = "j.completion = :completion";
            $params[':completion'] = $completionMap[$filters['completion']];
        }
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
                WHERE j.job_id != " . self::EXCLUDED_JOB_ID . "
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
        $conditions[] = "(e.date_of_resigned IS NULL OR e.date_of_resigned < '1970-01-02' OR e.date_of_resigned > :end_date)";
    } else {
        // fallback: exclude anyone resigned on/before the attendance day
        $conditions[] = "(e.date_of_resigned IS NULL OR e.date_of_resigned < '1970-01-02' OR e.date_of_resigned > a.attendance_date)";
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
            -- new_salary overrides the base completely; increment_amount is additive
            COALESCE((
                SELECT si.increment_amount
                FROM salary_increments si
                WHERE si.emp_id = e.emp_id
                  AND si.increment_date <= a.attendance_date
                ORDER BY si.increment_date DESC
                LIMIT 1
            ), 0) AS increment_amount,
            0 AS new_salary_override
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
            $override = (float)($record['new_salary_override'] ?? 0);
            $inc  = (float)($record['increment_amount'] ?? 0);
            $presence = (float)($record['presence'] ?? 0);

            $effective_base = $override > 0 ? $override : $base;
            $record['total_rate']  = $effective_base + $inc;
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
                        AND ep.payment_date BETWEEN :start_date AND :end_date), 0) AS other_payments,
            COALESCE((SELECT SUM(ep.paid_amount) 
                      FROM employee_payments ep 
                      WHERE ep.emp_id = e.emp_id 
                        AND ep.payment_type = 'Advance'
                        AND ep.payment_date BETWEEN :start_date AND :end_date), 0) AS advance_paid,
            COALESCE((SELECT SUM(ep.deduction_amount) 
                      FROM employee_payments ep 
                      WHERE ep.emp_id = e.emp_id 
                        AND ep.payment_type = 'Advance'
                        AND ep.payment_date BETWEEN :start_date AND :end_date), 0) AS advance_deduction
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
          AND (e.date_of_resigned IS NULL OR e.date_of_resigned < '1970-01-02' OR e.date_of_resigned > :end_date)
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
                    'advance_details' => [
                        'paid_amount' => floatval($row['advance_paid']),
                        'deduction_amount' => floatval($row['advance_deduction'])
                    ],
                    'attendance_summary' => [
                        'presence_count' => 0,
                        'attendance_dates' => []
                    ]
                ];
            }

            if (!empty($row['attendance_date'])) {
                if ($rate_type === 'Fixed') {
                    // Fixed salary: total_payment is the rate (set once during init above)
                    // Only set it here if not already set (first attendance row)
                    if ($employee_wages[$emp_id]['total_payment'] === 0) {
                        $employee_wages[$emp_id]['total_payment'] = $total_rate;
                    }
                    // Track days for reporting even though payment is fixed
                    $employee_wages[$emp_id]['total_days'] += $presence;
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

        // Separate fixed and daily wage employees
        $fixed_wage_employees = [];
        $daily_wage_employees = [];
        foreach ($employee_wages as $emp) {
            if ($emp['rate_type'] === 'Fixed') {
                $fixed_wage_employees[] = $emp;
            } else {
                $daily_wage_employees[] = $emp;
            }
        }
        
        // Update attendance summary to correctly calculate days including half days
        foreach ($employee_wages as &$emp) {
            // Sum up all presence values (1.0 for full day, 0.5 for half day, 0.0 for absent)
            $total_presence = array_sum(array_column($emp['attendance_details'], 'presence'));
            $emp['attendance_summary']['total_presence'] = $total_presence;
            
            // Count actual attendance records for reference
            $emp['attendance_summary']['record_count'] = count($emp['attendance_details']);
        }
        unset($emp);

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

            }
            foreach ($labor_sums as $name => $total) {
                $unique_days = count(array_unique($labor_days[$name] ?? []));

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
 * Returns a per-employee statutory breakdown: employer EPF (12%) + ETF (3%)
 * for every Fixed-rate employee active during the given period.
 * Also returns the grand totals.
 *
 * @param string|null $start_date
 * @param string|null $end_date
 * @return array [
 *   'rows'       => [ ['emp_id','emp_name','basic_salary','epf_employer','etf'] ... ],
 *   'total_epf'  => float,   // company EPF (12%) total
 *   'total_etf'  => float,   // company ETF  (3%) total
 *   'total'      => float,   // EPF + ETF combined
 * ]
 */
public function getStatutoryBreakdown($start_date = null, $end_date = null): array {
    try {
        $start = !empty($start_date) ? $start_date : '2022-01-01';
        $end = !empty($end_date) ? $end_date : date('Y-m-d');

        $months = [];
        $current = new DateTime($start);
        $current->modify('first day of this month');
        $target = new DateTime($end);
        $target->modify('first day of this month');

        while ($current <= $target) {
            $months[] = [
                'first_day' => $current->format('Y-m-01'),
                'last_day' => $current->format('Y-m-t')
            ];
            $current->modify('+1 month');
        }

        $empStmt = $this->db->query("SELECT emp_id, emp_name, date_of_joined, date_of_resigned FROM employees ORDER BY emp_name");
        $employees = $empStmt->fetchAll(PDO::FETCH_ASSOC);

        $rateStmt = $this->db->query("
            SELECT emp_id, rate_amount, DATE_FORMAT(effective_date, '%Y-%m-%d') AS effective_date 
            FROM employee_payment_rates 
            WHERE rate_type = 'Fixed' 
            ORDER BY emp_id, effective_date ASC
        ");
        $allRates = $rateStmt->fetchAll(PDO::FETCH_ASSOC);

        $ratesByEmp = [];
        foreach ($allRates as $r) {
            $ratesByEmp[$r['emp_id']][] = [
                'amount' => floatval($r['rate_amount']),
                'date' => $r['effective_date']
            ];
        }

        $rows = [];
        $totalEpfEmployer = 0.0;
        $totalEpfEmployee = 0.0;
        $totalEtf = 0.0;

        foreach ($employees as $emp) {
            $empId = $emp['emp_id'];
            $joinedStr = $emp['date_of_joined'];
            $resignedStr = $emp['date_of_resigned'];

            $joined = (!empty($joinedStr) && $joinedStr !== '0000-00-00') ? new DateTime($joinedStr) : null;
            $resigned = (!empty($resignedStr) && $resignedStr !== '0000-00-00' && $resignedStr !== '1970-01-01') ? new DateTime($resignedStr) : null;

            $empBasicSum = 0.0;
            $empEpfEmployeeSum = 0.0;
            $empEpfEmployerSum = 0.0;
            $empEtfSum = 0.0;
            $hasActiveMonth = false;
            $latestActiveRate = 0.0;

            foreach ($months as $m) {
                $mFirst = new DateTime($m['first_day']);
                $mLast = new DateTime($m['last_day']);

                if ($joined && $joined > $mLast) {
                    continue;
                }
                if ($resigned && $resigned < $mFirst) {
                    continue;
                }

                $effectiveRate = 0.0;
                if (isset($ratesByEmp[$empId])) {
                    foreach ($ratesByEmp[$empId] as $r) {
                        if ($r['date'] <= $m['last_day']) {
                            $effectiveRate = $r['amount'];
                        } else {
                            break;
                        }
                    }
                }

                if ($effectiveRate > 0) {
                    $hasActiveMonth = true;
                    $empBasicSum += $effectiveRate;
                    $empEpfEmployeeSum += ($effectiveRate * 0.08);
                    $empEpfEmployerSum += ($effectiveRate * 0.12);
                    $empEtfSum += ($effectiveRate * 0.03);
                    $latestActiveRate = $effectiveRate;
                }
            }

            if ($hasActiveMonth && $latestActiveRate > 0) {
                $rows[] = [
                    'emp_id'       => $empId,
                    'emp_name'     => $emp['emp_name'],
                    'basic_salary' => $latestActiveRate,
                    'epf_employee' => $latestActiveRate * 0.08,
                    'epf_employer' => $latestActiveRate * 0.12,
                    'etf'          => $latestActiveRate * 0.03,
                    'total_statutory' => $latestActiveRate * 0.15,
                ];

                $totalEpfEmployer += $empEpfEmployerSum;
                $totalEpfEmployee += $empEpfEmployeeSum;
                $totalEtf += $empEtfSum;
            }
        }

        return [
            'rows'      => $rows,
            'total_epf' => $totalEpfEmployer,
            'total_etf' => $totalEtf,
            'total'     => $totalEpfEmployer + $totalEtf,
        ];
    } catch (Exception $e) {
        error_log("Error in getStatutoryBreakdown: " . $e->getMessage());
        return ['rows' => [], 'total_epf' => 0.0, 'total_etf' => 0.0, 'total' => 0.0];
    }
}

/**
 * Backwards-compat shim so existing callers of getEPFCosts() still work.
 */
public function getEPFCosts($start_date = null, $end_date = null): float {
    return $this->getStatutoryBreakdown($start_date, $end_date)['total_epf'];
}

/**
 * Returns every operational-expense transaction row (for the auditor detail table).
 * Joins employees and jobs for context.
 *
 * @param string|null $start_date
 * @param string|null $end_date
 * @return array
 */
public function getDetailedExpenseRows($start_date = null, $end_date = null): array {
    $where = $start_date ? "WHERE oe.expensed_date BETWEEN :start_date AND :end_date" : "";
    $query = "
        SELECT
            oe.expense_id,
            oe.expensed_date,
            oe.expenses_category,
            oe.description,
            oe.expense_amount,
            oe.voucher_number,
            oe.paid,
            oe.remarks,
            oe.job_id,
            COALESCE(e.emp_name, 'N/A')       AS emp_name,
            COALESCE(j.location, 'N/A')        AS job_location,
            COALESCE(p.company_reference, 'N/A') AS company_reference
        FROM operational_expenses oe
        LEFT JOIN employees e ON oe.emp_id = e.emp_id
        LEFT JOIN jobs j      ON oe.job_id = j.job_id
        LEFT JOIN projects p  ON j.project_id = p.project_id
        $where
        ORDER BY oe.expensed_date DESC, oe.expense_id DESC
    ";
    try {
        $stmt = $this->db->prepare($query);
        if ($start_date) {
            $stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
            $stmt->bindValue(':end_date',   $end_date,   PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in getDetailedExpenseRows: " . $e->getMessage());
        return [];
    }
}



    /**
     * Retrieves jobs with maintenance data for the maintenance report
     * @param array $filters Associative array of filters (job_id, customer_reference, company_reference, completion)
     * @return array An array of job data with completion status mapping
     */
    public function getJobsWithMaintenanceData($filters = []) {
        $query = "
            SELECT 
                j.job_id, j.date_completed, j.customer_reference, j.location, j.job_capacity, j.engineer,
                p.company_reference,
                CASE j.completion
                    WHEN 0.0 THEN 'Not Started'
                    WHEN 0.1 THEN 'Cancelled'
                    WHEN 0.2 THEN 'Started'
                WHEN 0.3 THEN 'Postponed'
                    WHEN 0.5 THEN 'Ongoing'
                    WHEN 1.0 THEN 'Completed'
                    ELSE 'Unknown'
                END AS completion_status
            FROM jobs j
            LEFT JOIN projects p ON j.project_id = p.project_id
            WHERE j.job_id != " . self::EXCLUDED_JOB_ID . "
        ";

        $conditions = [];
        $params = [];

        if (!empty($filters['job_id'])) {
            $conditions[] = "j.job_id = :job_id";
            $params[':job_id'] = $filters['job_id'];
        }
        if (!empty($filters['customer_reference'])) {
            // Allow partial matches for customer search
            $conditions[] = "j.customer_reference LIKE :customer_reference";
            $params[':customer_reference'] = "%" . $filters['customer_reference'] . "%";
        }
        if (!empty($filters['company_reference'])) {
            $conditions[] = "p.company_reference = :company_reference";
            $params[':company_reference'] = $filters['company_reference'];
        }
        if (!empty($filters['project_id'])) {
            $conditions[] = "j.project_id = :project_id";
            $params[':project_id'] = $filters['project_id'];
        }
        if (isset($filters['completion'])) {
            $conditions[] = "j.completion = :completion";
            $params[':completion'] = $filters['completion'];
        }

        if ($conditions) {
            $query .= " AND " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY j.job_id DESC";

        try {
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getJobsWithMaintenanceData: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch aggregated material costs grouped by job_id
     */
    public function getJobMaterialCosts(): array {
        try {
            $stmt = $this->db->query("SELECT job_id, SUM(total_cost) AS material_cost FROM job_materials GROUP BY job_id");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $map = [];
            foreach ($rows as $r) {
                $map[$r['job_id']] = floatval($r['material_cost']);
            }
            return $map;
        } catch (PDOException $e) {
            error_log("Error in getJobMaterialCosts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch all items calculated for a specific job_id
     */
    public function getMaterialsForJob($jobId): array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM job_materials WHERE job_id = ? ORDER BY id ASC");
            $stmt->execute([$jobId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getMaterialsForJob: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total material cost sum filtered by date_completed range of active jobs
     */
    public function getMaterialCostSummary($start_date = null, $end_date = null): float {
        try {
            $query = "
                SELECT SUM(jm.total_cost) AS total_materials
                FROM job_materials jm
                JOIN jobs j ON jm.job_id = j.job_id
                WHERE 1=1
            ";
            $params = [];
            if ($start_date) {
                $query .= " AND j.date_completed >= :start_date";
                $params[':start_date'] = $start_date;
            }
            if ($end_date) {
                $query .= " AND j.date_completed <= :end_date";
                $params[':end_date'] = $end_date;
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return floatval($res['total_materials'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error in getMaterialCostSummary: " . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Fetch all active jobs for dropdown selection
     */
    public function getActiveJobsList(): array {
        try {
            $stmt = $this->db->query("
                SELECT j.job_id, j.customer_reference, j.location, j.selling_price, p.company_reference
                FROM jobs j
                LEFT JOIN projects p ON j.project_id = p.project_id
                WHERE j.project_id = 5
                ORDER BY p.company_reference ASC, j.customer_reference ASC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getActiveJobsList: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Add a single material item to a job and calculate prices automatically
     */
    public function addJobMaterialItem($jobId, $name, $qty, $unitPrice, $margin): bool {
        try {
            $margin = floatval($margin);
            if ($margin > 0 && $margin < 1) {
                $margin = round($margin * 100, 4);
            }
            if ($margin > 100) { $margin = 100; }

            // total_cost, profit_amount, final_price are GENERATED columns — MySQL computes them.
            $stmt = $this->db->prepare("
                INSERT INTO job_materials (job_id, material_name, quantity, unit_price, profit_margin)
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$jobId, $name, $qty, $unitPrice, $margin]);
        } catch (PDOException $e) {
            error_log("Error in addJobMaterialItem: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing material item and automatically calculate values
     */
    public function updateJobMaterialItem(int $id, float $qty, float $unitPrice, float $margin): bool {
        try {
            if ($margin > 0 && $margin < 1) {
                $margin = round($margin * 100, 4);
            }
            if ($margin > 100) { $margin = 100; }

            // total_cost, profit_amount, final_price are GENERATED columns — MySQL computes them.
            $stmt = $this->db->prepare("
                UPDATE job_materials 
                SET quantity = ?, unit_price = ?, profit_margin = ?
                WHERE id = ?
            ");
            return $stmt->execute([$qty, $unitPrice, $margin, $id]);
        } catch (PDOException $e) {
            error_log("Error in updateJobMaterialItem: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a single material item
     */
    public function deleteJobMaterialItem($id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM job_materials WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in deleteJobMaterialItem: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear all material items for a job
     */
    public function clearJobMaterialItems($jobId): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM job_materials WHERE job_id = ?");
            return $stmt->execute([$jobId]);
        } catch (PDOException $e) {
            error_log("Error in clearJobMaterialItems: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Bulk-import material rows from a parsed spreadsheet.
     * Each $rows entry must have: material_name, quantity, unit_price, profit_margin.
     * Returns ['inserted'=>N, 'skipped'=>N, 'errors'=>[...]]
     */
    public function importJobMaterials(int $jobId, array $rows): array {
        $inserted = 0;
        $skipped  = 0;
        $errors   = [];

        try {
            $this->db->beginTransaction();

            // total_cost, profit_amount, final_price are GENERATED columns — MySQL computes them.
            $stmt = $this->db->prepare("
                INSERT INTO job_materials
                    (job_id, material_name, quantity, unit_price, profit_margin)
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($rows as $i => $row) {
                $rowNum = $i + 2; // Excel row number (1=header)

                $name   = trim($row['material_name'] ?? '');
                $qty    = floatval($row['quantity']    ?? 0);
                $price  = floatval($row['unit_price']  ?? 0);
                $margin = floatval($row['profit_margin'] ?? 0);

                // PhpSpreadsheet reads Excel percentage-formatted cells (e.g. "10%") as 0.10.
                // Auto-scale to percentage when value is between 0 and 1 exclusive.
                if ($margin > 0 && $margin < 1) {
                    $margin = round($margin * 100, 4);
                }
                if ($margin > 100) { $margin = 100; }

                if ($name === '') {
                    $errors[] = "Row {$rowNum}: Material name is empty — skipped.";
                    $skipped++;
                    continue;
                }
                if ($qty <= 0) {
                    $errors[] = "Row {$rowNum}: Quantity must be > 0 (got {$qty}) — skipped.";
                    $skipped++;
                    continue;
                }
                if ($price <= 0) {
                    $errors[] = "Row {$rowNum}: Unit price must be > 0 (got {$price}) — skipped.";
                    $skipped++;
                    continue;
                }

                $stmt->execute([$jobId, $name, $qty, $price, $margin]);
                $inserted++;
            }

            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error in importJobMaterials: " . $e->getMessage());
            $errors[] = "Database error: " . $e->getMessage();
        }

        return compact('inserted', 'skipped', 'errors');
    }

    /**
     * Get the total system selling price for a job (set by user, overrides calculated quote)
     */
    public function getJobSellingPrice(int $jobId): ?float {
        try {
            $stmt = $this->db->prepare("SELECT selling_price FROM jobs WHERE job_id = ?");
            $stmt->execute([$jobId]);
            $val = $stmt->fetchColumn();
            return ($val !== false && $val !== null) ? floatval($val) : null;
        } catch (PDOException $e) {
            error_log("Error in getJobSellingPrice: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Set (or clear) the total system selling price for a job
     */
    public function setJobSellingPrice(int $jobId, ?float $price): bool {
        try {
            $stmt = $this->db->prepare("UPDATE jobs SET selling_price = ? WHERE job_id = ?");
            $stmt->execute([$price, $jobId]);
            return true;
        } catch (PDOException $e) {
            error_log("Error in setJobSellingPrice: " . $e->getMessage());
            return false;
        }
    }

}