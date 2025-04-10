$(document).ready(function() {
    var tableName = window.appConfig.tableName;
    var basePath = window.appConfig.basePath;
    var dataTable = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `${basePath}/admin/manageTable/${tableName}`,
            type: 'POST',
            data: function(d) {
                d.action = 'get_records';
                d.sortColumn = d.columns[d.order[0].column].data;
                d.sortOrder = d.order[0].dir;
            }
        },
        columns: [
            { data: 'job_id' },
            { data: 'engineer' },
            { data: 'customer_reference' },
            { data: 'location' },
            { data: 'date_started' },
            { data: 'date_completed' },
            { data: 'completion', render: function(data) { return data; } }, // Already formatted by getCompletionStatus
            {
                data: 'job_id',
                render: function(data, type, row) {
                    var hasInvoice = row.has_invoice;
                    var btnClass = hasInvoice ? 'invoice-btn has-invoice' : 'invoice-btn no-invoice';
                    return '<button class="' + btnClass + '" data-job-id="' + data + '"><i class="fas fa-file-invoice"></i></button>';
                },
                orderable: false
            },
            {
                data: 'job_id',
                render: function(data) {
                    return '<button class="status-btn" data-job-id="' + data + '"><i class="fas fa-sync-alt"></i></button>';
                },
                orderable: false
            },
            {
                data: 'job_id',
                render: function(data) {
                    return '<button class="cancel-btn" data-job-id="' + data + '"><i class="fas fa-ban"></i></button>';
                },
                orderable: false
            },
            {
                data: 'job_id',
                render: function(data) {
                    return '<button class="edit-btn" data-id="' + data + '"><i class="fas fa-edit"></i></button>' +
                           '<button class="delete-btn" data-id="' + data + '"><i class="fas fa-trash-alt"></i></button>';
                },
                orderable: false
            }
        ]
    });

    // Invoice button click handler
    $('#dataTable tbody').on('click', '.invoice-btn', function() {
        var jobId = $(this).data('job-id');
        openInvoiceModal(jobId);
    });

    // Create button handler
    $('#create-btn').on('click', function() {
        $('#crud-action').val('create');
        $('#crud-form')[0].reset();
        $('#crud-modal').css('display', 'flex').addClass('active');
    });

    // Close modal
    $('#crud-close-btn').on('click', function() {
        $('#crud-modal').removeClass('active').css('display', 'none');
    });

    // Form submission for create/update
    $('#crud-form').on('submit', function(e) {
        e.preventDefault();
        var action = $('#crud-action').val();
        var formData = $(this).serialize() + '&action=' + action;
        $.ajax({
            url: `${basePath}/admin/manageTable/${tableName}`,
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#crud-modal').removeClass('active').css('display', 'none');
                dataTable.ajax.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseText);
            }
        });
    });

    // Edit button handler
    $('#dataTable tbody').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        var rowData = dataTable.row($(this).closest('tr')).data();
        $('#crud-action').val('update');
        $('#crud-form [name="id"]').val(id);
        $('#crud-form [name="engineer"]').val(rowData.engineer);
        $('#crud-form [name="customer_reference"]').val(rowData.customer_reference);
        $('#crud-form [name="location"]').val(rowData.location);
        $('#crud-form [name="date_started"]').val(rowData.date_started);
        $('#crud-form [name="date_completed"]').val(rowData.date_completed);
        $('#crud-form [name="completion"]').val(rowData.completion.match(/[\d.]+/)[0]); // Extract numeric value
        $('#crud-modal').css('display', 'flex').addClass('active');
    });

    // Delete button handler
    $('#dataTable tbody').on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this record?')) {
            var id = $(this).data('id');
            $.ajax({
                url: `${basePath}/admin/manageTable/${tableName}`,
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function() {
                    dataTable.ajax.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        }
    });

    // Update status handler
    $('#dataTable tbody').on('click', '.status-btn', function() {
        var jobId = $(this).data('job-id');
        $.ajax({
            url: `${basePath}/admin/manageTable/${tableName}`,
            type: 'POST',
            data: { action: 'update_status', job_id: jobId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    dataTable.ajax.reload();
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseText);
            }
        });
    });

    // Cancel job handler
    $('#dataTable tbody').on('click', '.cancel-btn', function() {
        var jobId = $(this).data('job-id');
        if (confirm('Are you sure you want to cancel this job?')) {
            $.ajax({
                url: `${basePath}/admin/manageTable/${tableName}`,
                type: 'POST',
                data: { action: 'update_status', job_id: jobId, completion: 0.1 },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        dataTable.ajax.reload();
                    } else {
                        alert('Error: ' . response.error);
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        }
    });

    // Export CSV button handler
    $('#export-btn').on('click', function() {
        var startDate = $('#start-date').val();
        var endDate = $('#end-date').val();
        window.location.href = `${basePath}/admin/manageTable/${tableName}?action=export_csv&start_date=${startDate}&end_date=${endDate}`;
    });

    function openInvoiceModal(jobId) {
        $.ajax({
            url: `${basePath}/admin/manageTable/${tableName}`,
            type: 'POST',
            data: { action: 'get_invoice_details', job_id: jobId },
            dataType: 'json',
            success: function(response) {
                if (!response || Object.keys(response).length === 0) {
                    if (confirm('No invoice found for this job. Would you like to create one?')) {
                        window.location.href = `${basePath}/admin/manageTable/invoice_data`;
                    }
                } else {
                    var modalContent = `
                        <p><strong>Invoice No:</strong> ${response.invoice_no || 'N/A'}</p>
                        <p><strong>Invoice Date:</strong> ${response.invoice_date || 'N/A'}</p>
                        <p><strong>Invoice Value:</strong> ${response.invoice_value || 'N/A'}</p>
                        <p><strong>Received Amount:</strong> ${response.received_amount || 'N/A'}</p>
                        <p><strong>Payment Received Date:</strong> ${response.payment_received_date || 'N/A'}</p>
                        <p><strong>Remarks:</strong> ${response.remarks || 'N/A'}</p>
                    `;
                    if (response.job_details) {
                        modalContent += `
                            <p><strong>Job Details:</strong></p>
                            <ul>
                                <li>Job ID: ${response.job_details.job_id}</li>
                                <li>Engineer: ${response.job_details.engineer}</li>
                                <li>Customer: ${response.job_details.customer_reference}</li>
                            </ul>
                        `;
                    }
                    $('#invoice-modal-content').html(modalContent);
                    $('#invoice-modal').css('display', 'flex').addClass('active');
                }
            },
            error: function(xhr) {
                alert('Error fetching invoice details: ' + xhr.responseText);
            }
        });
    }

    // Close invoice modal
    $('#invoice-close-btn').on('click', function() {
        $('#invoice-modal').removeClass('active').css('display', 'none');
    });
});