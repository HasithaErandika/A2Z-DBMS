<?php
// src/controllers/AdminController.php

require_once 'src/core/Controller.php';

use Database;

class AdminController extends Controller {
    private $tableManager;

    public function __construct() {
        $this->tableManager = new TableManager();
    }

    public function manageTable($table, $request = null, $response = null) {
        if (!$request) $request = new App\Helpers\Request();
        if (!$response) $response = new App\Helpers\Response();

        try {
            $db = Database::getInstance($_SESSION['db_username'], $_SESSION['db_password']);
        } catch (Exception $e) {
            error_log("DB connection failed in manageTable: " . $e->getMessage());
            $response->redirect(BASE_PATH . "/login");
            exit;
        }

        if (!in_array($table, $this->tableManager->getAllowedTables())) {
            $response->redirect(BASE_PATH . "/admin");
            exit;
        }

        if ($request->isPost()) {
            $action = $request->post('action') ?? '';
            $columns = $this->tableManager->getColumns($table);
            $idColumn = $this->tableManager->getPrimaryKey($table);

            if ($action === 'set_maintenance_status') {
                try {
                    $jobId = $request->post('job_id') ?? '';
                    $cycle = intval($request->post('cycle_number') ?? 0);
                    $scheduledDate = $request->post('scheduled_date') ?? null;
                    $status = $request->post('status') ?? 'scheduled';
                    $description = $request->post('description') ?? null;

                    if (empty($jobId) || $cycle <= 0) {
                        throw new Exception('Job ID and cycle number are required');
                    }

                    $pdo = $db->getConnection();
                    $check = $pdo->prepare("SELECT schedule_id FROM maintenance_schedule WHERE job_id = ? AND cycle_number = ? LIMIT 1");
                    $check->execute([$jobId, $cycle]);
                    $existing = $check->fetch(PDO::FETCH_ASSOC);

                    if ($existing) {
                        $updateFields = [];
                        $params = [];
                        if ($scheduledDate !== null) { $updateFields[] = 'scheduled_date = ?'; $params[] = $scheduledDate; }
                        if ($status !== null) { $updateFields[] = 'status = ?'; $params[] = $status; }
                        if ($description !== null) { $updateFields[] = 'description = ?'; $params[] = $description; }
                        if ($status === 'completed') { $updateFields[] = 'actual_date = ?'; $params[] = date('Y-m-d'); }
                        
                        if (!empty($updateFields)) {
                            $params[] = $existing['schedule_id'];
                            $sql = "UPDATE maintenance_schedule SET " . implode(', ', $updateFields) . " WHERE schedule_id = ?";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute($params);
                        }
                        $response->json(['success' => true, 'action' => 'updated']);
                    } else {
                        $actualDate = $status === 'completed' ? date('Y-m-d') : null;
                        $stmt = $pdo->prepare("INSERT INTO maintenance_schedule (job_id, cycle_number, scheduled_date, actual_date, status, description) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$jobId, $cycle, $scheduledDate, $actualDate, $status, $description]);
                        $response->json(['success' => true, 'action' => 'inserted', 'id' => $pdo->lastInsertId()]);
                    }
                } catch (Exception $e) {
                    error_log('Error in set_maintenance_status: ' . $e->getMessage());
                    $response->json(['success' => false, 'error' => $e->getMessage()]);
                }
            }

            if ($action === 'get_records') {
                $searchPost = $request->post('search');
                $searchTerms = isset($searchPost['terms']) && is_array($searchPost['terms']) ? array_map('trim', $searchPost['terms']) : [];
                $sortColumn = $request->post('sortColumn') ?? '';
                $sortOrder = strtoupper($request->post('sortOrder') ?? 'DESC');
                
                $perPage = $request->post('length') !== null ? (int)$request->post('length') : 10;
                $start = $request->post('start') !== null ? (int)$request->post('start') : 0;
                $page = ($perPage > 0) ? floor($start / $perPage) + 1 : 1;

                try {
                    $result = $this->tableManager->fetchRecords($table, $page, $perPage, $searchTerms, $sortColumn, $sortOrder, true, false);
                    $response->json($result);
                } catch (Exception $e) {
                    $response->json(['error' => $e->getMessage()]);
                }
            } elseif ($action === 'generate_maintenance') {
                try {
                    $result = $this->tableManager->generateMaintenanceSchedulesFromJobs();
                    $response->json($result);
                } catch (Exception $e) {
                    $response->json(['success' => false, 'error' => $e->getMessage()]);
                }
            } elseif ($action === 'generate_maintenance_for_job') {
                try {
                    $jobId = $request->post('job_id') ?? '';
                    $result = $this->tableManager->generateMaintenanceSchedulesForJob($jobId);
                    $response->json($result);
                } catch (Exception $e) {
                    $response->json(['success' => false, 'error' => $e->getMessage()]);
                }
            } elseif ($action === 'create') {
                $data = [];
                foreach ($columns as $column) {
                    if ($column !== $idColumn) {
                        $data[$column] = $request->post($column) ?? '';
                    }
                }
                error_log("Create data for $table: " . json_encode($data));
                try {
                    $newId = $this->tableManager->create($table, $data);
                    $response->json(['success' => true, 'message' => 'Record created successfully!', 'id' => $newId]);
                } catch (Exception $e) {
                    $error = "Error creating record: " . $e->getMessage();
                    error_log($error);
                    $response->json(['success' => false, 'error' => $error]);
                }
            } elseif ($action === 'update') {
                $id = $request->post('id') ?? '';
                $data = [];
                foreach ($columns as $column) {
                    $data[$column] = $request->post($column) ?? '';
                }
                try {
                    $this->tableManager->update($table, $data, $idColumn, $id);
                    $response->json(['success' => true, 'message' => 'Record updated successfully!']);
                } catch (Exception $e) {
                    $error = "Error updating record: " . $e->getMessage();
                    error_log($error);
                    $response->json(['success' => false, 'error' => $error]);
                }
            } elseif ($action === 'delete') {
                $id = $request->post('id') ?? '';
                if (empty($id)) {
                    $response->json(['success' => false, 'error' => 'Record ID is required']);
                }
                error_log("Attempting to delete record from $table with $idColumn = $id");
                try {
                    $this->tableManager->delete($table, $idColumn, $id);
                    $response->json(['success' => true, 'message' => 'Record deleted successfully!']);
                } catch (Exception $e) {
                    $error = "Error deleting record: " . $e->getMessage();
                    error_log($error);
                    $response->json(['success' => false, 'error' => $error]);
                }
            } elseif ($action === 'export_csv') {
                $this->tableManager->exportRecordsToCSV($table, $request->post('start_date'), $request->post('end_date'));
                exit;
            } elseif ($action === 'mark_as_paid' && $table === 'operational_expenses') {
                try {
                    $id = $request->post('id') ?? '';
                    if (empty($id)) throw new Exception("ID is required");
                    
                    $this->tableManager->update('operational_expenses', ['paid' => 1], 'expense_id', $id);
                    $response->json(['success' => true, 'message' => 'Expense marked as paid']);
                } catch (Exception $e) {
                    $response->json(['success' => false, 'error' => $e->getMessage()]);
                }
            } elseif ($action === 'update_status' && $table === 'jobs') {
                try {
                    $jobId = $request->post('job_id') ?? '';
                    $newCompletion = $request->post('completion') ?? null;
                    $result = $this->tableManager->updateJobStatus($jobId, $newCompletion);
                    $response->json($result);
                } catch (Exception $e) {
                    $response->json(['success' => false, 'error' => $e->getMessage()]);
                }
            } elseif ($action === 'get_job_completion' && $table === 'jobs') {
                try {
                    $jobId = $request->post('job_id') ?? '';
                    $completion = $this->tableManager->getJobCompletion($jobId);
                    $response->json(['completion' => $completion]);
                } catch (Exception $e) {
                    $response->json(['error' => $e->getMessage()]);
                }
            } elseif ($action === 'get_invoice_details' && $table === 'jobs') {
                try {
                    $jobId = $request->post('job_id') ?? '';
                    if (empty($jobId)) {
                        throw new Exception("Job ID is required");
                    }
                    $invoiceData = $this->tableManager->getInvoiceDetailsByJobId($jobId);
                    $response->json($invoiceData ?? []);
                } catch (Exception $e) {
                    $response->json(['error' => $e->getMessage()]);
                }
            }
        }

        // Fetch records for initial page load
        $searchVal = $request->get('search');
        $searchTerms = $searchVal ? array_map('trim', explode(' ', $searchVal)) : [];
        $result = $this->tableManager->fetchRecords($table, 1, 0, $searchTerms, '', 'DESC', false, false);

        $data = [
            'table' => $table,
            'columns' => $this->tableManager->getColumns($table),
            'records' => $result['data'],
            'totalRecords' => $result['recordsTotal'],
            'config' => $this->tableManager->getConfig($table),
            'username' => $_SESSION['db_username'] ?? 'Admin',
            'dbname' => Database::getDatabaseName(),
            'tableManager' => $this->tableManager,
            'message' => $request->get('message') ?? '',
            'error' => $request->get('error') ?? ''
        ];

        if ($table === 'jobs') {
            $data['totalCapacity'] = $this->tableManager->calculateTotalJobCapacity();
        }
        
        if ($table === 'operational_expenses') {
            $data['monthlyExpenses'] = $this->tableManager->getMonthlyExpenses();
        }

        $this->render('admin/manage_table', $data);
    }
}