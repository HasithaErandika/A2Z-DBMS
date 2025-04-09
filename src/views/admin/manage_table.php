<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/'); // Adjust this to your app's actual base path
// $searchQuery removed as it's no longer needed with DataTables handling search via AJAX
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage <?php echo htmlspecialchars($data['table']); ?> - A2Z Engineering</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/src/assets/css/manage_table.css">
    <style>
        .search-bar { margin-bottom: 20px; }
        .search-input { width: 100%; padding: 8px; font-size: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-table"></i> Manage <?php echo htmlspecialchars($data['table']); ?></h1>
            <div class="header-actions">
                <button class="btn btn-primary tooltip" onclick="openModal('create')" data-tooltip="Add a new record"><i class="fas fa-plus"></i> Add Record</button>
                <button class="btn btn-secondary tooltip" onclick="window.location.href='<?php echo BASE_PATH; ?>/admin/tables'" data-tooltip="Return to tables"><i class="fas fa-arrow-left"></i> Back to Tables</button>
            </div>
        </div>

        <div class="filters">
            <div class="stats">
                <?php if ($data['table'] === 'jobs' && isset($data['totalCapacity'])): ?>
                    <span class="stats-box tooltip" data-tooltip="Total capacity across all jobs"><i class="fas fa-weight-hanging"></i> Total Capacity: <?php echo number_format($data['totalCapacity'], 2); ?></span>
                <?php endif; ?>
                <span class="stats-box tooltip" id="record-count" data-tooltip="Total records in the table"><i class="fas fa-database"></i> <?php echo $data['totalRecords']; ?> Records</span>
            </div>
            <form class="export-form" method="POST" action="<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>">
                <input type="hidden" name="action" value="export_csv">
                <input type="date" name="start_date" required aria-label="Start Date">
                <input type="date" name="end_date" required aria-label="End Date">
                <button type="submit" class="btn btn-primary tooltip" data-tooltip="Export data as CSV"><i class="fas fa-download"></i> Export CSV</button>
            </form>
        </div>

        <div class="search-bar">
            <!-- Removed form submission; search is now handled by DataTables -->
            <input class="search-input" id="searchInput" type="text" 
                   placeholder="Search table..." aria-label="Search table data">
        </div>

        <div class="table-container">
            <table id="data-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <?php foreach ($data['columns'] as $column): ?>
                            <th><?php echo htmlspecialchars($column); ?></th>
                        <?php endforeach; ?>
                        <?php if ($data['table'] === 'jobs'): ?>
                            <th>Status</th>
                        <?php endif; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Initial data loaded via AJAX -->
                </tbody>
            </table>
            <div class="spinner" id="loading-spinner"><i class="fas fa-spinner"></i></div>
        </div>
    </div>

    <!-- CRUD Modal -->
    <div class="modal" id="crud-modal">
        <div class="modal-content">
            <h2 id="modal-title" aria-live="polite"></h2>
            <form id="crud-form" method="POST" action="<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>">
                <input type="hidden" name="action" id="form-action">
                <input type="hidden" name="id" id="form-id">
                <?php 
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
                    'jobs' => 'job_id'
                ];
                $primaryKey = $primaryKeys[$data['table']] ?? $data['columns'][0];
                $dateColumns = ['date_started', 'date_completed', 'date', 'attendance_date', 'date_of_joined', 'date_of_resigned', 'date_of_birth', 'effective_date', 'end_date', 'expensed_date', 'invoice_date', 'payment_date', 'increment_date', 'txn_date'];
                $timeColumns = ['start_time', 'end_time'];
                foreach ($data['columns'] as $column): ?>
                    <div class="form-group">
                        <label for="<?php echo $column; ?>"><?php echo htmlspecialchars($column); ?></label>
                        <?php if ($column === $primaryKey): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" readonly aria-label="<?php echo htmlspecialchars($column); ?>">
                        <?php elseif (($column === 'emp_id' && $data['table'] !== 'employees') || ($data['table'] === 'cash_hand' && in_array($column, ['given_by', 'received_by']))): ?>
                            <select name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Employee">
                                <?php echo $data['tableManager']->getEmployeeOptions(); ?>
                            </select>
                        <?php elseif ($column === 'job_id' && $data['table'] !== 'jobs'): ?>
                            <select name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Job">
                                <?php echo $data['tableManager']->getJobDetails(); ?>
                            </select>
                        <?php elseif ($column === 'project_id' && $data['table'] !== 'projects'): ?>
                            <select name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Project">
                                <?php echo $data['tableManager']->getProjectDetailsForJobs(); ?>
                            </select>
                        <?php elseif ($column === 'project_id' && $data['table'] === 'jobs'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Project ID">
                        <?php elseif ($column === 'presence'): ?>
                            <select name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Presence Status">
                                <option value="1.0">Full Day</option>
                                <option value="0.5">Half Day</option>
                                <option value="0.0">Not Attended</option>
                            </select>
                        <?php elseif ($column === 'paid'): ?>
                            <select name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Payment Status">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        <?php elseif ($column === 'expenses_category' && $data['table'] === 'operational_expenses'): ?>
                            <select name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Expenses Category">
                                <option value="Meals">Meals</option>
                                <option value="Tools">Tools</option>
                                <option value="Fuel">Fuel</option>
                                <option value="Materials">Materials</option>
                                <option value="Hiring of labor">Hiring of labor</option>
                                <option value="Hiring of vehicle">Hiring of vehicle</option>
                                <option value="Mobile">Mobile</option>
                                <option value="Professional Charges">Professional Charges</option>
                                <option value="Clearance Charges">Clearance Charges</option>
                                <option value="Documentation">Documentation</option>
                                <option value="Other">Other</option>
                            </select>
                        <?php elseif (($column === 'rate_type' && $data['table'] === 'employee_payment_rates') || ($column === 'payment_type' && $data['table'] === 'employees')): ?>
                            <select name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                                <option value="Fixed">Fixed</option>
                                <option value="Daily">Daily</option>
                            </select>
                        <?php elseif ($column === 'payment_type' && $data['table'] === 'employee_payments'): ?>
                            <select name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Payment Type">
                                <option value="Monthly Salary">Monthly Salary</option>
                                <option value="Daily Wage">Daily Wage</option>
                                <option value="Advance">Advance</option>
                                <option value="Other">Other</option>
                            </select>
                        <?php elseif ($column === 'transaction_type' && $data['table'] === 'cash_hand'): ?>
                            <select name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Transaction Type">
                                <option value="In">In (Receiving from Accountant)</option>
                                <option value="Out">Out (Giving to Technician)</option>
                            </select>
                        <?php elseif ($column === 'increment_type' && $data['table'] === 'salary_increments'): ?>
                            <select name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Increment Type">
                                <option value="Promotion">Promotion</option>
                                <option value="Merit">Merit</option>
                                <option value="Annual">Annual</option>
                                <option value="Other">Other</option>
                            </select>
                        <?php elseif (in_array($column, $dateColumns)): ?>
                            <input type="date" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                        <?php elseif (in_array($column, $timeColumns)): ?>
                            <input type="time" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                        <?php elseif ($column === 'completion'): ?>
                            <input type="number" step="0.01" name="<?php echo $column; ?>" id="<?php echo $column; ?>" readonly aria-label="<?php echo htmlspecialchars($column); ?>">
                        <?php else: ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="form-group" style="display: flex; justify-content: flex-end; gap: 15px;">
                    <button type="button" class="btn btn-secondary tooltip" onclick="closeModal()" data-tooltip="Cancel changes"><i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" class="btn btn-primary tooltip" data-tooltip="Save changes"><i class="fas fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Invoice Modal -->
    <div class="modal" id="invoice-modal">
        <div class="modal-content">
            <h2><i class="fas fa-file-invoice"></i> Invoice Details</h2>
            <div id="invoice-details" class="invoice-details">
                <div class="invoice-grid">
                    <div class="invoice-item"><span class="label">Invoice Number:</span><span id="invoice-no">-</span></div>
                    <div class="invoice-item"><span class="label">Invoice Date:</span><span id="invoice-date">-</span></div>
                    <div class="invoice-item"><span class="label">Invoice Value:</span><span id="invoice-value">-</span></div>
                    <div class="invoice-item"><span class="label">Job Details:</span><span id="invoice-job">-</span></div>
                    <div class="invoice-item"><span class="label">Receiving Payment:</span><span id="invoice-receiving">-</span></div>
                    <div class="invoice-item"><span class="label">Received Amount:</span><span id="invoice-received">-</span></div>
                    <div class="invoice-item"><span class="label">Payment Received Date:</span><span id="invoice-payment-date">-</span></div>
                    <div class="invoice-item full-width"><span class="label">Remarks:</span><span id="invoice-remarks">-</span></div>
                </div>
                <div class="invoice-actions" style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 15px;">
                    <button class="btn btn-secondary tooltip" onclick="closeInvoiceModal()" data-tooltip="Close invoice view"><i class="fas fa-times"></i> Close</button>
                </div>
            </div>
            <div class="spinner" id="invoice-spinner"><i class="fas fa-spinner"></i></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        window.appConfig = {
            basePath: '<?php echo BASE_PATH; ?>',
            tableName: '<?php echo htmlspecialchars($data['table']); ?>',
            columns: <?php echo json_encode($data['columns']); ?>,
            primaryKey: '<?php echo $primaryKey; ?>',
            completionIndex: <?php echo json_encode(array_search('completion', $data['columns']) !== false ? array_search('completion', $data['columns']) : -1); ?>
        };
    </script>
    <script src="<?php echo BASE_PATH; ?>/src/assets/js/manage_table.js"></script>
</body>
</html>