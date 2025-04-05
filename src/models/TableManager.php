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
        'employee_payment_rates'
    ];

    private $tableConfigs = [
        'attendance' => [
            'editableFields' => ['emp_id', 'job_id', 'presence', 'start_time', 'end_time', 'remarks'],
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
            'searchFields' => ['attendance_date', 'presence', 'remarks', 'job_id'],
            'dateField' => 'attendance_date'
        ],
        'employees' => [
            'editableFields' => ['emp_name', 'emp_nic', 'date_of_birth', 'address', 'date_of_joined', 'date_of_resigned', 'designation', 'etf_number', 'daily_wage', 'basic_salary', 'nic_photo'],
            'validation' => [
                'emp_name' => ['required'],
                'emp_nic' => ['required']
            ],
            'searchFields' => ['emp_name', 'emp_nic', 'designation']
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
            'searchFields' => ['emp_name', 'acc_no', 'bank', 'job_id']
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
            'searchFields' => ['project_description', 'company_reference', 'job_id']
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
            'searchFields' => ['engineer', 'location', 'customer_reference'],
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
            'searchFields' => ['expensed_date', 'expenses_category', 'description', 'job_id'],
            'dateField' => 'expensed_date'
        ],
        'invoice_data' => [
            'editableFields' => ['emp_id', 'job_id', 'invoice_no', 'invoice_date', 'invoice_value', 'invoice', 'receiving_payment', 'received_amount', 'payment_received_date', 'remarks'],
            'validation' => [
                'invoice_value' => ['required'],
                'emp_id' => ['required']
            ],
            'formatters' => [
                'emp_id' => 'fetchEmployeeName',
                'job_id' => 'getJobDetails'
            ],
            'searchFields' => ['invoice_no', 'invoice_date', 'job_id'],
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
            'searchFields' => ['payment_date', 'payment_type', 'job_id'],
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
            'searchFields' => ['increment_type', 'increment_date', 'job_id'],
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
            'searchFields' => ['rate_type', 'effective_date'],
            'dateField' => 'effective_date'
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
            error_log("Columns for $table: " . implode(', ', $columns));
            return $columns;
        } catch (PDOException $e) {
            error_log("Error fetching columns for $table: " . $e->getMessage());
            throw new Exception("Failed to retrieve columns for $table: " . $e->getMessage());
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

    public function getPaginatedRecords($table, $page = 1, $perPage = 10, $searchTerm = '', $sortColumn = '', $sortOrder = 'DESC') {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }

        try {
            $allColumns = $this->getColumns($table);
            $offset = ($page - 1) * $perPage;
            $config = $this->getConfig($table);
            $idColumn = $allColumns[0]; // Primary key

            $sql = "SELECT " . implode(',', array_map(fn($col) => "`$col`", $allColumns)) . " FROM `$table`";
            $countSql = "SELECT COUNT(*) FROM `$table`";
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

            $totalStmt = $this->db->query("SELECT COUNT(*) FROM `$table`");
            $recordsTotal = $totalStmt->fetchColumn();

            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $recordsFiltered = $countStmt->fetchColumn();

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
            foreach ($params as $index => $param) {
                $stmt->bindValue($index + 1, $param);
            }
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($data as &$record) {
                foreach ($allColumns as $column) {
                    if (isset($config['formatters'][$column])) {
                        $record[$column] = $this->{$config['formatters'][$column]}($record[$column]);
                    }
                }
            }
            unset($record);

            error_log("Fetched " . count($data) . " records for $table, page $page, perPage $perPage");

            return [
                'draw' => isset($_POST['draw']) ? (int)$_POST['draw'] : 1,
                'recordsTotal' => (int)$recordsTotal,
                'recordsFiltered' => (int)$recordsFiltered,
                'data' => $data
            ];
        } catch (PDOException $e) {
            error_log("Error in getPaginatedRecords for $table: " . $e->getMessage());
            throw new Exception("Failed to retrieve records from $table: " . $e->getMessage());
        }
    }

    public function getInvoiceDetailsForJob($jobId) {
    if (empty($jobId)) return null;
    try {
        $stmt = $this->db->prepare("
            SELECT 
                invoice_no,
                invoice_date,
                invoice_value,
                invoice,
                receiving_payment,
                received_amount,
                payment_received_date,
                remarks,
                emp_id,
                job_id
            FROM invoice_data 
            WHERE job_id = ?
        ");
        $stmt->execute([$jobId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($invoice) {
            // Fetch employee name (or any other info) if necessary
            if (isset($invoice['emp_id'])) {
                $invoice['emp_id'] = $this->fetchEmployeeName($invoice['emp_id']);
            }
            // If you have job details as project-description-engineer
            if (isset($invoice['job_id'])) {
                $invoice['job_id'] = $this->getJobDetails($invoice['job_id']);
            }
            // Ensure the numbers are formatted properly
            if (isset($invoice['invoice_value'])) {
                $invoice['invoice_value'] = number_format((float)$invoice['invoice_value'], 2);
            }
            if (isset($invoice['received_amount'])) {
                $invoice['received_amount'] = number_format((float)$invoice['received_amount'], 2);
            }
        }
        return $invoice ?: null;
    } catch (PDOException $e) {
        error_log("Error in getInvoiceDetailsForJob: " . $e->getMessage());
        return null;
    }
}



    public function updateJobStatus($jobId) {
        try {
            $stmt = $this->db->prepare("SELECT completion FROM jobs WHERE job_id = ?");
            $stmt->execute([$jobId]);
            $currentCompletion = (float)$stmt->fetchColumn();
    
            if ($currentCompletion == 1.0) {
                return ['success' => true, 'completion' => 1.0];
            }
    
            if ($currentCompletion == 0.0) {
                $newCompletion = 0.2;
            } elseif ($currentCompletion == 0.2) {
                $newCompletion = 0.5;
            } elseif ($currentCompletion == 0.5) {
                $newCompletion = 1.0;
            } else {
                $newCompletion = 0.0;
            }
    
            $stmt = $this->db->prepare("UPDATE jobs SET completion = ? WHERE job_id = ?");
            $stmt->execute([$newCompletion, $jobId]);
    
            error_log("Updated job $jobId status from $currentCompletion to $newCompletion");
            return ['success' => true, 'completion' => $newCompletion];
        } catch (PDOException $e) {
            error_log("Error in updateJobStatus for job_id $jobId: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getCompletionStatus($value) {
        $value = (float)$value;
        if ($value == 0.00) {
            return '<span style="color: #EF4444;">Not Started</span>';
        } elseif ($value == 0.50) {
            return '<span style="color: #F59E0B;">Ongoing</span>';
        } elseif ($value == 1.00) {
            return '<span style="color: #10B981;">Completed</span>';
        }
        return htmlspecialchars($value);
    }

    public function create($table, $data) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }

        try {
            $config = $this->getConfig($table);
            $this->validate($table, $data, $config['validation'] ?? []);

            $editableFields = $config['editableFields'] ?? $this->getColumns($table);
            $filteredData = array_intersect_key($data, array_flip($editableFields));

            if (empty($filteredData)) {
                throw new Exception("No valid fields provided for insertion in $table");
            }

            $columns = array_keys($filteredData);
            $placeholders = array_fill(0, count($columns), '?');
            $sql = "INSERT INTO `$table` (" . implode(',', array_map(fn($col) => "`$col`", $columns)) . ") 
                    VALUES (" . implode(',', $placeholders) . ")";
            
            $stmt = $this->db->prepare($sql);
            $values = array_values($filteredData);
            $stmt->execute($values);

            $lastId = $this->db->lastInsertId();
            
            error_log("Successfully created record in $table with ID: $lastId");
            return $lastId;
        } catch (PDOException $e) {
            error_log("PDO Error in create for $table: " . $e->getMessage());
            throw new Exception("Failed to create record in $table: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("Validation Error in create for $table: " . $e->getMessage());
            throw $e; // Re-throw validation exceptions
        }
    }

    public function update($table, $data, $idColumn, $id) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }

        try {
            $config = $this->getConfig($table);
            $this->validate($table, $data, $config['validation'] ?? []);

            $editableFields = $config['editableFields'] ?? $this->getColumns($table);
            $filteredData = array_intersect_key($data, array_flip($editableFields));

            if (empty($filteredData)) {
                throw new Exception("No valid fields provided for update in $table");
            }

            $setClause = implode(',', array_map(fn($col) => "`$col` = ?", array_keys($filteredData)));
            $sql = "UPDATE `$table` SET $setClause WHERE `$idColumn` = ?";
            
            $stmt = $this->db->prepare($sql);
            $values = array_values($filteredData);
            $values[] = $id;
            
            $stmt->execute($values);
            $rowCount = $stmt->rowCount();

            error_log("Updated $rowCount record(s) in $table with $idColumn = $id");
            return $rowCount > 0;
        } catch (PDOException $e) {
            error_log("Error in update for $table: " . $e->getMessage());
            throw new Exception("Failed to update record in $table: " . $e->getMessage());
        }
    }

    public function delete($table, $idColumn, $id) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
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

            error_log("Deleted $rowCount record(s) from $table with $idColumn = $id");
            return $rowCount > 0;
        } catch (PDOException $e) {
            error_log("Error in delete for $table: " . $e->getMessage());
            throw new Exception("Failed to delete record from $table: " . $e->getMessage());
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
        return $value === 'Yes' || $value === true || $value === 1 ? '<i class="fas fa-check" style="color: #10B981;"></i>' : '<i class="fas fa-times" style="color: #EF4444;"></i>';
    }

    public function getPresenceDisplay($value) {
        $value = (float)$value;
        if ($value == 1.0) return '<span style="color: #10B981;">Full Day</span>';
        if ($value == 0.5) return '<span style="color: #F59E0B;">Half Day</span>';
        if ($value == 0.0) return '<span style="color: #EF4444;">Not Attended</span>';
        return htmlspecialchars($value);
    }

    public function getInvoiceByJobId($jobId) {
        if (empty($jobId)) return null;
        try {
            $stmt = $this->db->prepare("
                SELECT * 
                FROM invoice_data 
                WHERE job_id = ?
            ");
            $stmt->execute([$jobId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                if (isset($result['emp_id'])) {
                    $result['emp_id'] = $this->fetchEmployeeName($result['emp_id']);
                }
                if (isset($result['job_id'])) {
                    $result['job_id'] = $this->getJobDetails($result['job_id']);
                }
            }
            
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error in getInvoiceByJobId: " . $e->getMessage());
            return null;
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

    public function getProjectDetailsForJobs($projectId = null) {
    // If a project ID is provided, return a single project's details
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

            $companyRef  = !empty($result['company_reference']) 
                           ? htmlspecialchars($result['company_reference']) 
                           : 'No Company';

            $projectDesc = !empty($result['project_description']) 
                           ? htmlspecialchars($result['project_description']) 
                           : 'No Description';

            return "$companyRef - $projectDesc";

        } catch (PDOException $e) {
            error_log("Error in getProjectDetailsForJobs (details): " . $e->getMessage());
            return 'Error';
        }
    }

    // If no project ID is provided, return the <option> list
    try {
        $stmt = $this->db->query("
            SELECT project_id, company_reference, project_description 
            FROM projects 
            ORDER BY project_id DESC
        ");
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $options = ['<option value="">Select Project</option>'];

        foreach ($projects as $project) {
            $projectId    = htmlspecialchars($project['project_id']);
            $companyRef   = htmlspecialchars($project['company_reference']);
            $projectDesc  = htmlspecialchars($project['project_description']);

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
        return $result['data'];
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
            $dateField = $config['dateField'] ?? $this->getColumns($table)[0];
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

    public function formatCurrency($value) {
        return number_format((float)$value, 2);
    }
    
    public function getEmployeeOptions() {
    try {
        $stmt = $this->db->query("SELECT emp_id, emp_name FROM employees ORDER BY emp_id DESC");
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $options = ['<option value="">Select Employee</option>'];

        foreach ($employees as $employee) {
            $empId   = htmlspecialchars($employee['emp_id']);
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

    public function getJobDetails($jobId = null) {
    // If a job ID is provided, return the job's details in the specified format
    if (!empty($jobId)) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.project_description, 
                    p.company_reference, 
                    j.engineer
                FROM jobs j
                LEFT JOIN projects p ON j.project_id = p.project_id
                WHERE j.job_id = ?
            ");
            $stmt->execute([$jobId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) return 'Job Not Found';

            $projectDesc = $result['project_description'] ? htmlspecialchars($result['project_description']) : 'No Description';
            $companyRef  = $result['company_reference'] ? htmlspecialchars($result['company_reference']) : 'No Company';
            $engineer    = $result['engineer'] ? htmlspecialchars($result['engineer']) : 'No Engineer';

            return "$projectDesc - $companyRef - $engineer";

        } catch (PDOException $e) {
            error_log("Error in getJobDetails (details): " . $e->getMessage());
            return 'Error fetching job details';
        }
    }

    // If no job ID is provided, return the <option> list for jobs with project details
    try {
        $query = "
            SELECT 
                j.job_id, 
                j.engineer, 
                p.project_description, 
                p.company_reference
            FROM jobs j
            LEFT JOIN projects p ON j.project_id = p.project_id
            ORDER BY j.job_id DESC
        ";

        $result = $this->db->query($query);
        $options = ['<option value="">Select Job</option>'];

        while ($row = $result->fetch()) {
            $jobId           = htmlspecialchars($row['job_id']);
            $engineer        = htmlspecialchars($row['engineer']);
            $projectDesc     = htmlspecialchars($row['project_description']);
            $companyRef      = htmlspecialchars($row['company_reference']);

            $options[] = sprintf(
                '<option value="%s">(%s - %s - %s - %s)</option>',
                $jobId,
                $jobId,
                $projectDesc,
                $companyRef,
                $engineer
            );
        }

        return implode('', $options);

    } catch (PDOException $e) {
        error_log("Error in getJobDetails (options): " . $e->getMessage());
        return '<option value="">Error loading jobs</option>';
    }
}


    
// New method for project options
   public function getProjectOptionsCRUD() {
    // Initialize the default option
    $options = '<option value="">Select Project</option>';

    // Query projects ordered by project_id
    $query = "
        SELECT 
            project_id, 
            company_reference, 
            project_description 
        FROM projects 
        ORDER BY project_id DESC
    ";

    $result = $this->db->query($query);

    // Build option elements
    while ($row = $result->fetch()) {
        $projectId     = htmlspecialchars($row['project_id']);
        $companyRef    = htmlspecialchars($row['company_reference']);
        $projectDesc   = htmlspecialchars($row['project_description']);

        $options .= sprintf(
            '<option value="%s">(%s - %s - %s)</option>',
            $projectId,
            $projectId,
            $companyRef,
            $projectDesc
        );
    }

    return $options;
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