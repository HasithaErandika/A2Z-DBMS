// Utility function to debounce input events
const debounce = (func, wait) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
};

// Format completion status for display
const getCompletionStatus = (value) => {
    switch (parseFloat(value)) {
        case 0.0: return '<span style="color: #EF4444;">Not Started</span>';
        case 0.1: return '<span style="color: #D1D5DB;">Cancelled</span>';
        case 0.2: return '<span style="color: #3B82F6;">Started</span>';
        case 0.5: return '<span style="color: #F59E0B;">Ongoing</span>';
        case 1.0: return '<span style="color: #10B981;">Completed</span>';
        default: return String(value);
    }
};

// Render status select dropdown for jobs table
const renderStatusButton = (data, type, row) => {
    if (type !== 'display') return data;
    return `
        <select class="status-select" data-id="${row[window.appConfig.primaryKey]}" aria-label="Job status">
            <option value="0.0" ${data == '0.0' ? 'selected' : ''}>Not Started</option>
            <option value="0.1" ${data == '0.1' ? 'selected' : ''}>Cancelled</option>
            <option value="0.2" ${data == '0.2' ? 'selected' : ''}>Started</option>
            <option value="0.5" ${data == '0.5' ? 'selected' : ''}>Ongoing</option>
            <option value="1.0" ${data == '1.0' ? 'selected' : ''}>Completed</option>
        </select>
    `;
};

// Format date for form inputs
const formatDateForInput = (dateStr) => {
    if (!dateStr) return '';
    if (!/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
        try {
            const date = new Date(dateStr);
            return date.toISOString().split('T')[0];
        } catch (e) {
            return '';
        }
    }
    return dateStr;
};

