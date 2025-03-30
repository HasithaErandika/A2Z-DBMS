<?php
require_once 'src/core/Model.php';

class TableManager extends Model {
    private $allowedTables = [
        'attendance', 'employees', 'employee_bank_details', 'projects', 'jobs',
        'operational_expenses', 'invoice_data', 'employee_payments', 'salary_increments' // Note: 'Material' and 'Material_List_Per_Site' are missing from your schema
    ];

    private $tableConfigs = [
        'attendance' => [
            'editableFields' => ['presence', 'start_time', 'end_time', 'remarks'],
            'validation' => [
                'presence' => ['required', 'in:Present,Absent'],
                'attendance_date' => ['required']
            ],
            'formatters' => ['presence' => 'getBooleanIcon']
        ],
        'employees' => [
            'editableFields' => ['emp_name', 'emp_nic', 'date_of_birth', 'address', 'date_of_joined', 'date_of_resigned', 'designation', 'etf_number', 'daily_wage', 'basic_salary', 'nic_photo'],
            'validation' => [
                'emp_name' => ['required'],
                'emp_nic' => ['required']
            ]
        ],
        'jobs' => [
            'editableFields' => ['service_category', 'date_started', 'date_completed', 'company_reference', 'engineer', 'location', 'job_capacity', 'remarks', 'project_id'],
            'customDisplay' => ['job_id' => 'getJobDetails', 'invoice_exists' => 'checkInvoiceExists']
        ],
        'operational_expenses' => [
            'editableFields' => ['expensed_date', 'expenses_category', 'description', 'expense_amount', 'paid', 'remarks', 'voucher_number', 'bill'],
            'validation' => ['expense_amount' => ['required']],
            'formatters' => ['paid' => 'getBooleanIcon']
        ],
        'employee_payments' => [
            'editableFields' => ['payment_date', 'payment_type', 'paid_amount', 'remarks'],
            'validation' => ['paid_amount' => ['required']]
        ],
        'invoice_data' => [
            'editableFields' => ['invoice_no', 'invoice_date', 'invoice_value', 'invoice', 'receiving_payment', 'received_amount', 'payment_received_date', 'remarks'],
            'validation' => ['invoice_value' => ['required']]
        ],
        'projects' => [
            'editableFields' => ['project_description', 'company_reference', 'remarks'],
            'validation' => ['project_description' => ['required']]
        ],
        'salary_increments' => [
            'editableFields' => ['increment_type', 'increment_date', 'increment_amount', 'new_salary', 'reason'],
            'validation' => ['increment_amount' => ['required']]
        ],
        'employee_bank_details' => [
            'editableFields' => ['emp_name', 'acc_no', 'bank', 'branch'],
            'validation' => ['acc_no' => ['required']]
        ]
    ];

    public function getAllowedTables() {
        return $this->allowedTables;
    }

    public function getConfig($table) {
        return $this->tableConfigs[$table] ?? [];
    }

