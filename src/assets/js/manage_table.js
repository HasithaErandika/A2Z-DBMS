function updateStatus(jobId, $button) {
    const currentCompletion = parseFloat($button.data('completion')) || 0.0;

    if (currentCompletion === 1.0 || currentCompletion === 0.1) return;

    const statusMap = {
        0.0: 0.2, // Not Started → Started
        0.2: 0.5, // Started → Ongoing
        0.5: 1.0  // Ongoing → Completed
    };

    const nextCompletion = statusMap[currentCompletion];
    if (!nextCompletion) {
        alert('Invalid status transition from ' + currentCompletion);
        return;
    }

    $.ajax({
        url: `${window.appConfig.basePath}/admin/manageTable/jobs`,
        type: 'POST',
        data: { action: 'update_status', job_id: jobId, completion: nextCompletion },
        dataType: 'json',
        success: function(response) {
            if (!response || !response.success) {
                alert('Failed to update status: ' + (response?.error || 'Unknown error'));
                return;
            }

            const newCompletion = parseFloat(response.completion);
            $button.data('completion', newCompletion); // Update button data attribute
            $button.attr('data-completion', newCompletion); // Ensure DOM attribute updates

            // Update button text and state
            if (newCompletion === 1.0) {
                $button.text('Completed');
                $button.prop('disabled', true);
                $button.closest('td').siblings('.action-buttons').find('.cancel-job-btn').remove();
            }

            // Update DataTable row data
            const $row = $button.closest('tr');
            const rowData = $('#data-table').DataTable().row($row).data();
            rowData.completion = newCompletion;
            $('#data-table').DataTable().row($row).data(rowData).draw(false); // Redraw row without full reload

            // Optional: Full table reload if needed
            // $('#data-table').DataTable().ajax.reload(null, false);
        },
        error: function(xhr, status, error) {
            alert('Failed to update status: ' + (xhr.responseText || error));
        }
    });
}

function renderStatusButton(data, type, row) {
    if (type !== 'display') return data;
    const completion = parseFloat(row.completion) || 0.0;

    let buttonText = 'Change Status';
    let disabled = '';

    if (completion === 1.0) {
        buttonText = 'Completed';
        disabled = 'disabled';
    } else if (completion === 0.1) {
        buttonText = 'Cancelled';
        disabled = 'disabled';
    }

    return `
        <button class="btn status-btn" 
                data-job-id="${row.job_id}" 
                data-completion="${completion}" 
                ${disabled}>
            ${buttonText}
        </button>
    `;
}

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
                d.search = { value: $('#searchInput').val() || '' };
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
                    if (type !== 'display') return data;
                    if (column === 'job_id' && typeof data === 'object' && data.job_id) {
                        return `${data.job_id} - ${data.project_description} - ${data.engineer}`;
                    }
                    return String(data);
                }
            })),
            ...(window.appConfig.tableName === 'jobs' ? [{
                data: 'completion',
                render: renderStatusButton,
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
                        const hasInvoice = row.has_invoice === true || row.has_invoice === 1;
                        buttons += `
                            <button class="btn view-invoice-btn tooltip" 
                                    data-job-id="${row.job_id}" 
                                    data-tooltip="View invoice" 
                                    style="background-color: ${hasInvoice ? '#17A2B8' : '#DC3545'}; color: white;">
                                <i class="fas fa-file-invoice"></i>
                            </button>
                        `;
                    }
                    if (window.appConfig.tableName === 'jobs' && parseFloat(row.completion) !== 1.0 && parseFloat(row.completion) !== 0.1) {
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
        columnDefs: [{ targets: '_all', defaultContent: '-' }],
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[0, 'desc']],
        language: {
            processing: 'Loading data...',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            emptyTable: 'No data available in table',
            paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' }
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
                updateStatus($(this).data('job-id'), $(this));
            });
            $('#data-table').on('click', '.cancel-job-btn', function() {
                cancelJob($(this).data('job-id'), this);
            });
            $('#data-table').on('click', '.view-invoice-btn', function() {
                openInvoiceModal($(this).data('job-id'));
            });
        }
    });
});

function getCompletionStatus(value) {
    switch (parseFloat(value)) {
        case 0.0: return '<span style="color: #EF4444;">Not Started</span>';
        case 0.1: return '<span style="color: #D1D5DB;">Cancelled</span>';
        case 0.2: return '<span style="color: #3B82F6;">Started</span>';
        case 0.5: return '<span style="color: #F59E0B;">Ongoing</span>';
        case 1.0: return '<span style="color: #10B981;">Completed</span>';
        default: return value;
    }
}


function openInvoiceModal(jobId) {
    $.ajax({
        url: `${window.appConfig.basePath}/admin/manageTable/${window.appConfig.tableName}`,
        type: 'POST',
        data: { action: 'get_invoice_details', job_id: jobId },
        dataType: 'json',
        success: function(response) {
            if (!response || Object.keys(response).length === 0) {
                window.location.href = `${window.appConfig.basePath}/admin/manageTable/invoice_data`;
                return;
            }
            const modal = $('#invoice-modal');
            const spinner = $('#invoice-spinner');
            const details = $('#invoice-details');

            $('#invoice-no, #invoice-date, #invoice-value, #invoice-job, #invoice-receiving, #invoice-received, #invoice-payment-date, #invoice-remarks').text('-');
            spinner.show();
            details.hide();
            modal.css('display', 'flex');
            setTimeout(() => modal.addClass('active'), 10);

            spinner.hide();
            details.show();
            $('#invoice-no').text(response.invoice_no || '-');
            $('#invoice-date').text(response.invoice_date || '-');
            $('#invoice-value').text(response.invoice_value ? `$${response.invoice_value}` : '-');
            $('#invoice-job').text(response.job_details ? `${response.job_details.job_id} - ${response.job_details.customer_reference}` : response.job_id || '-');
            $('#invoice-receiving').text(response.receiving_payment ? `$${response.receiving_payment}` : '-');
            $('#invoice-received').text(response.received_amount ? `$${response.received_amount}` : '-');
            $('#invoice-payment-date').text(response.payment_received_date || '-');
            $('#invoice-remarks').text(response.remarks || '-');
        },
        error: function(xhr, status, error) {
            console.error('Error fetching invoice details:', xhr.responseText || error);
            window.location.href = `${window.appConfig.basePath}/admin/manageTable/invoice_data`;
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
                    // Handle job_id object in edit modal
                    if (column === 'job_id' && typeof data[column] === 'object' && data[column].job_id) {
                        element.value = data[column].job_id; // Use job_id for select input
                    } else {
                        element.value = data[column] || '';
                    }
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
        data: { action: 'update_status', job_id: jobId, completion: 0.1 },
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


$('#crud-modal').on('click', function(e) {
    if (e.target === this) closeModal();
});

$('#invoice-modal').on('click', function(e) {
    if (e.target === this) closeInvoiceModal();
});