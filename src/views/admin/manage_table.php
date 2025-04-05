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
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/src/assets/css/manage_table.css">
    <style>
        .column-search { margin-bottom: 10px; }
        .column-search input, .column-search select { width: 100%; padding: 5px; margin-top: 5px; }
        .global-search { margin-bottom: 20px; }
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

        <!-- Global Search Bar -->
        <div class="global-search">
            <input type="text" id="global-search" placeholder="Search all columns (e.g., name:John status:completed)" style="width: 100%; padding: 8px;">
        </div>

        <!-- Column-Specific Search Inputs -->
        <div class="column-search">
            <?php foreach ($data['columns'] as $column): ?>
                <div style="display: inline-block; width: <?php echo 100 / count($data['columns']); ?>%; padding: 5px;">
                    <label for="search-<?php echo $column; ?>"><?php echo htmlspecialchars($column); ?></label>
                    <?php if ($column === 'completion' && $data['table'] === 'jobs'): ?>
                        <select id="search-<?php echo $column; ?>" class="column-filter">
                            <option value="">All</option>
                            <option value="0.0">Not Started</option>
                            <option value="0.2">Started</option>
                            <option value="0.5">Ongoing</option>
                            <option value="1.0">Completed</option>
                        </select>
                    <?php elseif (in_array($column, ['date_started', 'date_completed', 'date', 'attendance_date', 'invoice_date'])): ?>
                        <input type="date" id="search-<?php echo $column; ?>" class="column-filter">
                    <?php else: ?>
                        <input type="text" id="search-<?php echo $column; ?>" class="column-filter" placeholder="Filter <?php echo $column; ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
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
                    <?php if (!empty($data['records'])): ?>
                        <?php foreach ($data['records'] as $record): ?>
                            <tr>
                                <?php foreach ($data['columns'] as $column): ?>
                                    <td><?php echo htmlspecialchars($record[$column] ?? '-'); ?></td>
                                <?php endforeach; ?>
                                <?php if ($data['table'] === 'jobs'): ?>
                                    <td><?php echo $record['completion'] ?? '-'; ?></td>
                                <?php endif; ?>
                                <td>-</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="<?php echo count($data['columns']) + ($data['table'] === 'jobs' ? 2 : 1); ?>">Loading data...</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="spinner" id="loading-spinner"><i class="fas fa-spinner"></i></div>
        </div>
    </div>

    <!-- CRUD Modal (unchanged) -->
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
                $dateColumns = ['date_started', 'date_completed', 'date','attendance_date', 'date_of_joined', 'date_of_resigned','date_of_birth','effective_date','end_date','expensed_date','invoice_date','payment_date','increment_date'];
                $timeColumns = ['start_time', 'end_time'];
                foreach ($data['columns'] as $column): ?>
                    <div class="form-group">
                        <label for="<?php echo $column; ?>"><?php echo htmlspecialchars($column); ?></label>
                        <?php if ($column === $primaryKey): ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" readonly aria-label="<?php echo htmlspecialchars($column); ?>">
                        <?php elseif ($column === 'emp_id' && $data['table'] !== 'employees'): ?>
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

    <!-- Invoice Modal (unchanged) -->
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
        console.log('App Config:', window.appConfig);

        $(document).ready(function() {
            console.log('Initializing DataTable for:', window.appConfig.tableName);
            console.log('Columns:', window.appConfig.columns);

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
                        d.globalSearch = $('#global-search').val() || '';
                        d.columnFilters = {};
                        $('.column-filter').each(function() {
                            const column = $(this).attr('id').replace('search-', '');
                            const value = $(this).val();
                            if (value !== '') { // Only include non-empty filters
                                d.columnFilters[column] = value;
                            }
                        });
                        d.sortColumn = window.appConfig.columns[d.order[0]?.column] || '';
                        d.sortOrder = d.order[0]?.dir || 'desc';
                        console.log('AJAX Request Data:', d);
                        return d;
                    },
                    dataSrc: function(json) {
                        console.log('AJAX Response:', json);
                        if (!json || json.error) {
                            console.error('Error in response:', json?.error || 'No data returned');
                            alert('Failed to load table data: ' + (json?.error || 'Unknown error'));
                            return [];
                        }
                        if (!Array.isArray(json.data)) {
                            console.error('Invalid data format:', json.data);
                            return [];
                        }
                        return json.data;
                    },
                    beforeSend: function() {
                        $('#loading-spinner').show();
                        console.log('Fetching data...');
                    },
                    complete: function() {
                        $('#loading-spinner').hide();
                        console.log('Data fetch complete');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr.responseText);
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
                                    statusText = 'Mark as Ongoing';
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
                                default:
                                    statusClass = 'unknown';
                                    statusText = 'Unknown Status';
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
                            const rowData = JSON.stringify(row).replace(/"/g, '"');
                            let buttons = `
                                <div class="action-buttons" style="display: flex; gap: 10px;">
                                    <button class="btn btn-primary tooltip edit-btn" data-row='${rowData}' data-tooltip="Edit record"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger tooltip delete-btn" data-id="${row[window.appConfig.primaryKey]}" data-tooltip="Delete record"><i class="fas fa-trash"></i></button>
                            `;
                            if (window.appConfig.tableName === 'jobs') {
                                buttons += `
                                    <button class="btn btn-info tooltip view-invoice-btn" data-job-id="${row.job_id}" data-tooltip="View invoice"><i class="fas fa-file-invoice"></i></button>
                                `;
                            }
                            buttons += `</div>`;
                            return buttons;
                        },
                        orderable: false,
                        searchable: false,
                        width: '150px'
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
                    search: 'Global Search:',
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
                    console.log('DataTable initialized');

                    // Debounce function to prevent excessive AJAX calls
                    function debounce(func, wait) {
                        let timeout;
                        return function(...args) {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => func.apply(this, args), wait);
                        };
                    }

                    // Global search event with debounce
                    $('#global-search').on('keyup', debounce(function() {
                        table.ajax.reload();
                    }, 300));

                    // Column-specific search events with debounce
                    $('.column-filter').on('keyup change', debounce(function() {
                        table.ajax.reload();
                    }, 300));

                    $('#data-table').off('click', '.edit-btn').on('click', '.edit-btn', function() {
                        const rowData = $(this).data('row');
                        console.log('Edit button clicked, row data:', rowData);
                        openModal('edit', rowData);
                    });
                    $('#data-table').off('click', '.delete-btn').on('click', '.delete-btn', function() {
                        const id = $(this).data('id');
                        console.log('Delete button clicked, ID:', id);
                        deleteRecord(id);
                    });
                    $('#data-table').off('click', '.status-btn').on('click', '.status-btn', function() {
                        const jobId = $(this).data('job-id');
                        const completion = parseFloat($(this).data('completion'));
                        console.log('Status button clicked, Job ID:', jobId, 'Completion:', completion);
                        updateStatus(jobId, this, completion);
                    });
                    $('#data-table').off('click', '.view-invoice-btn').on('click', '.view-invoice-btn', function() {
                        const jobId = $(this).data('job-id');
                        console.log('View invoice button clicked, Job ID:', jobId);
                        openInvoiceModal(jobId);
                    });
                }
            });

            $('<style>')
                .text(`
                    .status-btn { padding: 8px 16px; color: #fff; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; }
                    .not-started { background: #EF4444; } /* Red */
                    .started { background: #3B82F6; } /* Blue */
                    .ongoing { background: #F59E0B; } /* Orange */
                    .completed { background: #10B981; } /* Green */
                    .unknown { background: #6B7280; } /* Gray */
                    .status-btn:hover:not(:disabled) { opacity: 0.9; transform: translateY(-1px); }
                    .status-btn:disabled { opacity: 0.6; cursor: not-allowed; }
                    .action-buttons { display: flex; justify-content: center; align-items: center; }
                    input[readonly] { background-color: #f0f0f0; cursor: not-allowed; }
                `)
                .appendTo('head');
        });

        function openInvoiceModal(jobId) {
            const modal = $('#invoice-modal');
            const spinner = $('#invoice-spinner');
            const details = $('#invoice-details');

            console.log('Opening invoice modal for jobId:', jobId);
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
                    console.log('Invoice AJAX Success Response:', response);
                    spinner.hide();
                    details.show();

                    if (!response || response.error || !response.data) {
                        console.warn('Invalid invoice response:', response);
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
                    console.error('Invoice AJAX Error:', status, error, xhr.responseText);
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
                    console.error('Error parsing record data:', e);
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
                    console.log('Delete Response:', response);
                    $('#data-table').DataTable().ajax.reload(null, false);
                },
                error: function(xhr, status, error) {
                    console.error('Delete Error:', status, error, xhr.responseText);
                    alert('Failed to delete record: ' + (xhr.responseText || error));
                }
            });
        }

        function updateStatus(jobId, button, currentCompletion) {
            if (currentCompletion === 1.0) return;

            let nextCompletion;
            switch (currentCompletion) {
                case 0.0:
                    nextCompletion = 0.2;
                    break;
                case 0.2:
                    nextCompletion = 0.5;
                    break;
                case 0.5:
                    nextCompletion = 1.0;
                    break;
                default:
                    console.error('Invalid current completion value:', currentCompletion);
                    return;
            }

            console.log('Updating status for Job ID:', jobId, 'from', currentCompletion, 'to', nextCompletion);

            $.ajax({
                url: `${window.appConfig.basePath}/admin/manageTable/jobs`,
                type: 'POST',
                data: { 
                    action: 'update_status', 
                    job_id: jobId, 
                    completion: nextCompletion 
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Status Update Response:', response);
                    if (!response || !response.success) {
                        console.error('Status update failed:', response?.error || 'No success flag');
                        alert('Failed to update status: ' + (response?.error || 'Unknown error'));
                        return;
                    }

                    const completion = parseFloat(response.completion);
                    console.log('Server confirmed completion:', completion);

                    const statusMap = {
                        0.0: { class: 'not-started', text: 'Start', color: '#EF4444' },
                        0.2: { class: 'started', text: 'Mark as Ongoing', color: '#3B82F6' },
                        0.5: { class: 'ongoing', text: 'Complete', color: '#F59E0B' },
                        1.0: { class: 'completed', text: 'Completed', color: '#10B981' }
                    };

                    const statusInfo = statusMap[completion] || { class: 'unknown', text: 'Unknown Status', color: '#6B7280' };
                    $(button)
                        .removeClass('not-started started ongoing completed unknown')
                        .addClass(statusInfo.class)
                        .text(statusInfo.text)
                        .data('completion', completion)
                        .css('background-color', statusInfo.color);
                    if (completion === 1.0) {
                        $(button).prop('disabled', true);
                    }

                    $('#data-table').DataTable().ajax.reload(null, false);
                },
                error: function(xhr, status, error) {
                    console.error('Status Update Error:', status, error, xhr.responseText);
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