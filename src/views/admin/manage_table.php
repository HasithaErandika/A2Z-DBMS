<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/'); // Adjust this to your app's actual base path
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage <?php echo htmlspecialchars($data['table']); ?> - A2Z Engineering</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/src/assets/css/manage_table.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-table"></i> Manage <?php echo htmlspecialchars($data['table']); ?></h1>
            <div class="header-actions">
                <button class="btn btn-primary tooltip" onclick="openModal('create')" data-tooltip="Add a new record"><i class="fas fa-plus"></i> Add Record</button>
                <button class="btn btn-secondary tooltip" onclick="window.location.href='<?php echo BASE_PATH; ?>/admin/tables'" data-tooltip="Return to tables"><i class="fas fa-arrow-left"></i> Back</button>
            </div>
        </div>

        <div class="filters">
            <div class="stats">
                <?php if ($data['table'] === 'jobs' && isset($data['totalCapacity'])): ?>
                    <span class="stat-box tooltip" data-tooltip="Total capacity across all jobs"><i class="fas fa-weight-hanging"></i> Total Capacity: <?php echo number_format($data['totalCapacity'], 2); ?></span>
                <?php endif; ?>
                <span class="stat-box tooltip" id="record-count" data-tooltip="Total and filtered records in the table"><i class="fas fa-database"></i> <?php echo $data['totalRecords']; ?> Records</span>
            </div>
            <form class="export-form" method="POST" action="<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>">
                <input type="hidden" name="action" value="export_csv">
                <div class="date-inputs">
                    <input type="date" name="start_date" required aria-label="Start Date" placeholder="YYYY-MM-DD">
                    <input type="date" name="end_date" required aria-label="End Date" placeholder="YYYY-MM-DD">
                </div>
                <button type="submit" class="btn btn-primary tooltip" data-tooltip="Export data as CSV"><i class="fas fa-download"></i> Export</button>
            </form>
        </div>

        <div class="search-bar">
            <input class="search-input" id="searchInput" type="text" placeholder="Search table (e.g., text or YYYY-MM-DD)" aria-label="Search table data">
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
            <div class="spinner" id="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>
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
                            <select class="nice-dropdown" onchange="document.getElementById('<?php echo $column; ?>').value = this.value" aria-label="Select Employee">
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
                            <select class="nice-dropdown" onchange="document.getElementById('<?php echo $column; ?>').value = this.value" aria-label="Expenses Category">
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
                            <div class="button-group">
                                <button type="button" class="btn-option btn-green" data-value="1.0" onclick="selectOption('<?php echo $column; ?>', '1.HLF Day</button>
                                <button type="button" class="btn-option btn-yellow" data-value="0.5" onclick="selectOption('<?php echo $column; ?>', '0.5')">Half Day</button>
                                <button type="button" class="btn-option btn-red" data-value="0.0" onclick="selectOption('<?php echo $column; ?>', '0.0')">Not Attended</button>
                            </div>
                        <?php elseif ($column === 'paid'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <div class="button-group">
                                <button type="button" class="btn-option btn-green" data-value="Yes" onclick="selectOption('<?php echo $column; ?>', 'Yes')">Yes</button>
                                <button type="button" class="btn-option btn-red" data-value="No" onclick="selectOption('<?php echo $column; ?>', 'No')">No</button>
                            </div>
                        <?php elseif (($column === 'rate_type' && $data['table'] === 'employee_payment_rates') || ($column === 'payment_type' && $data['table'] === 'employees')): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <div class="button-group">
                                <button type="button" class="btn-option btn-blue" data-value="Fixed" onclick="selectOption('<?php echo $column; ?>', 'Fixed')">Fixed</button>
                                <button type="button" class="btn-option btn-orange" data-value="Daily" onclick="selectOption('<?php echo $column; ?>', 'Daily')">Daily</button>
                            </div>
                        <?php elseif ($column === 'payment_type' && $data['table'] === 'employee_payments'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <div class="button-group">
                                <button type="button" class="btn-option btn-green" data-value="Monthly Salary" onclick="selectOption('<?php echo $column; ?>', 'Monthly Salary')">Monthly Salary</button>
                                <button type="button" class="btn-option btn-yellow" data-value="Daily Wage" onclick="selectOption('<?php echo $column; ?>', 'Daily Wage')">Daily Wage</button>
                                <button type="button" class="btn-option btn-orange" data-value="Advance" onclick="selectOption('<?php echo $column; ?>', 'Advance')">Advance</button>
                                <button type="button" class="btn-option btn-purple" data-value="Other" onclick="selectOption('<?php echo $column; ?>', 'Other')">Other</button>
                            </div>
                        <?php elseif ($column === 'transaction_type' && $data['table'] === 'cash_hand'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <div class="button-group">
                                <button type="button" class="btn-option btn-green" data-value="In" onclick="selectOption('<?php echo $column; ?>', 'In')">In</button>
                                <button type="button" class="btn-option btn-red" data-value="Out" onclick="selectOption('<?php echo $column; ?>', 'Out')">Out</button>
                            </div>
                        <?php elseif ($column === 'increment_type' && $data['table'] === 'salary_increments'): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <div class="button-group">
                                <button type="button" class="btn-option btn-blue" data-value="Promotion" onclick="selectOption('<?php echo $column; ?>', 'Promotion')">Promotion</button>
                                <button type="button" class="btn-option btn-green" data-value="Merit" onclick="selectOption('<?php echo $column; ?>', 'Merit')">Merit</button>
                                <button type="button" class="btn-option btn-yellow" data-value="Annual" onclick="selectOption('<?php echo $column; ?>', 'Annual')">Annual</button>
                                <button type="button" class="btn-option btn-purple" data-value="Other" onclick="selectOption('<?php echo $column; ?>', 'Other')">Other</button>
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
                    <button type="button" class="btn btn-secondary tooltip" onclick="closeModal()" data-tooltip="Cancel changes"><i class="fas fa-times"></i> Cancel</button>
                    <button type="button" class="btn btn-primary tooltip" onclick="openConfirmModal('update')" data-tooltip="Save changes"><i class="fas fa-save"></i> Save</button>
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
                <div class="form-actions">
                    <button class="btn btn-secondary tooltip" onclick="closeInvoiceModal()" data-tooltip="Close invoice view"><i class="fas fa-times"></i> Close</button>
                </div>
            </div>
            <div class="spinner" id="invoice-spinner"><i class="fas fa-spinner fa-spin"></i></div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal" id="confirm-modal">
        <div class="modal-content">
            <h2 id="confirm-title" aria-live="polite"></h2>
            <p id="confirm-message"></p>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary tooltip" onclick="closeConfirmModal()" data-tooltip="Cancel action"><i class="fas fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-primary tooltip" id="confirm-action-btn" data-tooltip="Confirm action"><i class="fas fa-check"></i> Confirm</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var columns = <?php echo json_encode($data['columns']); ?>;
            var dateColumns = <?php echo json_encode($dateColumns); ?>;
            var table;

            // Initialize spinner and hide table
            $('#loading-spinner').show();
            $('#data-table').hide();

            // Ensure modals are hidden on page load
            $('#crud-modal').hide();
            $('#invoice-modal').hide();
            $('#confirm-modal').hide();

            // Define column definitions for DataTables
            var columnDefs = [
                <?php foreach ($data['columns'] as $column): ?>
                    { 
                        data: "<?php echo htmlspecialchars($column); ?>",
                        title: "<?php echo htmlspecialchars($column); ?>",
                        name: "<?php echo htmlspecialchars($column); ?>"
                    },
                <?php endforeach; ?>
                <?php if ($data['table'] === 'jobs'): ?>
                    {
                        data: "completion",
                        title: "Status",
                        name: "completion",
                        render: function(data, type, row) {
                            return '<select class="status-select" data-id="' + row.<?php echo htmlspecialchars($primaryKey); ?> + '">' +
                                '<option value="0.0" ' + (data == '0.0' ? 'selected' : '') + '>Not Started</option>' +
                                '<option value="0.1" ' + (data == '0.1' ? 'selected' : '') + '>Cancelled</option>' +
                                '<option value="0.2" ' + (data == '0.2' ? 'selected' : '') + '>Started</option>' +
                                '<option value="0.5" ' + (data == '0.5' ? 'selected' : '') + '>Ongoing</option>' +
                                '<option value="1.0" ' + (data == '1.0' ? 'selected' : '') + '>Completed</option>' +
                                '</select>';
                        }
                    },
                <?php endif; ?>
                {
                    data: null,
                    title: "Actions",
                    name: "actions",
                    render: function(data, type, row) {
                        var buttons = '<button class="btn btn-primary btn-sm edit-btn" data-id="' + row.<?php echo htmlspecialchars($primaryKey); ?> + '"><i class="fas fa-edit"></i></button>' +
                                      '<button class="btn btn-danger btn-sm delete-btn" data-id="' + row.<?php echo htmlspecialchars($primaryKey); ?> + '"><i class="fas fa-trash"></i></button>';
                        <?php if ($data['table'] === 'jobs'): ?>
                            if (row.has_invoice) {
                                buttons += '<button class="btn btn-info btn-sm invoice-btn" data-id="' + row.<?php echo htmlspecialchars($primaryKey); ?> + '"><i class="fas fa-file-invoice"></i></button>';
                            }
                        <?php endif; ?>
                        return buttons;
                    }
                }
            ];

            table = $('#data-table').DataTable({
                paging: true,
                pageLength: 10,
                lengthChange: true,
                lengthMenu: [10, 25, 50, 100],
                processing: true,
                serverSide: true,
                searching: false,
                scrollX: true,
                autoWidth: false,
                order: [[0, 'desc']],
                ajax: {
                    url: "<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>",
                    type: "POST",
                    data: function(d) {
                        var searchValue = $('#searchInput').val();
                        var isDate = /^\d{4}-\d{2}-\d{2}$/.test(searchValue);
                        d.action = 'get_records';
                        d.search = {
                            value: searchValue,
                            isDate: isDate
                        };
                        d.sortColumn = d.columns[d.order[0].column].name || d.columns[d.order[0].column].data;
                        d.sortOrder = d.order[0].dir;
                        d.page = Math.floor(d.start / d.length) + 1;
                        d.perPage = d.length;
                    },
                    beforeSend: function() {
                        $('#loading-spinner').show();
                        $('#data-table').hide();
                    },
                    complete: function() {
                        $('#loading-spinner').hide();
                        $('#data-table').show();
                        updateRecordCount();
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTable AJAX error:', error, thrown);
                        $('#loading-spinner').hide();
                        $('#data-table').show();
                        $('#record-count').text('Error loading data');
                    }
                },
                columns: columnDefs,
                drawCallback: function() {
                    table.columns.adjust();
                    updateRecordCount();
                }
            });

            // Function to update record count display
            function updateRecordCount() {
                var info = table.ajax.json() || {};
                var totalRecords = info.recordsTotal || 0;
                var filteredRecords = info.recordsFiltered !== undefined ? info.recordsFiltered : totalRecords;
                var searchValue = $('#searchInput').val();
                var displayText = totalRecords + ' Records';
                if (searchValue && filteredRecords !== totalRecords) {
                    displayText += ' (' + filteredRecords + ' Filtered)';
                }
                $('#record-count').text(displayText);
            }

            // Custom search bar functionality
            $('#searchInput').on('keyup', function() {
                var searchValue = $(this).val();
                if (!/^\d{4}-\d{2}-\d{2}$/.test(searchValue)) {
                    if (/^\d{2}[-\\/]\d{2}[-\\/]\d{4}$/.test(searchValue)) {
                        var parts = searchValue.split(/[-\\/]/);
                        searchValue = `${parts[2]}-${parts[1]}-${parts[0]}`;
                        $(this).val(searchValue);
                    }
                }
                table.ajax.reload(function() {
                    updateRecordCount();
                }, false);
            });

            // Event delegation for edit button
            $('#data-table tbody').on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                var rowData = table.row($(this).closest('tr')).data();
                if (rowData) {
                    openModal('update', id, rowData);
                }
            });

            // Event delegation for delete button
            $('#data-table tbody').on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                openConfirmModal('delete', id);
            });

            // Event delegation for status select
            $('#data-table tbody').on('change', '.status-select', function() {
                var id = $(this).data('id');
                var newStatus = $(this).val();
                $.post("<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>", {
                    action: 'update_status',
                    job_id: id,
                    completion: newStatus
                }, function(response) {
                    if (response.success) {
                        table.ajax.reload();
                    } else {
                        alert('Error updating status: ' + (response.error || 'Unknown error'));
                    }
                }, 'json').fail(function(xhr, error) {
                    console.error('Status update error:', error);
                    alert('Error updating status');
                });
            });

            // Event delegation for invoice button
            $('#data-table tbody').on('click', '.invoice-btn', function() {
                var id = $(this).data('id');
                openInvoiceModal(id);
            });

            // Adjust table on window resize
            $(window).on('resize', function() {
                table.columns.adjust();
            });
        });

        function openModal(action, id = null, data = null) {
            $('#crud-modal').show();
            $('#form-action').val(action);
            $('#modal-title').text(action === 'create' ? 'Add New Record' : 'Edit Record');
            if (action === 'create') {
                $('.primary-key-field').closest('.form-group').hide();
                $('#crud-form')[0].reset();
                $('#form-id').val('');
                $('.btn-option').removeClass('active');
                $('.nice-dropdown').val('');
            } else if (action === 'update' && data) {
                $('.primary-key-field').closest('.form-group').show();
                $('#form-id').val(id);
                var dateColumns = <?php echo json_encode($dateColumns); ?>;
                <?php foreach ($data['columns'] as $column): ?>
                    if (dateColumns.includes('<?php echo $column; ?>') && data.<?php echo htmlspecialchars($column); ?>) {
                        let dateValue = data.<?php echo htmlspecialchars($column); ?>;
                        if (dateValue && !/^\d{4}-\d{2}-\d{2}$/.test(dateValue)) {
                            try {
                                let date = new Date(dateValue);
                                dateValue = date.toISOString().split('T')[0];
                            } catch (e) {
                                dateValue = '';
                            }
                        }
                        $('#<?php echo $column; ?>').val(dateValue || '');
                    } else {
                        let value = data.<?php echo htmlspecialchars($column); ?> || '';
                        $('#<?php echo $column; ?>').val(value);
                        // Update dropdowns and button groups
                        var select = document.querySelector(`#crud-form select.nice-dropdown[onchange*="document.getElementById('<?php echo $column; ?>')"]`);
                        if (select) {
                            select.value = value;
                        }
                        var button = document.querySelector(`#crud-form .form-group button[data-value="${value}"][onclick*="selectOption('<?php echo $column; ?>')"]`);
                        if (button) {
                            document.querySelectorAll(`#crud-form .form-group button[onclick*="selectOption('<?php echo $column; ?>')"]`).forEach(btn => btn.classList.remove('active'));
                            button.classList.add('active');
                        }
                    }
                <?php endforeach; ?>
            }
        }

        function closeModal() {
            $('#crud-modal').hide();
            $('.primary-key-field').closest('.form-group').show();
        }

        function selectOption(fieldId, value) {
            document.getElementById(fieldId).value = value;
            const buttons = document.querySelectorAll(`#crud-form .form-group button[data-value][onclick*="${fieldId}"]`);
            buttons.forEach(btn => btn.classList.remove('active'));
            const selectedButton = document.querySelector(`#crud-form .form-group button[data-value="${value}"][onclick*="${fieldId}"]`);
            if (selectedButton) selectedButton.classList.add('active');
        }

        function openInvoiceModal(jobId) {
            $('#invoice-modal').show();
            $('#invoice-spinner').show();
            $('#invoice-details').hide();
            $.post("<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>", {
                action: 'get_invoice_details',
                job_id: jobId
            }, function(data) {
                $('#invoice-spinner').hide();
                $('#invoice-details').show();
                $('#invoice-no').text(data.invoice_no || '-');
                let formatDate = (dateStr) => {
                    if (!dateStr) return '-';
                    if (!/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
                        try {
                            let date = new Date(dateStr);
                            return date.toISOString().split('T')[0];
                        } catch (e) {
                            return dateStr;
                        }
                    }
                    return dateStr;
                };
                $('#invoice-date').text(formatDate(data.invoice_date));
                $('#invoice-value').text(data.invoice_value || '-');
                $('#invoice-job').text(data.job_details ? data.job_details.details : '-');
                $('#invoice-receiving').text(data.receiving_payment || '-');
                $('#invoice-received').text(data.received_amount || '-');
                $('#invoice-payment-date').text(formatDate(data.payment_received_date));
                $('#invoice-remarks').text(data.remarks || '-');
            }, 'json').fail(function(xhr, error) {
                $('#invoice-spinner').hide();
                $('#invoice-details').show();
                alert('Error loading invoice details: ' + error);
            });
        }

        function closeInvoiceModal() {
            $('#invoice-modal').hide();
        }

        function openConfirmModal(action, id = null) {
            $('#confirm-modal').show();
            $('#confirm-title').text(
                action === 'create' ? 'Confirm Add' :
                action === 'update' ? 'Confirm Update' :
                'Confirm Delete'
            );
            $('#confirm-message').text(
                action === 'create' ? 'Are you sure you want to add this new record?' :
                action === 'update' ? 'Are you sure you want to save changes to this record?' :
                'Are you sure you want to delete this record? This action cannot be undone.'
            );
            
            $('#confirm-action-btn').off('click').on('click', function() {
                var formData = $('#crud-form').serialize();
                if (action === 'create' || action === 'update') {
                    // Submit create or update via AJAX
                    $.ajax({
                        url: "<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>",
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                closeConfirmModal();
                                closeModal();
                                table.ajax.reload(function() {
                                    updateRecordCount();
                                }, false);
                                alert(action === 'create' ? 'Record added successfully!' : 'Record updated successfully!');
                            } else {
                                alert('Error ' + (action === 'create' ? 'adding' : 'updating') + ' record: ' + (response.error || 'Unknown error'));
                            }
                        },
                        error: function(xhr, error) {
                            console.error((action === 'create' ? 'Create' : 'Update') + ' error:', error);
                            alert('Error ' + (action === 'create' ? 'adding' : 'updating') + ' record: ' + (xhr.responseJSON?.error || 'Server error'));
                        }
                    });
                } else if (action === 'delete') {
                    // Perform the delete action
                    $.post("<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>", {
                        action: 'delete',
                        id: id
                    }, function(response) {
                        if (response.success) {
                            closeConfirmModal();
                            table.ajax.reload(function() {
                                updateRecordCount();
                            }, false);
                            alert('Record deleted successfully!');
                        } else {
                            alert('Error deleting record: ' + (response.error || 'Unknown error'));
                        }
                    }, 'json').fail(function(xhr, error) {
                        console.error('Delete error:', error);
                        alert('Error deleting record: ' + (xhr.responseJSON?.error || 'Server error'));
                    });
                }
            });
        }

        function closeConfirmModal() {
            $('#confirm-modal').hide();
        }
    </script>
</body>
</html>