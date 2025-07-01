<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/'); // Adjust this to your app's actual base path
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage <?php echo htmlspecialchars($data['table']); ?> - A2Z Engineering</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>src/assets/css/manage_table.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-table" aria-hidden="true"></i> Manage <?php echo htmlspecialchars($data['table']); ?></h1>
            <div class="header-actions">
                <button class="btn btn-primary tooltip" onclick="openModal('create')" aria-label="Add a new record" data-tooltip="Add a new record"><i class="fas fa-plus" aria-hidden="true"></i> Add Record</button>
                <button class="btn btn-secondary tooltip" onclick="window.location.href='<?php echo BASE_PATH; ?>admin/tables'" aria-label="Return to tables" data-tooltip="Return to tables"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back</button>
            </div>
        </div>

        <div class="filters">
            <div class="stats">
                <?php if ($data['table'] === 'jobs' && isset($data['totalCapacity'])): ?>
                    <span class="stat-box tooltip" data-tooltip="Total capacity across all jobs"><i class="fas fa-weight-hanging" aria-hidden="true"></i> Total Capacity: <?php echo number_format($data['totalCapacity'], 2); ?></span>
                <?php endif; ?>
                <span class="stat-box tooltip" id="record-count" data-tooltip="Total and filtered records in the table"><i class="fas fa-database" aria-hidden="true"></i> <?php echo $data['totalRecords']; ?> Records</span>
            </div>
            <form class="export-form" method="POST" action="<?php echo BASE_PATH; ?>admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>">
                <input type="hidden" name="action" value="export_csv">
                <div class="date-inputs">
                    <input type="date" name="start_date" required aria-label="Start Date" placeholder="YYYY-MM-DD">
                    <input type="date" name="end_date" required aria-label="End Date" placeholder="YYYY-MM-DD">
                </div>
                <button type="submit" class="btn btn-primary tooltip" aria-label="Export data as CSV" data-tooltip="Export data as CSV"><i class="fas fa-download" aria-hidden="true"></i> Export</button>
            </form>
        </div>

        <div class="search-bar">
            <input class="search-input" id="searchInput" type="text" placeholder="Search all fields..." aria-label="Search all table data">
        </div>

        <div class="table-wrapper">
            <table id="data-table" class="display">
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
                <tbody></tbody>
            </table>
            <div class="spinner" id="loading-spinner"><i class="fas fa-spinner fa-spin" aria-hidden="true"></i></div>
        </div>
    </div>

    <!-- CRUD Modal -->
    <div class="modal" id="crud-modal" role="dialog" aria-labelledby="modal-title">
        <div class="modal-content">
            <h2 id="modal-title" aria-live="polite"></h2>
            <form id="crud-form" method="POST" action="<?php echo BASE_PATH; ?>admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>">
                <input type="hidden" name="action" id="form-action">
                <input type="hidden" name="id" id="form-id">
                <?php 
                $primaryKeys = [
                    'employees' => 'emp_id', 'employee_payment_rates' => 'rate_id', 'attendance' => 'attendance_id',
                    'salary_increments' => 'increment_id', 'employee_payments' => 'payment_id', 'invoice_data' => 'invoice_id',
                    'operational_expenses' => 'expense_id', 'projects' => 'project_id', 'employee_bank_details' => 'id',
                    'jobs' => 'job_id', 'cash_hand' => 'cash_id'
                ];
                $primaryKey = $primaryKeys[$data['table']] ?? $data['columns'][0];
                $dateColumns = ['date_started', 'date_completed', 'date', 'attendance_date', 'date_of_joined', 'date_of_resigned', 'date_of_birth', 'effective_date', 'end_date', 'expensed_date', 'invoice_date', 'payment_date', 'increment_date', 'txn_date', 'payment_received_date'];
                $timeColumns = ['start_time', 'end_time'];
                foreach ($data['columns'] as $column): ?>
                    <div class="form-group">
                        <label for="<?php echo $column; ?>"><?php echo htmlspecialchars($column); ?></label>
                        <?php if ($column === $primaryKey): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" readonly aria-label="<?php echo htmlspecialchars($column); ?>" class="primary-key-field">
                        <?php elseif (($column === 'emp_id' && $data['table'] !== 'employees') || ($data['table'] === 'cash_hand' && in_array($column, ['given_by', 'received_by']))): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <select class="nice-dropdown" onchange="document.getElementById('<?php echo $column; ?>').value = this.value" aria-label="Select Employee for <?php echo htmlspecialchars($column); ?>">
                                <?php echo $data['tableManager']->getEmployeeOptions(); ?>
                            </select>
                        <?php elseif ($column === 'job_id' && $data['table'] !== 'jobs'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <select class="nice-dropdown" onchange="document.getElementById('<?php echo $column; ?>').value = this.value" aria-label="Select Job">
                                <?php echo $data['tableManager']->getJobDetails(); ?>
                            </select>
                        <?php elseif ($column === 'project_id' && $data['table'] !== 'projects'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <select class="nice-dropdown" onchange="document.getElementById('<?php echo $column; ?>').value = this.value" aria-label="Select Project">
                                <?php echo $data['tableManager']->getProjectDetailsForJobs(); ?>
                            </select>
                        <?php elseif ($column === 'project_id' && $data['table'] === 'jobs'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Project ID">
                        <?php elseif ($column === 'expenses_category' && $data['table'] === 'operational_expenses'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <select class="nice-dropdown" onchange="document.getElementById('<?php echo $column; ?>').value = this.value" aria-label="Select Expenses Category">
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
                        <?php elseif ($column === 'presence'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <div class="button-group" role="group" aria-label="Presence options">
                                <button type="button" class="btn-option btn-green" data-value="1.0" onclick="selectOption('<?php echo $column; ?>', '1.0')" aria-label="Full Day">Full Day</button>
                                <button type="button" class="btn-option btn-yellow" data-value="0.5" onclick="selectOption('<?php echo $column; ?>', '0.5')" aria-label="Half Day">Half Day</button>
                                <button type="button" class="btn-option btn-red" data-value="0.0" onclick="selectOption('<?php echo $column; ?>', '0.0')" aria-label="Not Attended">Not Attended</button>
                            </div>
                        <?php elseif ($column === 'paid'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <div class="button-group" role="group" aria-label="Paid options">
                                <button type="button" class="btn-option btn-green" data-value="Yes" onclick="selectOption('<?php echo $column; ?>', 'Yes')" aria-label="Paid">Yes</button>
                                <button type="button" class="btn-option btn-red" data-value="No" onclick="selectOption('<?php echo $column; ?>', 'No')" aria-label="Not Paid">No</button>
                            </div>
                        <?php elseif (($column === 'rate_type' && $data['table'] === 'employee_payment_rates') || ($column === 'payment_type' && $data['table'] === 'employees')): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <div class="button-group" role="group" aria-label="Rate type options">
                                <button type="button" class="btn-option btn-blue" data-value="Fixed" onclick="selectOption('<?php echo $column; ?>', 'Fixed')" aria-label="Fixed Rate">Fixed</button>
                                <button type="button" class="btn-option btn-orange" data-value="Daily" onclick="selectOption('<?php echo $column; ?>', 'Daily')" aria-label="Daily Rate">Daily</button>
                            </div>
                        <?php elseif ($column === 'payment_type' && $data['table'] === 'employee_payments'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <div class="button-group" role="group" aria-label="Payment type options">
                                <button type="button" class="btn-option btn-green" data-value="Monthly Salary" onclick="selectOption('<?php echo $column; ?>', 'Monthly Salary')" aria-label="Monthly Salary">Monthly Salary</button>
                                <button type="button" class="btn-option btn-yellow" data-value="Daily Wage" onclick="selectOption('<?php echo $column; ?>', 'Daily Wage')" aria-label="Daily Wage">Daily Wage</button>
                                <button type="button" class="btn-option btn-orange" data-value="Advance" onclick="selectOption('<?php echo $column; ?>', 'Advance')" aria-label="Advance">Advance</button>
                                <button type="button" class="btn-option btn-purple" data-value="Other" onclick="selectOption('<?php echo $column; ?>', 'Other')" aria-label="Other">Other</button>
                            </div>
                        <?php elseif ($column === 'transaction_type' && $data['table'] === 'cash_hand'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <div class="button-group" role="group" aria-label="Transaction type options">
                                <button type="button" class="btn-option btn-green" data-value="In" onclick="selectOption('<?php echo $column; ?>', 'In')" aria-label="Transaction In">In</button>
                                <button type="button" class="btn-option btn-red" data-value="Out" onclick="selectOption('<?php echo $column; ?>', 'Out')" aria-label="Transaction Out">Out</button>
                            </div>
                        <?php elseif ($column === 'increment_type' && $data['table'] === 'salary_increments'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <div class="button-group" role="group" aria-label="Increment type options">
                                <button type="button{Phenomenon} class="btn-option btn-blue" data-value="Promotion" onclick="selectOption('<?php echo $column; ?>', 'Promotion')" aria-label="Promotion">Promotion</button>
                                <button type="button" class="btn-option btn-green" data-value="Merit" onclick="selectOption('<?php echo $column; ?>', 'Merit')" aria-label="Merit">Merit</button>
                                <button type="button" class="btn-option btn-yellow" data-value="Annual" onclick="selectOption('<?php echo $column; ?>', 'Annual')" aria-label="Annual">Annual</button>
                                <button type="button" class="btn-option btn-purple" data-value="Other" onclick="selectOption('<?php echo $column; ?>', 'Other')" aria-label="Other">Other</button>
                            </div>
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
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary tooltip" onclick="closeModal()" aria-label="Cancel changes" data-tooltip="Cancel changes"><i class="fas fa-times" aria-hidden="true"></i> Cancel</button>
                    <button type="button" class="btn btn-primary tooltip" onclick="openConfirmModal('update')" aria-label="Save changes" data-tooltip="Save changes"><i class="fas fa-save" aria-hidden="true"></i> Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Invoice Modal -->
    <div class="modal" id="invoice-modal" role="dialog" aria-labelledby="invoice-title">
        <div class="modal-content">
            <h2 id="invoice-title"><i class="fas fa-file-invoice" aria-hidden="true"></i> Invoice Details</h2>
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
                <div class="form-actions">
                    <button class="btn btn-secondary tooltip" onclick="closeInvoiceModal()" aria-label="Close invoice view" data-tooltip="Close invoice view"><i class="fas fa-times" aria-hidden="true"></i> Close</button>
                </div>
            </div>
            <div class="spinner" id="invoice-spinner"><i class="fas fa-spinner fa-spin" aria-hidden="true"></i></div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal" id="confirm-modal" role="dialog" aria-labelledby="confirm-title">
        <div class="modal-content">
            <h2 id="confirm-title" aria-live="polite"></h2>
            <p id="confirm-message"></p>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary tooltip" onclick="closeConfirmModal()" aria-label="Cancel action" data-tooltip="Cancel action"><i class="fas fa-times" aria-hidden="true"></i> Cancel</button>
                <button type="button" class="btn btn-primary tooltip" id="confirm-action-btn" aria-label="Confirm action" data-tooltip="Confirm action"><i class="fas fa-check" aria-hidden="true"></i> Confirm</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="<?php echo BASE_PATH; ?>src/assets/js/manage_table.js"></script>
    <script>
        // Pass PHP data to JavaScript
        window.appConfig = {
            basePath: '<?php echo BASE_PATH; ?>',
            tableName: '<?php echo htmlspecialchars($data['table']); ?>',
            columns: <?php echo json_encode($data['columns']); ?>,
            primaryKey: '<?php echo htmlspecialchars($primaryKey); ?>',
            dateColumns: <?php echo json_encode($dateColumns); ?>
        };
    </script>
</body>
</html>