    public function getColumns($table) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        $stmt = $this->db->query("SHOW COLUMNS FROM " . $table);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getTotalEmployees() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM employees");
        return $stmt->fetchColumn();
    }
    
    public function getActiveJobs() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM jobs WHERE date_completed IS NULL");
        return $stmt->fetchColumn();
    }
    
    public function getTotalProjects() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM projects");
        return $stmt->fetchColumn();
    }
    
    public function getTotalExpenses() {
        $stmt = $this->db->query("SELECT SUM(expense_amount) FROM operational_expenses");
        $total = $stmt->fetchColumn();
        return $total ?: 0.0;
    }
    
    public function getTotalPayments() {
        $stmt = $this->db->query("SELECT SUM(paid_amount) FROM employee_payments");
        $total = $stmt->fetchColumn();
        return $total ?: 0.0;
    }
    public function getTodaysJobs() {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM jobs WHERE date_started = ? AND date_completed IS NULL");
        $stmt->execute([$today]);
        return $stmt->fetchColumn();
    }
    
    public function getTodaysExpenses() {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT SUM(expense_amount) FROM operational_expenses WHERE expensed_date = ?");
        $stmt->execute([$today]);
        $total = $stmt->fetchColumn();
        return $total ?: 0.0;
    }
    
    public function getMySQLVersion() {
        $stmt = $this->db->query("SELECT VERSION()");
        return $stmt->fetchColumn();
    }


    public function create($table, $data) {
        $config = $this->getConfig($table);
        $this->validate($table, $data, $config['validation'] ?? []);
        $keys = implode(',', array_map(fn($col) => "`$col`", array_keys($data)));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare("INSERT INTO `$table` ($keys) VALUES ($placeholders)");
        $stmt->execute(array_values($data));
    }

    public function update($table, $data, $idColumn, $id) {
        $config = $this->getConfig($table);
        $this->validate($table, $data, $config['validation'] ?? []);
        $setClause = implode(',', array_map(fn($col) => "`$col` = ?", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE `$table` SET $setClause WHERE `$idColumn` = ?");
        $values = array_values($data);
        $values[] = $id;
        $stmt->execute($values);
    }

    public function delete($table, $idColumn, $id) {
        $stmt = $this->db->prepare("DELETE FROM `$table` WHERE `$idColumn` = ?");
        $stmt->execute([$id]);
    }

    public function getEmployeeName($empId) {
        $stmt = $this->db->prepare("SELECT emp_name FROM employees WHERE emp_id = ?");
        if (!$stmt) {
            error_log("Error preparing statement for getEmployeeName");
            return 'Error';
        }
        $stmt->execute([$empId]);
        $empName = $stmt->fetchColumn();
        return $empName !== false ? htmlspecialchars($empName) : 'Unknown';
    }

    public function getBooleanIcon($value) {
        return $value === 'Present' || $value === 'Yes' || $value === true || $value === 1 ? '<i class="fas fa-check green"></i>' : '<i class="fas fa-times red"></i>';
    }

    public function getJobDetails($jobId) {
        $stmt = $this->db->prepare("SELECT service_category, company_reference FROM jobs WHERE job_id = ?");
        if (!$stmt) {
            error_log("Error preparing statement for getJobDetails");
            return 'Error - Error retrieving job details';
        }
        $stmt->execute([$jobId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            return 'No details found';
        }
        $serviceCategory = $result['service_category'] ? htmlspecialchars($result['service_category']) : '';
        $companyRef = $result['company_reference'] ? htmlspecialchars($result['company_reference']) : '';
        return "{$serviceCategory} - {$companyRef}";
    }

    public function checkInvoiceExists($jobId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM invoice_data WHERE job_id = ?");
        if (!$stmt) {
            error_log("Error preparing statement for checkInvoiceExists");
            return false;
        }
        $stmt->execute([$jobId]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function calculateTotalJobCapacity() {
        $totalCapacity = 0.0;
        $stmt = $this->db->query("SELECT job_capacity FROM jobs");
        if ($stmt === false) {
            error_log("Error executing query for calculateTotalJobCapacity");
            return 0.0;
        }
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            if (preg_match('/\d+(\.\d+)?/', $row['job_capacity'] ?? '', $matches)) {
                $totalCapacity += (float)$matches[0];
            }
        }
        return $totalCapacity;
    }
    public function getRecords($table, $page = 1, $perPage = 10) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        $allColumns = $this->getColumns($table);
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT " . implode(',', array_map(fn($col) => "`$col`", $allColumns)) . " FROM `$table` LIMIT :offset, :perPage";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchRecords($table, $searchTerm) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        $allColumns = $this->getColumns($table);
        $searchTerm = "%$searchTerm%";
        $conditions = array_map(fn($col) => "`$col` LIKE ?", $allColumns);
        $sql = "SELECT " . implode(',', array_map(fn($col) => "`$col`", $allColumns)) . " FROM `$table` WHERE " . implode(' OR ', $conditions);
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_fill(0, count($allColumns), $searchTerm));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exportRecordsToCSV($table, $startDate, $endDate) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        $config = $this->getConfig($table);
        $dateField = $config['dateField'] ?? null;
        $allColumns = $this->getColumns($table);

        $sql = "SELECT " . implode(',', array_map(fn($col) => "`$col`", $allColumns)) . " FROM `$table`";
        if ($dateField && $startDate && $endDate) {
            $sql .= " WHERE `$dateField` BETWEEN ? AND ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$startDate, $endDate]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Generate CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $table . '_records_' . date('Ymd') . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, $allColumns); // Headers
        foreach ($records as $record) {
            fputcsv($output, array_map('strval', array_values($record))); // Ensure all values are strings
        }
        fclose($output);
        exit;
    }

    private function validate($table, $data, $rules) {
        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && empty($data[$field])) {
                    throw new Exception("Field $field is required for $table");
                } elseif (strpos($rule, 'in:') === 0 && !empty($data[$field])) {
                    $allowed = explode(',', substr($rule, 3));
                    if (!in_array($data[$field], $allowed)) {
                        throw new Exception("Field $field must be one of: " . implode(', ', $allowed));
                    }
                }
            }
        }
    }
}