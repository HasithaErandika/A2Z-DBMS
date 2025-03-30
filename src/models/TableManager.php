<?php
require_once 'src/core/Model.php';

class TableManager extends Model {
    private $allowedTables = [
        'attendance', 'employees', 'employee_bank_details', 'projects', 'jobs',
        'operational_expenses', 'invoice_data', 'employee_payments', 'salary_increments'
    ];

    private $tableConfigs = [
        'attendance' => [
            'editableFields' => ['presence', 'start_time', 'end_time', 'remarks'],
            'validation' => [
                'presence' => ['required', 'in:0.0,0.5,1.0'],
                'attendance_date' => ['required']
            ],
            'formatters' => ['presence' => 'getPresenceDisplay', 'job_id' => 'getCustomerReferenceForJobId'],
            'searchFields' => ['attendance_date', 'presence', 'remarks'],
            'dateField' => 'attendance_date' // Added for export
        ],
        'employees' => [
            'editableFields' => ['emp_name', 'emp_nic', 'date_of_birth', 'address', 'date_of_joined', 'date_of_resigned', 'designation', 'etf_number', 'daily_wage', 'basic_salary', 'nic_photo'],
            'validation' => [
                'emp_name' => ['required'],
                'emp_nic' => ['required']
            ],
            'formatters' => ['job_id' => 'getCustomerReferenceForJobId'],
            'searchFields' => ['emp_name', 'emp_nic', 'designation']
        ],
        'jobs' => [
            'editableFields' => ['date_started', 'date_completed', 'engineer', 'location', 'job_capacity', 'remarks', 'project_id'],
            'customDisplay' => ['job_id' => 'getJobDetails', 'invoice_exists' => 'checkInvoiceExists'],
            'formatters' => ['project_id' => 'getProjectDetailsForJobs'],
            'searchFields' => ['engineer', 'location'],
            'dateField' => 'date_started' // Added for export
        ],
        'operational_expenses' => [
            'editableFields' => ['expensed_date', 'expenses_category', 'description', 'expense_amount', 'paid', 'remarks', 'voucher_number', 'bill'],
            'validation' => ['expense_amount' => ['required']],
            'formatters' => ['paid' => 'getBooleanIcon', 'job_id' => 'getCustomerReferenceForJobId'],
            'searchFields' => ['expensed_date', 'expenses_category', 'description'],
            'dateField' => 'expensed_date' // Added for export
        ],
        'employee_payments' => [
            'editableFields' => ['payment_date', 'payment_type', 'paid_amount', 'remarks'],
            'validation' => ['paid_amount' => ['required']],
            'formatters' => ['job_id' => 'getCustomerReferenceForJobId'],
            'searchFields' => ['payment_date', 'payment_type'],
            'dateField' => 'payment_date' // Added for export
        ],
        'invoice_data' => [
            'editableFields' => ['invoice_no', 'invoice_date', 'invoice_value', 'invoice', 'receiving_payment', 'received_amount', 'payment_received_date', 'remarks'],
            'validation' => ['invoice_value' => ['required']],
            'formatters' => ['job_id' => 'getCustomerReferenceForJobId'],
            'searchFields' => ['invoice_no', 'invoice_date'],
            'dateField' => 'invoice_date' // Added for export
        ],
        'projects' => [
            'editableFields' => ['project_description', 'company_reference', 'remarks'],
            'validation' => ['project_description' => ['required']],
            'formatters' => ['job_id' => 'getCustomerReferenceForJobId'],
            'searchFields' => ['project_description', 'company_reference']
        ],
        'salary_increments' => [
            'editableFields' => ['increment_type', 'increment_date', 'increment_amount', 'new_salary', 'reason'],
            'validation' => ['increment_amount' => ['required']],
            'formatters' => ['job_id' => 'getCustomerReferenceForJobId'],
            'searchFields' => ['increment_type', 'increment_date'],
            'dateField' => 'increment_date' // Added for export
        ],
        'employee_bank_details' => [
            'editableFields' => ['emp_name', 'acc_no', 'bank', 'branch'],
            'validation' => ['acc_no' => ['required']],
            'formatters' => ['job_id' => 'getCustomerReferenceForJobId'],
            'searchFields' => ['emp_name', 'acc_no', 'bank']
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
        try {
            $stmt = $this->db->query("SHOW COLUMNS FROM " . $table);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error fetching columns for $table: " . $e->getMessage());
            throw new Exception("Failed to retrieve columns for $table");
        }
    }

    public function getTotalEmployees() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM employees");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error in getTotalEmployees: " . $e->getMessage());
            return 0;
        }
    }

    public function getActiveJobs() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM jobs WHERE date_completed IS NULL");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error in getActiveJobs: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalProjects() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM projects");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error in getTotalProjects: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalExpenses() {
        try {
            $stmt = $this->db->query("SELECT SUM(expense_amount) FROM operational_expenses");
            return $stmt->fetchColumn() ?: 0.0;
        } catch (PDOException $e) {
            error_log("Error in getTotalExpenses: " . $e->getMessage());
            return 0.0;
        }
    }

    public function getTotalPayments() {
        try {
            $stmt = $this->db->query("SELECT SUM(paid_amount) FROM employee_payments");
            return $stmt->fetchColumn() ?: 0.0;
        } catch (PDOException $e) {
            error_log("Error in getTotalPayments: " . $e->getMessage());
            return 0.0;
        }
    }

    public function getTodaysJobs() {
        try {
            $today = date('Y-m-d');
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM jobs WHERE date_started = ? AND date_completed IS NULL");
            $stmt->execute([$today]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error in getTodaysJobs: " . $e->getMessage());
            return 0;
        }
    }

    public function getTodaysExpenses() {
        try {
            $today = date('Y-m-d');
            $stmt = $this->db->prepare("SELECT SUM(expense_amount) FROM operational_expenses WHERE expensed_date = ?");
            $stmt->execute([$today]);
            return $stmt->fetchColumn() ?: 0.0;
        } catch (PDOException $e) {
            error_log("Error in getTodaysExpenses: " . $e->getMessage());
            return 0.0;
        }
    }

    public function getMySQLVersion() {
        try {
            $stmt = $this->db->query("SELECT VERSION()");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error in getMySQLVersion: " . $e->getMessage());
            return 'Unknown';
        }
    }

    public function getPaginatedRecords($table, $page = 1, $perPage = 10, $searchTerm = '', $sortColumn = '', $sortOrder = 'DESC') {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }

        try {
            $allColumns = $this->getColumns($table);
            $offset = ($page - 1) * $perPage;
            $config = $this->getConfig($table);
            $idColumn = $allColumns[0]; // Primary key

            // Use DISTINCT for attendance table to avoid duplicates
            $sql = "SELECT " . ($table === 'attendance' ? 'DISTINCT ' : '') . implode(',', array_map(fn($col) => "`$col`", $allColumns)) . " FROM `$table`";
            $countSql = "SELECT COUNT(" . ($table === 'attendance' ? 'DISTINCT ' . $idColumn : '*') . ") FROM `$table`";
            $params = [];

            if ($searchTerm) {
                $searchFields = $config['searchFields'] ?? $allColumns;
                $searchTerm = "%$searchTerm%";
                $conditions = array_map(fn($col) => "`$col` LIKE ?", $searchFields);
                $sql .= " WHERE " . implode(' OR ', $conditions);
                $countSql .= " WHERE " . implode(' OR ', $conditions);
                $params = array_fill(0, count($searchFields), $searchTerm);
            }

            if ($sortColumn && in_array($sortColumn, $allColumns)) {
                $sql .= " ORDER BY `$sortColumn` " . ($sortOrder === 'ASC' ? 'ASC' : 'DESC');
            } else {
                $sql .= " ORDER BY `$idColumn` DESC";
            }

            $sql .= " LIMIT :offset, :perPage";

            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $totalRecords = $countStmt->fetchColumn();

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
            foreach ($params as $index => $param) {
                $stmt->bindValue($index + 1, $param);
            }
            $stmt->execute();
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($records as &$record) {
                foreach ($allColumns as $column) {
                    if (isset($config['customDisplay'][$column])) {
                        $record[$column] = $this->{$config['customDisplay'][$column]}($record[$allColumns[0]]);
                    } elseif (isset($config['formatters'][$column])) {
                        $record[$column] = $this->{$config['formatters'][$column]}($record[$column]);
                    }
                }
                if ($table === 'jobs') {
                    $record['invoice_exists'] = $this->checkInvoiceExists($record['job_id']);
                }
            }
            unset($record);

            // Log for debugging
            error_log("Fetched " . count($records) . " records for $table, page $page, perPage $perPage");

            return [
                'records' => $records,
                'totalRecords' => $totalRecords,
                'totalPages' => ceil($totalRecords / $perPage)
            ];
        } catch (PDOException $e) {
            error_log("Error in getPaginatedRecords for $table: " . $e->getMessage());
            throw new Exception("Failed to retrieve records from $table");
        }
    }

    public function create($table, $data) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        try {
            $config = $this->getConfig($table);
            $this->validate($table, $data, $config['validation'] ?? []);
            $keys = implode(',', array_map(fn($col) => "`$col`", array_keys($data)));
            $placeholders = implode(',', array_fill(0, count($data), '?'));
            $stmt = $this->db->prepare("INSERT INTO `$table` ($keys) VALUES ($placeholders)");
            $stmt->execute(array_values($data));
        } catch (PDOException $e) {
            error_log("Error in create for $table: " . $e->getMessage());
            throw new Exception("Failed to create record in $table");
        }
    }

    public function update($table, $data, $idColumn, $id) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        try {
            $config = $this->getConfig($table);
            $this->validate($table, $data, $config['validation'] ?? []);
            $setClause = implode(',', array_map(fn($col) => "`$col` = ?", array_keys($data)));
            $stmt = $this->db->prepare("UPDATE `$table` SET $setClause WHERE `$idColumn` = ?");
            $values = array_values($data);
            $values[] = $id;
            $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Error in update for $table: " . $e->getMessage());
            throw new Exception("Failed to update record in $table");
        }
    }

    public function delete($table, $idColumn, $id) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        try {
            $stmt = $this->db->prepare("DELETE FROM `$table` WHERE `$idColumn` = ?");
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error in delete for $table: " . $e->getMessage());
            throw new Exception("Failed to delete record from $table");
        }
    }

    public function getEmployeeName($empId) {
        try {
            $stmt = $this->db->prepare("SELECT emp_name FROM employees WHERE emp_id = ?");
            $stmt->execute([$empId]);
            $empName = $stmt->fetchColumn();
            return $empName !== false ? htmlspecialchars($empName) : 'Unknown';
        } catch (PDOException $e) {
            error_log("Error in getEmployeeName: " . $e->getMessage());
            return 'Error';
        }
    }

    public function getBooleanIcon($value) {
        return $value === 'Yes' || $value === true || $value === 1 ? '<i class="fas fa-check green"></i>' : '<i class="fas fa-times red"></i>';
    }

    public function getPresenceDisplay($value) {
        if ($value == 1.0) return '<span style="color: #10B981;">Full Day</span>';
        if ($value == 0.5) return '<span style="color: #F59E0B;">Half Day</span>';
        if ($value == 0.0) return '<span style="color: #EF4444;">Not Attended</span>';
        return htmlspecialchars($value); // Fallback
    }

    public function getJobDetails($jobId) {
        try {
            $stmt = $this->db->prepare("SELECT engineer FROM jobs WHERE job_id = ?");
            $stmt->execute([$jobId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result && $result['engineer'] ? htmlspecialchars($result['engineer']) : 'No Engineer';
        } catch (PDOException $e) {
            error_log("Error in getJobDetails: " . $e->getMessage());
            return 'Error';
        }
    }

    public function getCustomerReferenceForJobId($jobId) {
        if (empty($jobId)) return 'No Job ID';
        try {
            $stmt = $this->db->prepare("SELECT customer_reference FROM jobs WHERE job_id = ?");
            $stmt->execute([$jobId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result && $result['customer_reference'] ? htmlspecialchars($result['customer_reference']) : 'No Customer Reference';
        } catch (PDOException $e) {
            error_log("Error in getCustomerReferenceForJobId: " . $e->getMessage());
            return 'Error';
        }
    }

    public function getProjectDetailsForJobs($projectId) {
        if (empty($projectId)) return 'No Project ID';
        try {
            $stmt = $this->db->prepare("SELECT company_reference, project_description FROM projects WHERE project_id = ?");
            $stmt->execute([$projectId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) return 'No Project Found';
            $companyRef = $result['company_reference'] ? htmlspecialchars($result['company_reference']) : 'No Company';
            $projectDesc = $result['project_description'] ? htmlspecialchars($result['project_description']) : 'No Description';
            return "$companyRef - $projectDesc";
        } catch (PDOException $e) {
            error_log("Error in getProjectDetailsForJobs: " . $e->getMessage());
            return 'Error';
        }
    }

    public function checkInvoiceExists($jobId) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM invoice_data WHERE job_id = ?");
            $stmt->execute([$jobId]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error in checkInvoiceExists: " . $e->getMessage());
            return false;
        }
    }

    public function calculateTotalJobCapacity() {
        try {
            $stmt = $this->db->query("SELECT job_capacity FROM jobs");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $totalCapacity = 0.0;
            foreach ($results as $row) {
                if (preg_match('/\d+(\.\d+)?/', $row['job_capacity'] ?? '', $matches)) {
                    $totalCapacity += (float)$matches[0];
                }
            }
            return $totalCapacity;
        } catch (PDOException $e) {
            error_log("Error in calculateTotalJobCapacity: " . $e->getMessage());
            return 0.0;
        }
    }

    public function getRecords($table, $page = 1, $perPage = 10) {
        $result = $this->getPaginatedRecords($table, $page, $perPage);
        return $result['records'];
    }

    public function searchRecords($table, $searchTerm) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        try {
            $config = $this->getConfig($table);
            $searchFields = $config['searchFields'] ?? $this->getColumns($table);
            $searchTerm = "%$searchTerm%";
            $conditions = array_map(fn($col) => "`$col` LIKE ?", $searchFields);
            $sql = "SELECT " . implode(',', array_map(fn($col) => "`$col`", $this->getColumns($table))) . " FROM `$table` WHERE " . implode(' OR ', $conditions);
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_fill(0, count($searchFields), $searchTerm));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in searchRecords for $table: " . $e->getMessage());
            throw new Exception("Failed to search records in $table");
        }
    }

    public function exportRecordsToCSV($table, $startDate, $endDate) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        try {
            $config = $this->getConfig($table);
            $dateField = $config['dateField'] ?? $this->getColumns($table)[0]; // Fallback to first column
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

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename="' . $table . '_records_' . date('Ymd') . '.csv"');
            $output = fopen('php://output', 'w');
            fputcsv($output, $allColumns);
            foreach ($records as $record) {
                fputcsv($output, array_map('strval', array_values($record)));
            }
            fclose($output);
            exit;
        } catch (PDOException $e) {
            error_log("Error in exportRecordsToCSV for $table: " . $e->getMessage());
            throw new Exception("Failed to export records from $table");
        }
    }

    private function validate($table, $data, $rules) {
        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && (!isset($data[$field]) || $data[$field] === '')) {
                    throw new Exception("Field $field is required for $table");
                } elseif (strpos($rule, 'in:') === 0 && isset($data[$field]) && $data[$field] !== '') {
                    $allowed = explode(',', substr($rule, 3));
                    if (!in_array($data[$field], $allowed)) {
                        throw new Exception("Field $field must be one of: " . implode(', ', $allowed));
                    }
                }
            }
        }
    }
}