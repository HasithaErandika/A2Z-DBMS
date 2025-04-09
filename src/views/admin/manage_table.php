<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/'); // Adjust this to your app's actual base path
$searchQuery = isset($_POST['search']) ? $_POST['search'] : ''; // Capture search query if submitted
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
            <form id="searchForm" method="POST" action="">
                <input class="search-input" id="searchInput" name="search" type="text" 
                       value="<?php echo htmlspecialchars($searchQuery); ?>" 
                       placeholder="Search table..." aria-label="Search table data">
            </form>
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
                    <!-- Initial data will be loaded via AJAX -->
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
                $dateColumns = ['date_started', 'date_completed', 'date','attendance_date', 'date_of_joined', 'date_of_resigned','date_of_birth','effective_date','end_date','expensed_date','invoice_date','payment_date','increment_date','txn_date'];
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
                            <select name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria Ordem-label="<?php echo htmlspecialchars($column); ?>">
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
                    <div class="invoice-item"><span class="label">Employee:</span><span id="invoice-emp">-</span></div>
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

        $(document).ready(function() {
            const table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                ajax: {
                    url: `${window.appConfig.basePath}/admin/manageTable/${window.appConfig.tableName}`,
                    type: 'POST',
                    data: function(d) {
                        d.action = 'get_records';
                        d.search = { value: $('#searchInput').val() || '<?php echo htmlspecialchars($searchQuery); ?>' };
                        d.sortColumn = window.appConfig.columns[d.order[0]?.column] || window.appConfig.columns[0];
                        d.sortOrder = d.order[0]?.dir || 'desc';
                        return d;
                    },
                    dataSrc: function(json) {
                        if (!json || json.error) {
                            alert('Failed to load table data: ' + (json?.error || 'Unknown error'));
                            return [];
                        }
                        $('#record-count').html(`<i class="fas fa-database"></i> ${json.recordsTotal} Records`);
                        return json.data;
                    },
                    beforeSend: function() {
                        $('#loading-spinner').show();
                    },
                    complete: function() {
                        $('#loading-spinner').hide();
                    },
                    error: function(xhr, status, error) {
                        alert('Error loading data: ' + (xhr.responseText || error));
                        $('#loading-spinner').hide();
                    }
                },
                columns: [
                    ...window.appConfig.columns.map(column => ({
                        data: column,
                        name: column,
                        render: function(data, type, row) {
                            if (data === null || data === undefined || data === '') return '-';
                            return type === 'display' ? String(data) : data;
                        }
                    })),
                    ...(window.appConfig.tableName === 'jobs' ? [{
                        data: 'completion',
                        render: function(data, type, row) {
                            if (type !== 'display') return data;
                            const completion = parseFloat(row.completion) || 0;
                            let statusClass, statusText, disabled = '';
                            switch (completion) {
                                case 0.0:
                                    statusClass = 'not-started';
                                    statusText = 'Start';
                                    break;
                                case 0.2:
                                    statusClass = 'started';
                                    statusText = 'Ongoing';
                                    break;
                                case 0.5:
                                    statusClass = 'ongoing';
                                    statusText = 'Complete';
                                    break;
                                case 1.0:
                                    statusClass = 'completed';
                                    statusText = 'Completed';
                                    disabled = 'disabled';
                                    break;
                                case 0.1:
                                    statusClass = 'cancelled';
                                    statusText = 'Cancelled';
                                    disabled = 'disabled';
                                    break;
                                default:
                                    statusClass = 'unknown';
                                    statusText = 'Unknown';
                                    disabled = 'disabled';
                            }
                            return `<button class="btn status-btn ${statusClass}" data-job-id="${row.job_id}" data-completion="${completion}" ${disabled}>${statusText}</button>`;
                        },
                        orderable: false,
                        searchable: false,
                        width: '150px'
                    }] : []),
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (type !== 'display') return '';
                            const rowData = JSON.stringify(row).replace(/"/g, '&quot;');
                            let buttons = `
                                <div class="action-buttons" style="display: flex; gap: 10px;">
                                    <button class="btn btn-primary tooltip edit-btn" data-row='${rowData}' data-tooltip="Edit record"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger tooltip delete-btn" data-id="${row[window.appConfig.primaryKey]}" data-tooltip="Delete record"><i class="fas fa-trash"></i></button>
                                    <button class="btn btn-info tooltip view-invoice-btn" data-job-id="${row.job_id}" data-tooltip="View invoice"><i class="fas fa-file-invoice"></i></button>
                            `;
                            if (window.appConfig.tableName === 'jobs' && completion !== 1.0 && completion !== 0.1) {
                                buttons += `
                                    <button class="btn btn-warning tooltip cancel-job-btn" data-job-id="${row.job_id}" data-tooltip="Cancel job"><i class="fas fa-ban"></i></button>
                                `;
                            }
                            buttons += `</div>`;
                            return buttons;
                        },
                        orderable: false,
                        searchable: false,
                        width: '200px'
                    }
                ],
                columnDefs: [
                    { targets: '_all', defaultContent: '-' }
                ],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[0, 'desc']],
                language: {
                    processing: 'Loading data...',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    emptyTable: 'No data available in table',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                },
                dom: 'lfrtip',
                initComplete: function() {
                    function debounce(func, wait) {
                        let timeout;
                        return function(...args) {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => func.apply(this, args), wait);
                        };
                    }

                    $('#searchInput').on('keyup', debounce(function() {
                        table.ajax.reload(null, false);
                    }, 300));

                    $('#data-table').on('click', '.edit-btn', function() {
                        openModal('edit', $(this).data('row'));
                    });
                    $('#data-table').on('click', '.delete-btn', function() {
                        deleteRecord($(this).data('id'));
                    });
                    $('#data-table').on('click', '.status-btn', function() {
                        updateStatus($(this).data('job-id'), this, parseFloat($(this).data('completion')));
                    });
                    $('#data-table').on('click', '.cancel-job-btn', function() {
                        cancelJob($(this).data('job-id'), this);
                    });
                    $('#data-table').on('click', '.view-invoice-btn', function() {
                        openInvoiceModal($(this).data('job-id'));
                    });
                }
            });

            $('<style>')
                .text(`
                    .status-btn { padding: 8px 16px; color: #fff; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; }
                    .not-started { background: #EF4444; }
                    .started { background: #3B82F6; }
                    .ongoing { background: #F59E0B; }
                    .completed { background: #10B981; }
                    .cancelled { background: #D1D5DB; color: #374151; }
                    .unknown { background: #6B7280; }
                    .status-btn:hover:not(:disabled) { opacity: 0.9; transform: translateY(-1px); }
                    .status-btn:disabled { opacity: 0.6; cursor: not-allowed; }
                    .action-buttons { display: flex; justify-content: center; align-items: center; }
                    .cancel-job-btn { background: #FBBF24; color: #fff; }
                    .cancel-job-btn:hover { background: #F59E0B; }
                    input[readonly] { background-color: #f0f0f0; cursor: not-allowed; }
                `)
                .appendTo('head');
        });

        function openInvoiceModal(jobId) {
            const modal = $('#invoice-modal');
            const spinner = $('#invoice-spinner');
            const details = $('#invoice-details');

            $('#invoice-no, #invoice-date, #invoice-value, #invoice-emp, #invoice-job, #invoice-receiving, #invoice-received, #invoice-payment-date, #invoice-remarks').text('-');
            spinner.show();
            details.hide();
            modal.css('display', 'flex');
            setTimeout(() => modal.addClass('active'), 10);

            $.ajax({
                url: `${window.appConfig.basePath}/admin/manageTable/jobs`,
                type: 'POST',
                data: { action: 'get_invoice_details', job_id: jobId },
                dataType: 'json',
                success: function(response) {
                    spinner.hide();
                    details.show();
                    if (!response || response.error || !response.data) {
                        alert('No invoice found for this job or error occurred: ' + (response.error || 'Unknown error'));
                        return;
                    }
                    const invoice = response.data;
                    $('#invoice-no').text(invoice.invoice_no || '-');
                    $('#invoice-date').text(invoice.invoice_date || '-');
                    $('#invoice-value').text(invoice.invoice_value ? `$${parseFloat(invoice.invoice_value).toFixed(2)}` : '-');
                    $('#invoice-emp').text(invoice.emp_id || '-');
                    $('#invoice-job').text(invoice.job_id || '-');
                    $('#invoice-receiving').text(invoice.receiving_payment ? 'Yes' : 'No');
                    $('#invoice-received').text(invoice.received_amount ? `$${parseFloat(invoice.received_amount).toFixed(2)}` : '-');
                    $('#invoice-payment-date').text(invoice.payment_received_date || '-');
                    $('#invoice-remarks').text(invoice.remarks || '-');
                },
                error: function(xhr, status, error) {
                    spinner.hide();
                    details.show();
                    alert('Failed to fetch invoice details: ' + (xhr.responseText || error));
                }
            });
        }

        function closeInvoiceModal() {
            const modal = $('#invoice-modal');
            modal.removeClass('active');
            setTimeout(() => modal.css('display', 'none'), 300);
        }

        function openModal(action, record = null) {
            const modal = $('#crud-modal');
            const title = $('#modal-title');
            const form = $('#crud-form');
            const actionInput = $('#form-action');
            const idInput = $('#form-id');

            form[0].reset();
            if (action === 'create') {
                title.text(`Add New ${window.appConfig.tableName} Record`);
                actionInput.val('create');
                idInput.val('');
                document.getElementById(window.appConfig.primaryKey).style.display = 'none';
                document.querySelector(`label[for="${window.appConfig.primaryKey}"]`).style.display = 'none';
            } else if (action === 'edit' && record) {
                title.text(`Edit ${window.appConfig.tableName} Record`);
                actionInput.val('update');
                try {
                    const data = typeof record === 'string' ? JSON.parse(record) : record;
                    idInput.val(data[window.appConfig.primaryKey] || '');
                    window.appConfig.columns.forEach(column => {
                        const element = document.getElementById(column);
                        if (element) {
                            element.value = data[column] || '';
                        }
                    });
                    document.getElementById(window.appConfig.primaryKey).style.display = 'block';
                    document.querySelector(`label[for="${window.appConfig.primaryKey}"]`).style.display = 'block';
                } catch (e) {
                    alert('Failed to load record for editing.');
                    return;
                }
            }
            modal.css('display', 'flex');
            setTimeout(() => modal.addClass('active'), 10);
        }

        function closeModal() {
            const modal = $('#crud-modal');
            modal.removeClass('active');
            setTimeout(() => modal.css('display', 'none'), 300);
            document.getElementById(window.appConfig.primaryKey).style.display = 'block';
            document.querySelector(`label[for="${window.appConfig.primaryKey}"]`).style.display = 'block';
        }

        function deleteRecord(id) {
            if (!confirm('Are you sure you want to delete this record?')) return;
            $.ajax({
                url: `${window.appConfig.basePath}/admin/manageTable/${window.appConfig.tableName}`,
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function(response) {
                    $('#data-table').DataTable().ajax.reload(null, false);
                },
                error: function(xhr, status, error) {
                    alert('Failed to delete record: ' + (xhr.responseText || error));
                }
            });
        }

        function cancelJob(jobId, button) {
            if (!confirm('Are you sure you want to cancel this job? This action cannot be undone.')) return;

            $.ajax({
                url: `${window.appConfig.basePath}/admin/manageTable/jobs`,
                type: 'POST',
                data: { 
                    action: 'update_status', 
                    job_id: jobId, 
                    completion: 0.1 
                },
                dataType: 'json',
                success: function(response) {
                    if (!response || !response.success) {
                        alert('Failed to cancel job: ' + (response?.error || 'Unknown error'));
                        return;
                    }
                    $(button).closest('td').siblings('.status-btn').removeClass('not-started started ongoing completed')
                        .addClass('cancelled')
                        .text('Cancelled')
                        .prop('disabled', true)
                        .data('completion', 0.1);
                    $(button).remove();
                    $('#data-table').DataTable().ajax.reload(null, false);
                },
                error: function(xhr, status, error) {
                    alert('Failed to cancel job: ' + (xhr.responseText || error));
                }
            });
        }

        function updateStatus(jobId, button, currentCompletion) {
            if (currentCompletion === 1.0 || currentCompletion === 0.1) return;

            const statusMap = {
                0.0: { next: 0.2, class: 'started', text: 'Ongoing' },
                0.2: { next: 0.5, class: 'ongoing', text: 'Complete' },
                0.5: { next: 1.0, class: 'completed', text: 'Completed' }
            };

            const nextStatus = statusMap[currentCompletion];
            if (!nextStatus) {
                alert('Invalid current status');
                return;
            }

            $.ajax({
                url: `${window.appConfig.basePath}/admin/manageTable/jobs`,
                type: 'POST',
                data: { 
                    action: 'update_status', 
                    job_id: jobId, 
                    completion: nextStatus.next 
                },
                dataType: 'json',
                success: function(response) {
                    if (!response || !response.success) {
                        alert('Failed to update status: ' + (response?.error || 'Unknown error'));
                        return;
                    }
                    $(button).removeClass('not-started started ongoing completed')
                        .addClass(nextStatus.class)
                        .text(nextStatus.text)
                        .data('completion', nextStatus.next);
                    
                    if (nextStatus.next === 1.0) {
                        $(button).prop('disabled', true);
                        $(button).closest('td').siblings('.action-buttons').find('.cancel-job-btn').remove();
                    }
                    $('#data-table').DataTable().ajax.reload(null, false);
                },
                error: function(xhr, status, error) {
                    alert('Failed to update status: ' + (xhr.responseText || error));
                }
            });
        }

        $('#crud-modal').on('click', function(e) {
            if (e.target === this) closeModal();
        });

        $('#invoice-modal').on('click', function(e) {
            if (e.target === this) closeInvoiceModal();
        });
    </script>
</body>
</html>