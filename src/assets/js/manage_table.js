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
    // Spinner and modal initialization
    $('#loading-spinner').show();
    $('#data-table').hide();
    $('#crud-modal').hide();
    $('#invoice-modal').hide();
    $('#confirm-modal').hide();

    // DataTables initialization
    const columns = window.appConfig.columns;
    const dateColumns = window.appConfig.dateColumns;
    const primaryKey = window.appConfig.primaryKey;
    const tableName = window.appConfig.tableName;
    const basePath = window.appConfig.basePath;

    // Build columnDefs for DataTables
    let columnDefs = columns.map(col => ({
        data: col,
        title: col,
        name: col
    }));
    if (tableName === 'jobs') {
        columnDefs.push({
            data: 'completion',
            title: 'Status',
            name: 'completion',
            render: function(data, type, row) {
                return '<select class="status-select" data-id="' + row[primaryKey] + '">' +
                    '<option value="0.0" ' + (data == '0.0' ? 'selected' : '') + '>Not Started</option>' +
                    '<option value="0.1" ' + (data == '0.1' ? 'selected' : '') + '>Cancelled</option>' +
                    '<option value="0.2" ' + (data == '0.2' ? 'selected' : '') + '>Started</option>' +
                    '<option value="0.5" ' + (data == '0.5' ? 'selected' : '') + '>Ongoing</option>' +
                    '<option value="1.0" ' + (data == '1.0' ? 'selected' : '') + '>Completed</option>' +
                    '</select>';
            }
        });
    }
    columnDefs.push({
        data: null,
        title: 'Actions',
        name: 'actions',
        render: function(data, type, row) {
            let buttons = '<button class="btn btn-primary btn-sm edit-btn" data-id="' + row[primaryKey] + '"><i class="fas fa-edit"></i></button>' +
                          '<button class="btn btn-danger btn-sm delete-btn" data-id="' + row[primaryKey] + '"><i class="fas fa-trash"></i></button>';
            if (tableName === 'jobs' && row.has_invoice) {
                buttons += '<button class="btn btn-info btn-sm invoice-btn" data-id="' + row[primaryKey] + '"><i class="fas fa-file-invoice"></i></button>';
            }
            return buttons;
        }
    });

    let table = $('#data-table').DataTable({
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
            url: `${basePath}/admin/manageTable/${tableName}`,
            type: 'POST',
            data: function(d) {
                var searchValue = $('#searchInput').val();
                var isDate = /^\d{4}-\d{2}-\d{2}$/.test(searchValue);
                d.action = 'get_records';
                d.search = { value: searchValue, isDate: isDate };
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

    // Modal and CRUD logic
    window.openModal = function(action, id = null, data = null) {
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
            columns.forEach(function(column) {
                let value = data[column] || '';
                if (dateColumns.includes(column) && value) {
                    if (value && !/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                        try {
                            let date = new Date(value);
                            value = date.toISOString().split('T')[0];
                        } catch (e) {
                            value = '';
                        }
                    }
                }
                $('#' + column).val(value);
                // Update dropdowns and button groups
                var select = document.querySelector(`#crud-form select.nice-dropdown[onchange*="document.getElementById('${column}')"]`);
                if (select) select.value = value;
                var button = document.querySelector(`#crud-form .form-group button[data-value="${value}"][onclick*="selectOption('${column}')"]`);
                if (button) {
                    document.querySelectorAll(`#crud-form .form-group button[onclick*="selectOption('${column}')"]`).forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                }
            });
        }
    };

    window.closeModal = function() {
        $('#crud-modal').hide();
        $('.primary-key-field').closest('.form-group').show();
    };

    window.selectOption = function(fieldId, value) {
        document.getElementById(fieldId).value = value;
        const buttons = document.querySelectorAll(`#crud-form .form-group button[data-value][onclick*="${fieldId}"]`);
        buttons.forEach(btn => btn.classList.remove('active'));
        const selectedButton = document.querySelector(`#crud-form .form-group button[data-value="${value}"][onclick*="${fieldId}"]`);
        if (selectedButton) selectedButton.classList.add('active');
    };

    window.openInvoiceModal = function(jobId) {
        $('#invoice-modal').show();
        $('#invoice-spinner').show();
        $('#invoice-details').hide();
        $.post(`${basePath}/admin/manageTable/${tableName}`, {
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
    };

    window.closeInvoiceModal = function() {
        $('#invoice-modal').hide();
    };

    window.openConfirmModal = function(action, id = null) {
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
                $.ajax({
                    url: `${basePath}/admin/manageTable/${tableName}`,
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            window.closeConfirmModal();
                            window.closeModal();
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
                $.post(`${basePath}/admin/manageTable/${tableName}`, {
                    action: 'delete',
                    id: id
                }, function(response) {
                    if (response.success) {
                        window.closeConfirmModal();
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
    };

    window.closeConfirmModal = function() {
        $('#confirm-modal').hide();
    };

    // Event delegation for edit, delete, status, invoice
    $('#data-table tbody').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        var rowData = table.row($(this).closest('tr')).data();
        if (rowData) {
            window.openModal('update', id, rowData);
        }
    });
    $('#data-table tbody').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        window.openConfirmModal('delete', id);
    });
    $('#data-table tbody').on('change', '.status-select', function() {
        var id = $(this).data('id');
        var newStatus = $(this).val();
        $.post(`${basePath}/admin/manageTable/${tableName}`, {
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
    $('#data-table tbody').on('click', '.invoice-btn', function() {
        var id = $(this).data('id');
        window.openInvoiceModal(id);
    });
    $(window).on('resize', function() {
        table.columns.adjust();
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

$('#crud-modal').on('click', function(e) {
    if (e.target === this) closeModal();
});

$('#invoice-modal').on('click', function(e) {
    if (e.target === this) closeInvoiceModal();
});