// Initialize DataTable and event handlers
$(document).ready(function () {
    // Ensure modals are hidden on page load
    $('#crud-modal').hide().attr('aria-hidden', 'true');
    $('#invoice-modal').hide().attr('aria-hidden', 'true');
    $('#confirm-modal').hide().attr('aria-hidden', 'true');

    const table = $('#data-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        autoWidth: false,
        ajax: {
            url: `${window.appConfig.basePath}/admin/manageTable/${window.appConfig.tableName}`,
            type: 'POST',
            data: function (d) {
                return {
                    action: 'get_records',
                    search: { value: $('#searchInput').val() || '' },
                    sortColumn: window.appConfig.columns[d.order[0]?.column] || window.appConfig.columns[0],
                    sortOrder: d.order[0]?.dir || 'desc',
                    page: Math.floor(d.start / d.length) + 1,
                    perPage: d.length,
                    draw: d.draw
                };
            },
            dataSrc: function (json) {
                if (!json || json.error) {
                    alert(`Failed to load table data: ${json?.error || 'Unknown error'}`);
                    console.error('DataTable error:', json?.error || 'Unknown error');
                    return [];
                }
                const recordCountText = `${json.recordsTotal} Records` +
                    (json.recordsFiltered !== json.recordsTotal ? ` (${json.recordsFiltered} Filtered)` : '');
                $('#record-count').html(`<i class="fas fa-database" aria-hidden="true"></i> ${recordCountText}`);
                return json.data;
            },
            beforeSend: function () {
                $('#loading-spinner').show();
                $('#data-table').hide();
            },
            complete: function () {
                $('#loading-spinner').hide();
                $('#data-table').show();
            },
            error: function (xhr, status, error) {
                let message = '';
                // Try to show the raw response if not valid JSON
                if (xhr.responseJSON) {
                    message = xhr.responseJSON.error || error || 'Server error';
                } else if (xhr.responseText && xhr.responseText.trim().charAt(0) === '<') {
                    // Looks like HTML (probably a PHP warning or error)
                    message = 'Server returned HTML instead of JSON.\n\n' + xhr.responseText;
                } else {
                    message = xhr.responseText || error || 'Server error';
                }
                alert(`Error loading data: ${message}`);
                console.error('DataTable AJAX error:', status, error, xhr.responseText);
                $('#loading-spinner').hide();
                $('#data-table').show();
            }
        },
        columns: [
            ...window.appConfig.columns.map(column => ({
                data: column,
                name: column,
                render: (data, type) => {
                    if (type !== 'display') return data;
                    return data === null || data === undefined || data === '' ? '-' : String(data);
                }
            })),
            ...(window.appConfig.tableName === 'jobs' ? [{
                data: 'completion',
                name: 'completion',
                render: renderStatusButton,
                orderable: false,
                searchable: false,
                width: '150px'
            }] : []),
            {
                data: null,
                render: (data, type, row) => {
                    if (type !== 'display') return '';
                    const rowData = JSON.stringify(row).replace(/"/g, '"');
                    let buttons = `
                        <div class="action-buttons" style="display: flex; gap: 10px;">
                            <button class="btn btn-primary tooltip edit-btn" data-row='${rowData}' aria-label="Edit record" data-tooltip="Edit record"><i class="fas fa-edit" aria-hidden="true"></i></button>
                            <button class="btn btn-danger tooltip delete-btn" data-id="${row[window.appConfig.primaryKey]}" aria-label="Delete record" data-tooltip="Delete record"><i class="fas fa-trash" aria-hidden="true"></i></button>
                    `;
                    if (window.appConfig.tableName === 'jobs') {
                        const hasInvoice = row.has_invoice === true || row.has_invoice === '1';
                        buttons += `
                            <button class="btn view-invoice-btn tooltip" 
                                    data-job-id="${row.job_id}" 
                                    aria-label="${hasInvoice ? 'View invoice' : 'No invoice available'}" 
                                    data-tooltip="${hasInvoice ? 'View invoice' : 'No invoice available'}"
                                    style="background-color: ${hasInvoice ? '#17A2B8' : '#DC3545'}; color: white;">
                                <i class="fas fa-file-invoice" aria-hidden="true"></i>
                            </button>
                        `;
                        if (parseFloat(row.completion) !== 1.0 && parseFloat(row.completion) !== 0.1) {
                            buttons += `
                                <button class="btn btn-warning tooltip cancel-job-btn" 
                                        data-job-id="${row.job_id}" 
                                        aria-label="Cancel job" 
                                        data-tooltip="Cancel job">
                                    <i class="fas fa-ban" aria-hidden="true"></i>
                                </button>
                            `;
                        }
                    }
                    buttons += '</div>';
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
        initComplete: function () {
            // Debounced search
            $('#searchInput').on('keyup', debounce(() => {
                table.ajax.reload(null, false);
            }, 300));

            // Event delegation for buttons
            $('#data-table').on('click', '.edit-btn', function () {
                openModal('edit', $(this).data('row'));
            });

            $('#data-table').on('click', '.delete-btn', function () {
                deleteRecord($(this).data('id'));
            });

            $('#data-table').on('click', '.view-invoice-btn', function () {
                openInvoiceModal($(this).data('job-id'));
            });

            $('#data-table').on('click', '.cancel-job-btn', function () {
                cancelJob($(this).data('job-id'));
            });

            $('#data-table').on('change', '.status-select', function () {
                updateStatus($(this).data('id'), $(this).val());
            });

            // Keyboard navigation for modals
            $(document).on('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeModal();
                    closeInvoiceModal();
                    closeConfirmModal();
                }
            });
        }
    });

    // Open CRUD modal
    window.openModal = (action, rowData = null) => {
        const isCreate = action === 'create';
        $('#crud-modal').show().attr('aria-hidden', 'false');
        $('#form-action').val(isCreate ? 'create' : 'update');
        $('#modal-title').text(isCreate ? 'Add New Record' : 'Edit Record');
        $('#crud-form')[0].reset();
        $('.btn-option').removeClass('active');
        $('.nice-dropdown').val('');
        $('.primary-key-field').closest('.form-group').toggle(isCreate ? false : true);

        if (!isCreate && rowData) {
            $('#form-id').val(rowData[window.appConfig.primaryKey]);
            window.appConfig.columns.forEach(column => {
                const input = $(`#${column}`);
                if (input.length) {
                    let value = rowData[column] || '';
                    if (window.appConfig.dateColumns.includes(column)) {
                        value = formatDateForInput(value);
                    }
                    input.val(value);
                    // Update dropdowns
                    const select = $(`#crud-form select.nice-dropdown[onchange*="document.getElementById('${column}')"]`);
                    if (select.length) {
                        select.val(value);
                    }
                    // Update button groups
                    const button = $(`#crud-form .form-group button[data-value="${value}"][onclick*="selectOption('${column}')"]`);
                    if (button.length) {
                        $(`#crud-form .form-group button[onclick*="selectOption('${column}')"]`).removeClass('active');
                        button.addClass('active');
                    }
                }
            });
        } else {
            $('#form-id').val('');
        }
        $('#crud-form input:visible:first').focus();
    };

    // Close CRUD modal
    window.closeModal = () => {
        $('#crud-modal').hide().attr('aria-hidden', 'true');
    };

    // Select option for button groups
    window.selectOption = (fieldId, value) => {
        $(`#${fieldId}`).val(value);
        const buttons = $(`#crud-form .form-group button[onclick*="selectOption('${fieldId}')"]`);
        buttons.removeClass('active');
        $(`#crud-form .form-group button[data-value="${value}"][onclick*="selectOption('${fieldId}')"]`).addClass('active');
    };

    // Open invoice modal
    window.openInvoiceModal = (jobId) => {
        $('#invoice-modal').show().attr('aria-hidden', 'false');
        $('#invoice-spinner').show();
        $('#invoice-details').hide();
        $.post(`${window.appConfig.basePath}/admin/manageTable/${window.appConfig.tableName}`, {
            action: 'get_invoice_details',
            job_id: jobId
        }, (data) => {
            $('#invoice-spinner').hide();
            $('#invoice-details').show();
            $('#invoice-no').text(data.invoice_no || '-');
            $('#invoice-date').text(formatDateForInput(data.invoice_date) || '-');
            $('#invoice-value').text(data.invoice_value || '-');
            $('#invoice-job').text(data.job_details || '-');
            $('#invoice-receiving').text(data.receiving_payment || '-');
            $('#invoice-received').text(data.received_amount || '-');
            $('#invoice-payment-date').text(formatDateForInput(data.payment_received_date) || '-');
            $('#invoice-remarks').text(data.remarks || '-');
            $('#invoice-details').find('span:visible:first').focus();
        }, 'json').fail((xhr, error) => {
            $('#invoice-spinner').hide();
            $('#invoice-details').show();
            alert(`Error loading invoice details: ${xhr.responseJSON?.error || error || 'Server error'}`);
            console.error('Invoice modal error:', error, xhr.responseText);
        });
    };

    // Close invoice modal
    window.closeInvoiceModal = () => {
        $('#invoice-modal').hide().attr('aria-hidden', 'true');
    };

    // Open confirmation modal
    window.openConfirmModal = (action, id = null) => {
        $('#confirm-modal').show().attr('aria-hidden', 'false');
        const isCreate = action === 'create';
        const isUpdate = action === 'update';
        $('#confirm-title').text(
            isCreate ? 'Confirm Add' :
            isUpdate ? 'Confirm Update' :
            'Confirm Delete'
        );
        $('#confirm-message').text(
            isCreate ? 'Are you sure you want to add this new record?' :
            isUpdate ? 'Are you sure you want to save changes to this record?' :
            'Are you sure you want to delete this record? This action cannot be undone.'
        );

        $('#confirm-action-btn').off('click').on('click', () => {
            if (isCreate || isUpdate) {
                const formData = $('#crud-form').serialize();
                $.ajax({
                    url: `${window.appConfig.basePath}/admin/manageTable/${window.appConfig.tableName}`,
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: (response) => {
                        if (response.success) {
                            closeConfirmModal();
                            closeModal();
                            table.ajax.reload(null, false);
                            alert(isCreate ? 'Record added successfully!' : 'Record updated successfully!');
                        } else {
                            alert(`Error ${isCreate ? 'adding' : 'updating'} record: ${response.error || 'Unknown error'}`);
                            console.error('CRUD error:', response.error);
                        }
                    },
                    error: (xhr, error) => {
                        alert(`Error ${isCreate ? 'adding' : 'updating'} record: ${xhr.responseJSON?.error || error || 'Server error'}`);
                        console.error('CRUD AJAX error:', error, xhr.responseText);
                    }
                });
            } else if (action === 'delete') {
                $.post(`${window.appConfig.basePath}/admin/manageTable/${window.appConfig.tableName}`, {
                    action: 'delete',
                    id: id
                }, (response) => {
                    if (response.success) {
                        closeConfirmModal();
                        table.ajax.reload(null, false);
                        alert('Record deleted successfully!');
                    } else {
                        alert(`Error deleting record: ${response.error || 'Unknown error'}`);
                        console.error('Delete error:', response.error);
                    }
                }, 'json').fail((xhr, error) => {
                    alert(`Error deleting record: ${xhr.responseJSON?.error || error || 'Server error'}`);
                    console.error('Delete AJAX error:', error, xhr.responseText);
                });
            }
        });
        $('#confirm-action-btn').focus();
    };

    // Close confirmation modal
    window.closeConfirmModal = () => {
        $('#confirm-modal').hide().attr('aria-hidden', 'true');
    };

    // Update job status
    const updateStatus = (jobId, newStatus) => {
        $.post(`${window.appConfig.basePath}/admin/manageTable/${window.appConfig.tableName}`, {
            action: 'update_status',
            job_id: jobId,
            completion: newStatus
        }, (response) => {
            if (response.success) {
                table.ajax.reload(null, false);
                alert('Status updated successfully!');
            } else {
                alert(`Error updating status: ${response.error || 'Unknown error'}`);
                console.error('Status update error:', response.error);
            }
        }, 'json').fail((xhr, error) => {
            alert(`Error updating status: ${xhr.responseJSON?.error || error || 'Server error'}`);
            console.error('Status update AJAX error:', error, xhr.responseText);
        });
    };

    // Cancel job
    const cancelJob = (jobId) => {
        openConfirmModal('cancel_job', jobId);
        $('#confirm-title').text('Confirm Cancel Job');
        $('#confirm-message').text('Are you sure you want to cancel this job? This action cannot be undone.');
        $('#confirm-action-btn').off('click').on('click', () => {
            $.post(`${window.appConfig.basePath}/admin/manageTable/${window.appConfig.tableName}`, {
                action: 'update_status',
                job_id: jobId,
                completion: '0.1'
            }, (response) => {
                if (response.success) {
                    closeConfirmModal();
                    table.ajax.reload(null, false);
                    alert('Job cancelled successfully!');
                } else {
                    alert(`Error cancelling job: ${response.error || 'Unknown error'}`);
                    console.error('Cancel job error:', response.error);
                }
            }, 'json').fail((xhr, error) => {
                alert(`Error cancelling job: ${xhr.responseJSON?.error || error || 'Server error'}`);
                console.error('Cancel job AJAX error:', error, xhr.responseText);
            });
        });
    };

    // Delete record
    const deleteRecord = (id) => {
        openConfirmModal('delete', id);
    };

    // Adjust table on window resize
    $(window).on('resize', () => {
        table.columns.adjust();
    });
});
