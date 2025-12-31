<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_log('Error reporting enabled in TableManager.php');
error_reporting(E_ALL);
require_once 'src/core/Model.php';
class TableManager extends Model {
    private $allowedTables = [
        'attendance', 'employees', 'employee_bank_details', 'projects', 'jobs',
        'operational_expenses', 'invoice_data', 'employee_payments', 'salary_increments',
        'employee_payment_rates', 'cash_hand', 'maintenance_schedule'
    ];
    private $tableConfigs = [
        'attendance' => [
            'editableFields' => ['emp_id', 'job_id', 'presence', 'start_time', 'end_time', 'remarks', 'attendance_date'],
            'validation' => [
                'presence' => ['required', 'in:0.0,0.5,1.0'],
                'attendance_date' => ['required'],
                'emp_id' => ['required']
            ],
            'formatters' => [
                'presence' => 'getPresenceDisplay',
                'emp_id' => 'fetchEmployeeName',
                'job_id' => 'getJobDetails'
            ],
            'searchFields' => ['attendance_date', 'presence', 'remarks', 'emp_id', 'job_id', 'start_time', 'end_time'],
            'dateField' => 'attendance_date'
        ],
        'employees' => [
            'editableFields' => ['emp_name', 'emp_nic', 'date_of_birth', 'address', 'date_of_joined', 'date_of_resigned', 'designation', 'etf_number', 'daily_wage', 'basic_salary', 'nic_photo'],
            'validation' => [
                'emp_name' => ['required'],
                'emp_nic' => ['required']
            ],
            'searchFields' => ['emp_name', 'emp_nic', 'designation', 'address', 'date_of_joined', 'date_of_resigned', 'etf_number'],
            'dateField' => 'date_of_joined'
        ],
        'employee_bank_details' => [
            'editableFields' => ['emp_id', 'job_id', 'emp_name', 'acc_no', 'bank', 'branch'],
            'validation' => [
                'acc_no' => ['required'],
                'emp_id' => ['required']
            ],
            'formatters' => [
                'emp_id' => 'fetchEmployeeName',
                'job_id' => 'getJobDetails'
            ],
            'searchFields' => ['emp_name', 'acc_no', 'bank', 'branch', 'emp_id', 'job_id'],
            'dateField' => null
        ],
        'projects' => [
            'editableFields' => ['emp_id', 'job_id', 'project_description', 'company_reference', 'remarks'],
            'validation' => [
                'project_description' => ['required'],
                'emp_id' => ['required']
            ],
            'formatters' => [
                'emp_id' => 'fetchEmployeeName',
                'job_id' => 'getJobDetails'
            ],
            'searchFields' => ['project_description', 'company_reference', 'remarks', 'emp_id', 'job_id'],
            'dateField' => null
        ],
        'jobs' => [
            'editableFields' => ['emp_id', 'project_id', 'engineer', 'date_started', 'date_completed', 'customer_reference', 'location', 'job_capacity', 'remarks', 'completion'],
            'validation' => [
                'engineer' => ['required'],
                'date_started' => ['required']
            ],
            'formatters' => [
                'project_id' => 'getProjectDetailsForJobs',
                'emp_id' => 'fetchEmployeeName',
                'completion' => 'getCompletionStatus'
            ],
            'searchFields' => ['engineer', 'location', 'customer_reference', 'date_started', 'date_completed', 'job_capacity', 'remarks', 'emp_id', 'project_id'],
            'dateField' => 'date_started'
        ],
        'operational_expenses' => [
            'editableFields' => ['emp_id', 'job_id', 'expensed_date', 'expenses_category', 'description', 'expense_amount', 'paid', 'remarks', 'voucher_number', 'bill'],
            'validation' => [
                'expense_amount' => ['required'],
                'emp_id' => ['required']
            ],
            'formatters' => [
                'paid' => 'getBooleanIcon',
                'emp_id' => 'fetchEmployeeName',
                'job_id' => 'getJobDetails'
            ],
            'searchFields' => ['expensed_date', 'expenses_category', 'description', 'expense_amount', 'paid', 'remarks', 'voucher_number', 'emp_id', 'job_id'],
            'dateField' => 'expensed_date'
        ],
        'invoice_data' => [
            'editableFields' => ['job_id', 'invoice_no', 'invoice_date', 'invoice_value', 'invoice', 'receiving_payment', 'received_amount', 'payment_received_date', 'remarks'],
            'validation' => [
                'invoice_value' => ['required'],
                'job_id' => ['required']
            ],
            'formatters' => [
                'job_id' => 'getJobDetails'
            ],
            'searchFields' => ['invoice_no', 'invoice_date', 'invoice_value', 'receiving_payment', 'received_amount', 'payment_received_date', 'remarks', 'job_id'],
            'dateField' => 'invoice_date'
        ],
        'employee_payments' => [
            'editableFields' => ['emp_id', 'job_id', 'payment_date', 'payment_type', 'paid_amount', 'remarks'],
            'validation' => [
                'paid_amount' => ['required'],
                'emp_id' => ['required']
            ],
            'formatters' => [
                'emp_id' => 'fetchEmployeeName',
                'job_id' => 'getJobDetails'
            ],
            'searchFields' => ['payment_date', 'payment_type', 'paid_amount', 'remarks', 'emp_id', 'job_id'],
            'dateField' => 'payment_date'
        ],
        'salary_increments' => [
            'editableFields' => ['emp_id', 'job_id', 'increment_type', 'increment_date', 'increment_amount', 'new_salary', 'reason'],
            'validation' => [
                'increment_amount' => ['required'],
                'emp_id' => ['required']
            ],
            'formatters' => [
                'emp_id' => 'fetchEmployeeName',
                'job_id' => 'getJobDetails'
            ],
            'searchFields' => ['increment_type', 'increment_date', 'increment_amount', 'reason', 'emp_id', 'job_id'],
            'dateField' => 'increment_date'
        ],
        'employee_payment_rates' => [
            'editableFields' => ['emp_id', 'rate_type', 'rate_amount', 'effective_date', 'end_date'],
            'validation' => [
                'emp_id' => ['required'],
                'rate_type' => ['required', 'in:Fixed,Daily'],
                'rate_amount' => ['required'],
                'effective_date' => ['required']
            ],
            'formatters' => [
                'emp_id' => 'fetchEmployeeName',
                'rate_amount' => 'formatCurrency'
            ],
            'searchFields' => ['rate_type', 'effective_date', 'end_date', 'rate_amount', 'emp_id'],
            'dateField' => 'effective_date'
        ],
        'cash_hand' => [
            'editableFields' => ['given_by', 'received_by', 'amount', 'purpose', 'transaction_type', 'txn_date', 'reference_note'],
            'validation' => [
                'given_by' => ['required'],
                'received_by' => ['required'],
                'amount' => ['required'],
                'transaction_type' => ['required', 'in:In,Out']
            ],
            'formatters' => [
                'given_by' => 'fetchEmployeeName',
                'received_by' => 'fetchEmployeeName',
                'amount' => 'formatCurrency',
                'transaction_type' => 'getTransactionTypeDisplay'
            ],
            'searchFields' => ['purpose', 'reference_note', 'txn_date', 'transaction_type', 'amount', 'given_by', 'received_by'],
            'dateField' => 'txn_date'
        ],
        'maintenance_schedule' => [
            'editableFields' => ['job_id', 'cycle_number', 'scheduled_date', 'actual_date', 'status', 'description'],
            'validation' => [
                'job_id' => ['required'],
                'cycle_number' => ['required']
            ],
            'formatters' => [
                'job_id' => 'getJobDetails'
            ],
            'searchFields' => ['job_id', 'scheduled_date', 'actual_date', 'status', 'description'],
            'dateField' => 'scheduled_date'
        ],
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
            $stmt = $this->db->query("SHOW COLUMNS FROM `$table`");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $columns;
        } catch (PDOException $e) {
            error_log("Error fetching columns for $table: " . $e->getMessage());
            throw new Exception("Failed to retrieve columns for $table: " . $e->getMessage());
        }
    }
    public function getPrimaryKey($table) {
        $primaryKeys = [
            'employees' => 'emp_id',
            'employee_payment_rates' => 'rate_id',
            'attendance' => 'attendance_id',
            'salary_increments' => 'increment_id',
            'employee_payments' => 'payment_id',
            'invoice_data' => 'invoice_id',
            'operational_expenses' => 'expense_id',
            'projects' => 'project_id',
            'employee_bank_details' => 'id',
            'jobs' => 'job_id',
            'maintenance_schedule' => 'schedule_id',
            'cash_hand' => 'cash_id'
        ];
        return $primaryKeys[$table] ?? $this->getColumns($table)[0];
    }
    public function getActiveEmployees() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM employees WHERE date_of_resigned IS NULL OR date_of_resigned = '0000-00-00'");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error in getActiveEmployees: " . $e->getMessage());
            return 'N/A';
        }
    }
    public function getTodaysAttendance() {
        try {
            $today = date('Y-m-d');
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM attendance WHERE attendance_date = ?");
            $stmt->execute([$today]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error in getTodaysAttendance: " . $e->getMessage());
            return 'N/A';
        }
    }
    public function getMonthlyExpenses() {
        try {
            $month = date('Y-m');
            $stmt = $this->db->prepare("SELECT SUM(expense_amount) FROM operational_expenses WHERE expensed_date LIKE ?");
            $stmt->execute([$month . '%']);
            return number_format($stmt->fetchColumn() ?: 0, 2);
        } catch (PDOException $e) {
            error_log("Error in getMonthlyExpenses: " . $e->getMessage());
            return 'N/A';
        }
    }
    public function getPendingPayments() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM employee_payments WHERE payment_type = ?");
            $stmt->execute(['Advance']);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error in getPendingPayments: " . $e->getMessage());
            return 'N/A';
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
    public function fetchRecords($table, $page = 1, $perPage = 10, $searchTerms = [], $sortColumn = '', $sortOrder = 'DESC', $dataTablesFormat = true, $dataOnly = false) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        try {
            $allColumns = $this->getColumns($table);
            $config = $this->getConfig($table);
            $idColumn = $this->getPrimaryKey($table);
            $dateField = $config['dateField'] ?? null;
            $selectColumns = array_map(function($col) use ($table, $dateField) {
                if ($col === $dateField) {
                    return "DATE_FORMAT(`$table`.`$col`, '%Y-%m-%d') AS `$col`";
                }
                return "`$table`.`$col`";
            }, $allColumns);
            $sql = "SELECT " . implode(',', $selectColumns);
            if ($table === 'jobs') {
                $sql .= ", p.company_reference AS company_reference, (SELECT COUNT(*) FROM invoice_data WHERE invoice_data.job_id = `$table`.job_id) > 0 AS has_invoice";
                $sql .= " FROM `$table` LEFT JOIN projects p ON `$table`.project_id = p.project_id";
            } else {
                $sql .= " FROM `$table`";
            }
            $countSql = "SELECT COUNT(*) FROM `$table`";
            $params = [];
            $conditions = [];
            if (!empty($searchTerms)) {
                $searchFields = $config['searchFields'] ?? $allColumns;
                foreach ($searchTerms as $index => $term) {
                    $term = trim($term);
                    if ($term === '') continue;
                    $termConditions = [];
                    foreach ($searchFields as $fieldIndex => $col) {
                        $paramName = ":search{$index}_{$fieldIndex}";
                        if (preg_match('/^\d+(\.\d+)?$/', $term)) {
                            $termConditions[] = "`$col` = $paramName";
                        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $term) && in_array($col, [$dateField])) {
                            $termConditions[] = "`$col` = $paramName";
                        } else {
                            $termConditions[] = "`$col` LIKE $paramName";
                            $term = "%{$term}%";
                        }
                        $params[$paramName] = $term;
                    }
                    if (!empty($termConditions)) {
                        $conditions[] = '(' . implode(' OR ', $termConditions) . ')';
                    }
                }
            }
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
                $countSql .= " WHERE " . implode(' AND ', $conditions);
            }
            if ($sortColumn && in_array($sortColumn, $allColumns)) {
                $sql .= " ORDER BY `$sortColumn` " . ($sortOrder === 'ASC' ? 'ASC' : 'DESC');
            } else {
                $sql .= " ORDER BY `$idColumn` DESC";
            }
            $offset = $perPage > 0 ? (int)(($page - 1) * $perPage) : 0;
            if ($perPage > 0) {
                $sql .= " LIMIT :offset, :perPage";
                $params[':offset'] = $offset;
                $params[':perPage'] = $perPage;
            }
            $totalStmt = $this->db->query("SELECT COUNT(*) FROM `$table`");
            $recordsTotal = $totalStmt->fetchColumn();
            $countStmt = $this->db->prepare($countSql);
            foreach ($params as $paramName => $paramValue) {
                if (strpos($paramName, ':search') === 0) {
                    $countStmt->bindValue($paramName, $paramValue, PDO::PARAM_STR);
                }
            }
            $countStmt->execute();
            $recordsFiltered = $countStmt->fetchColumn();
            $stmt = $this->db->prepare($sql);
            foreach ($params as $paramName => $paramValue) {
                $paramType = strpos($paramName, ':search') === 0 ? PDO::PARAM_STR : PDO::PARAM_INT;
                $stmt->bindValue($paramName, $paramValue, $paramType);
            }
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data = $this->applyFormatters($data, $table, $allColumns);
            if ($dataOnly) {
                return $data;
            }
            $result = [
                'recordsTotal' => (int)$recordsTotal,
                'recordsFiltered' => (int)$recordsFiltered,
                'data' => $data
            ];
            if ($dataTablesFormat) {
                $result['draw'] = isset($_POST['draw']) ? (int)$_POST['draw'] : 1;
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Error in fetchRecords for $table: " . $e->getMessage());
            throw new Exception("Failed to retrieve records from $table: " . $e->getMessage());
        }
    }
    private function applyFormatters($records, $table, $columns) {
        $config = $this->getConfig($table);
        foreach ($records as &$record) {
            foreach ($columns as $column) {
                if (isset($config['formatters'][$column]) && method_exists($this, $config['formatters'][$column])) {
                    $record[$column] = $this->{$config['formatters'][$column]}($record[$column]);
                }
            }
            if ($table === 'jobs' && isset($record['has_invoice'])) {
                $record['has_invoice'] = (bool)$record['has_invoice'];
            }
        }
        unset($record);
        return $records;
    }
    public function getInvoiceDetailsByJobId($jobId) {
        if (empty($jobId)) {
            error_log("Empty jobId passed to getInvoiceDetailsByJobId");
            return null;
        }
        try {
            $stmt = $this->db->prepare("
                SELECT
                    invoice_id,
                    job_id,
                    invoice_no,
                    DATE_FORMAT(invoice_date, '%Y-%m-%d') AS invoice_date,
                    invoice_value,
                    invoice,
                    receiving_payment,
                    received_amount,
                    DATE_FORMAT(payment_received_date, '%Y-%m-%d') AS payment_received_date,
                    remarks
                FROM invoice_data
                WHERE job_id = ?
                LIMIT 1
            ");
            $stmt->execute([$jobId]);
            $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$invoice) {
                error_log("No invoice found for job_id $jobId in getInvoiceDetailsByJobId");
                return null;
            }
            if (isset($invoice['invoice_value'])) {
                $invoice['invoice_value'] = number_format((float)$invoice['invoice_value'], 2, '.', '');
            }
            if (isset($invoice['receiving_payment'])) {
                $invoice['receiving_payment'] = number_format((float)$invoice['receiving_payment'], 2, '.', '');
            }
            if (isset($invoice['received_amount'])) {
                $invoice['received_amount'] = number_format((float)$invoice['received_amount'], 2, '.', '');
            }
            if (isset($invoice['job_id'])) {
                $jobDetails = $this->getJobDetails($invoice['job_id']);
                $invoice['job_details'] = is_array($jobDetails) ? $jobDetails : ['details' => $jobDetails];
            }
            return $invoice;
        } catch (PDOException $e) {
            error_log("Error in getInvoiceDetailsByJobId for job_id $jobId: " . $e->getMessage());
            return null;
        }
    }
    public function updateJobStatus($jobId, $newCompletion) {
        try {
            if (empty($jobId)) {
                return ['success' => false, 'error' => 'Job ID is required'];
            }
            $stmt = $this->db->prepare("SELECT completion FROM jobs WHERE job_id = ?");
            $stmt->execute([$jobId]);
            $currentCompletion = $stmt->fetchColumn();
            if ($currentCompletion === false) {
                return ['success' => false, 'error' => 'Job not found'];
            }
            $currentCompletion = (float)$currentCompletion;
            $validCompletions = [0.0, 0.1, 0.2, 0.3, 0.5, 1.0];
            $newCompletion = (float)$newCompletion;
            if (!in_array($newCompletion, $validCompletions)) {
                return ['success' => false, 'error' => 'Invalid completion value'];
            }
            $stmt = $this->db->prepare("UPDATE jobs SET completion = ? WHERE job_id = ?");
            $stmt->execute([$newCompletion, $jobId]);
            return ['success' => true, 'completion' => $newCompletion];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    public function create($table, $data) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        try {
            $this->db->beginTransaction();
            $config = $this->getConfig($table);
            $this->validate($table, $data, $config['validation'] ?? []);
            $editableFields = $config['editableFields'] ?? $this->getColumns($table);
            $filteredData = array_intersect_key($data, array_flip($editableFields));
            if (empty($filteredData)) {
                throw new Exception("No valid fields provided for insertion in $table. Submitted data: " . json_encode($data));
            }
            $columns = array_keys($filteredData);
            $placeholders = array_map(function($col) { return ":$col"; }, $columns);
            $sql = "INSERT INTO `$table` (" . implode(',', array_map(fn($col) => "`$col`", $columns)) . ")
                    VALUES (" . implode(',', $placeholders) . ")";
            $stmt = $this->db->prepare($sql);
            foreach ($filteredData as $key => $value) {
                $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            $newId = $this->db->lastInsertId();
            $this->db->commit();
            return $newId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("PDO Error in create for $table: " . $e->getMessage());
            throw new Exception("Failed to create record in $table: " . $e->getMessage());
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Validation Error in create for $table: " . $e->getMessage());
            throw $e;
        }
    }
    public function update($table, $data, $idColumn, $id) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        try {
            $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM `$table` WHERE `$idColumn` = :id");
            $checkStmt->bindValue(':id', $id, is_int($id) ? PDO::PARAM_INT : PDO::PARAM_STR);
            $checkStmt->execute();
            if ($checkStmt->fetchColumn() == 0) {
                throw new Exception("Record with $idColumn = $id not found in $table");
            }
            $config = $this->getConfig($table);
            $this->validate($table, $data, $config['validation'] ?? []);
            $editableFields = $config['editableFields'] ?? $this->getColumns($table);
            $filteredData = array_intersect_key($data, array_flip($editableFields));
            if (empty($filteredData)) {
                throw new Exception("No valid fields provided for update in $table");
            }
            $setClause = implode(',', array_map(fn($col) => "`$col` = :$col", array_keys($filteredData)));
            $sql = "UPDATE `$table` SET $setClause WHERE `$idColumn` = :id";
            $stmt = $this->db->prepare($sql);
            foreach ($filteredData as $key => $value) {
                $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->bindValue(':id', $id, is_int($id) ? PDO::PARAM_INT : PDO::PARAM_STR);
            $stmt->execute();
            $rowCount = $stmt->rowCount();
            return $rowCount > 0;
        } catch (PDOException $e) {
            error_log("Error in update for $table: " . $e->getMessage());
            throw new Exception("Failed to update record in $table: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Validation Error in update for $table: " . $e->getMessage());
            throw $e;
        }
    }
    public function delete($table, $idColumn, $id) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        if (empty($id)) {
            throw new Exception("Record ID is required for deletion in $table");
        }
        try {
            $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM `$table` WHERE `$idColumn` = ?");
            $checkStmt->execute([$id]);
            $exists = $checkStmt->fetchColumn();
            if (!$exists) {
                throw new Exception("Record with $idColumn = $id not found in $table");
            }
            $sql = "DELETE FROM `$table` WHERE `$idColumn` = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $rowCount = $stmt->rowCount();
            return $rowCount > 0;
        } catch (PDOException $e) {
            error_log("PDO Error in delete for $table with $idColumn = $id: " . $e->getMessage());
            if (strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
                throw new Exception("Cannot delete record from $table because it is referenced by other records (e.g., attendance, payments, or jobs).");
            }
            throw new Exception("Failed to delete record from $table: " . $e->getMessage());
        }
    }
    public function fetchEmployeeName($empId) {
        if (empty($empId)) return 'No Employee';
        try {
            $stmt = $this->db->prepare("SELECT emp_name FROM employees WHERE emp_id = ?");
            $stmt->execute([$empId]);
            $empName = $stmt->fetchColumn();
            return $empName !== false ? htmlspecialchars($empName) : 'Unknown Employee';
        } catch (PDOException $e) {
            error_log("Error in fetchEmployeeName: " . $e->getMessage());
            return 'Error';
        }
    }
    public function getEmployeeName($empId) {
        return $this->fetchEmployeeName($empId);
    }
    public function getBooleanIcon($value) {
        return $value === 'Yes' || $value === true || $value === 1 ? '<i class="fas fa-check" style="color: #10B981;"></i>' : '<i class="fas fa-times" style="color: #EF4444;"></i>';
    }
    public function getPresenceDisplay($value) {
        $value = (float)$value;
        if ($value == 1.0) return '<span style="color: #10B981;">Full Day</span>';
        if ($value == 0.5) return '<span style="color: #F59E0B;">Half Day</span>';
        if ($value == 0.0) return '<span style="color: #EF4444;">Not Attended</span>';
        return htmlspecialchars($value);
    }
    public function getTransactionTypeDisplay($value) {
        if ($value === 'In') {
            return '<span style="color: #10B981;">Received</span>';
        } elseif ($value === 'Out') {
            return '<span style="color: #EF4444;">Disbursed</span>';
        }
        return htmlspecialchars($value);
    }
    public function getCompletionStatus($value) {
        $value = (float)$value;
        switch ($value) {
            case 0.0: return '<span style="color: #EF4444;">Not Started</span>';
            case 0.1: return '<span style="color: #D1D5DB;">Cancelled</span>';
            case 0.2: return '<span style="color: #3B82F6;">Started</span>';
            case 0.3: return '<span style="color: #6D28D9;">Postponed</span>';
            case 0.5: return '<span style="color: #F59E0B;">Ongoing</span>';
            case 1.0: return '<span style="color: #10B981;">Completed</span>';
            default: return htmlspecialchars($value);
        }
    }
    public function getProjectDetailsForJobs($projectId = null) {
        if (!empty($projectId)) {
            try {
                $stmt = $this->db->prepare("
                    SELECT company_reference, project_description
                    FROM projects
                    WHERE project_id = ?
                ");
                $stmt->execute([$projectId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$result) {
                    return 'No Project Found';
                }
                $companyRef = !empty($result['company_reference']) ? htmlspecialchars($result['company_reference']) : 'No Company';
                $projectDesc = !empty($result['project_description']) ? htmlspecialchars($result['project_description']) : 'No Description';
                return "$companyRef - $projectDesc";
            } catch (PDOException $e) {
                error_log("Error in getProjectDetailsForJobs (details): " . $e->getMessage());
                return 'Error';
            }
        }
        try {
            $stmt = $this->db->query("
                SELECT project_id, company_reference, project_description
                FROM projects
                ORDER BY project_id DESC
            ");
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $options = ['<option value="">Select Project</option>'];
            foreach ($projects as $project) {
                $projectId = htmlspecialchars($project['project_id']);
                $companyRef = htmlspecialchars($project['company_reference']);
                $projectDesc = htmlspecialchars($project['project_description']);
                $options[] = sprintf(
                    '<option value="%s">(%s - %s - %s)</option>',
                    $projectId,
                    $projectId,
                    $companyRef,
                    $projectDesc
                );
            }
            return implode('', $options);
        } catch (PDOException $e) {
            error_log("Error in getProjectDetailsForJobs (options): " . $e->getMessage());
            return '<option value="">Error loading projects</option>';
        }
    }
    public function getJobDetails($jobId = null) {
        if (!empty($jobId)) {
            try {
                $stmt = $this->db->prepare("
                    SELECT
                        j.job_id,
                        j.engineer,
                        j.customer_reference,
                        p.project_description,
                        p.company_reference
                    FROM jobs j
                    LEFT JOIN projects p ON j.project_id = p.project_id
                    WHERE j.job_id = ?
                ");
                $stmt->execute([$jobId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$result) return 'Job Not Found';
                return sprintf(
                    '%s - %s - %s',
                    htmlspecialchars($result['project_description'] ?? 'No Description'),
                    htmlspecialchars($result['company_reference'] ?? 'No Company'),
                    htmlspecialchars($result['customer_reference'] ?? 'No Customer Ref')
                );
            } catch (PDOException $e) {
                error_log("Error in getJobDetails (details): " . $e->getMessage());
                return 'Error fetching job details';
            }
        }
        try {
            $stmt = $this->db->query("
                SELECT
                    j.job_id,
                    j.engineer,
                    j.customer_reference,
                    p.project_description,
                    p.company_reference
                FROM jobs j
                LEFT JOIN projects p ON j.project_id = p.project_id
                ORDER BY j.job_id DESC
            ");
            $options = ['<option value="">Select Job</option>'];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $jobId = htmlspecialchars($row['job_id']);
                $engineer = htmlspecialchars($row['engineer'] ?? 'No Engineer');
                $projectDesc = htmlspecialchars($row['project_description'] ?? 'No Description');
                $companyRef = htmlspecialchars($row['company_reference'] ?? 'No Company');
                $customerRef = htmlspecialchars($row['customer_reference'] ?? 'No Customer Ref');
                $options[] = sprintf(
                    '<option value="%s">(%s - %s - %s - %s - %s)</option>',
                    $jobId,
                    $jobId,
                    $projectDesc,
                    $companyRef,
                    $customerRef,
                    $engineer
                );
            }
            return implode('', $options);
        } catch (PDOException $e) {
            error_log("Error in getJobDetails (options): " . $e->getMessage());
            return '<option value="">Error loading jobs</option>';
        }
    }
    public function getEmployeeOptions() {
        try {
            $stmt = $this->db->query("SELECT emp_id, emp_name FROM employees WHERE date_of_resigned IS NULL OR date_of_resigned = '0000-00-00' ORDER BY emp_id DESC");
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $options = ['<option value="">Select Employee</option>'];
            foreach ($employees as $employee) {
                $empId = htmlspecialchars($employee['emp_id']);
                $empName = htmlspecialchars($employee['emp_name']);
                $options[] = sprintf(
                    '<option value="%s">(%s - %s)</option>',
                    $empId,
                    $empId,
                    $empName
                );
            }
            return implode('', $options);
        } catch (PDOException $e) {
            error_log("Error in getEmployeeOptions: " . $e->getMessage());
            return '<option value="">Error loading employees</option>';
        }
    }
    public function generateMaintenanceSchedulesForJob($jobId) {
        if (empty($jobId)) return ['success' => false, 'error' => 'Job ID required'];
        try {
            $stmt = $this->db->prepare("SELECT job_id, DATE_FORMAT(date_completed, '%Y-%m-%d') AS end_date, project_id FROM jobs WHERE job_id = ? LIMIT 1");
            $stmt->execute([$jobId]);
            $job = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$job) return ['success' => false, 'error' => 'Job not found'];
            if (empty($job['end_date'])) return ['success' => false, 'error' => 'Job has no completion date'];
            if ((int)$job['project_id'] !== 5) return ['success' => false, 'error' => 'Not an A2Z Engineering job'];
            $inserted = 0;
            $this->db->beginTransaction();
            $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM maintenance_schedule WHERE job_id = ? AND cycle_number = ?");
            $insertStmt = $this->db->prepare("INSERT INTO maintenance_schedule (job_id, cycle_number, scheduled_date, status, description) VALUES (?, ?, ?, 'scheduled', ?)");
            try {
                $base = new DateTime($job['end_date']);
            } catch (Exception $e) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Invalid job completion date'];
            }
            for ($cycle = 1; $cycle <= 4; $cycle++) {
                $cycleDate = clone $base;
                $months = 6 * $cycle;
                $cycleDate->modify("+{$months} months");
                $scheduled = $cycleDate->format('Y-m-d');
                $checkStmt->execute([$job['job_id'], $cycle]);
                if ($checkStmt->fetchColumn() == 0) {
                    $desc = "Auto-generated maintenance cycle {$cycle} from end_date {$job['end_date']}";
                    $insertStmt->execute([$job['job_id'], $cycle, $scheduled, $desc]);
                    $inserted += $insertStmt->rowCount();
                }
            }
            $this->db->commit();
            return ['success' => true, 'inserted' => $inserted, 'job_id' => $jobId];
        } catch (PDOException $e) {
            try { $this->db->rollBack(); } catch (Exception $ex) {}
            error_log("Error in generateMaintenanceSchedulesForJob: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    public function exportRecordsToCSV($table, $startDate, $endDate) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        $dateFormat = '/^\d{4}-\d{2}-\d{2}$/';
        if (($startDate && !preg_match($dateFormat, $startDate)) || ($endDate && !preg_match($dateFormat, $endDate))) {
            throw new Exception("Invalid date format. Use YYYY-MM-DD.");
        }
        try {
            $config = $this->getConfig($table);
            $dateField = $config['dateField'] ?? $this->getColumns($table)[0];
            $allColumns = $this->getColumns($table);
            $selectColumns = array_map(function($col) use ($table, $dateField) {
                if ($col === $dateField) {
                    return "DATE_FORMAT(`$table`.`$col`, '%Y-%m-%d') AS `$col`";
                }
                return "`$table`.`$col`";
            }, $allColumns);
            $sql = "SELECT " . implode(',', $selectColumns) . " FROM `$table`";
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
    public function formatCurrency($value) {
        return number_format((float)$value, 2, '.', '');
    }
    private function validate($table, $data, $rules) {
        $errors = [];
        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && (!isset($data[$field]) || $data[$field] === '')) {
                    $errors[] = "Field $field is required for $table";
                } elseif (strpos($rule, 'in:') === 0 && isset($data[$field]) && $data[$field] !== '') {
                    $allowedValues = explode(',', substr($rule, 3));
                    if (!in_array($data[$field], $allowedValues)) {
                        $errors[] = "Field $field in $table must be one of: " . implode(', ', $allowedValues);
                    }
                }
            }
        }
        if (!empty($errors)) {
            throw new Exception(implode('; ', $errors));
        }
    }
}
?>