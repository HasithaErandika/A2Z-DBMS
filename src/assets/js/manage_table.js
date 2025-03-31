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
                d.searchTerm = d.search.value || '';
                d.sortColumn = window.appConfig.columns[d.order[0].column] || '';
                d.sortOrder = d.order[0].dir || 'desc';
                console.log('AJAX Request Data:', d);
                return d;
            },
            dataSrc: function(json) {
                console.log('AJAX Response:', json);
                if (!json || json.error) {
                    console.error('Error in response:', json.error || 'No data returned');
                    alert('Failed to load table data: ' + (json.error || 'Unknown error'));
                    return [];
                }
                if (!json.data || !Array.isArray(json.data)) {
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
                alert('Error loading data: ' + error + '. Check console for details.');
                $('#loading-spinner').hide();
            }
        },
        columns: [
            ...window.appConfig.columns.map(column => ({
                data: column,
                name: column,
                render: function(data, type, row, meta) {
                    if (data === null || data === undefined || data === '') {
                        return '-';
                    }
                    // Allow HTML rendering for server-side formatted columns
                    if (type === 'display' && (
                        column === 'presence' || 
                        column === 'emp_id' || 
                        column === 'project_id' || 
                        column === 'paid' || 
                        (column === 'completion' && window.appConfig.tableName !== 'jobs')
                    )) {
                        return data; // Render HTML from formatters like getPresenceDisplay, getBooleanIcon
                    }
                    return data; // Raw value for sorting/filtering
                },
                createdCell: function(td, cellData, rowData, row, col) {
                    // Ensure HTML is not escaped
                    if (cellData && (
                        column === 'presence' || 
                        column === 'emp_id' || 
                        column === 'project_id' || 
                        column === 'paid' || 
                        (column === 'completion' && window.appConfig.tableName !== 'jobs')
                    )) {
                        $(td).html(cellData);
                    }
                }
            })),
            ...(window.appConfig.tableName === 'jobs' ? [{
                data: 'completion',
                render: function(data, type, row) {
                    const completion = parseFloat(row.completion); // Use raw value
                    let statusClass = '';
                    let statusText = '';
                    if (completion === 0.00) {
                        statusClass = 'not-started';
                        statusText = 'Start Project';
                    } else if (completion === 50.00) {
                        statusClass = 'ongoing';
                        statusText = 'Mark as Completed';
                    } else if (completion === 100.00) {
                        statusClass = 'completed';
                        statusText = 'Reset Status';
                    } else {
                        statusClass = 'unknown';
                        statusText = 'Unknown Status';
                    }
                    return `<button class="btn status-btn ${statusClass}" onclick="updateStatus('${row.job_id}', this, ${completion})">${statusText}</button>`;
                },
                orderable: false,
                searchable: false,
                width: '150px'
            }] : []),
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary tooltip" onclick="openModal('edit', '${JSON.stringify(row).replace(/'/g, "\\'")}')" data-tooltip="Edit record"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger tooltip" onclick="deleteRecord('${row[window.appConfig.columns[0]]}')" data-tooltip="Delete record"><i class="fas fa-trash"></i></button>
                        </div>
                    `;
                },
                orderable: false,
                searchable: false,
                width: '120px'
            }
        ],
        columnDefs: [
            { targets: '_all', defaultContent: '-' } // Fallback for null/undefined
        ],
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[0, 'desc']],
        language: {
            processing: 'Loading data...',
            search: 'Search:',
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
        }
    });

    // Add custom styles for status buttons
    $('<style>')
        .text(`
            .status-btn { padding: 8px 16px; color: #fff; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; }
            .not-started { background: #EF4444; }
            .ongoing { background: #F59E0B; }
            .completed { background: #10B981; }
            .unknown { background: #6B7280; }
            .status-btn:hover { opacity: 0.9; transform: translateY(-1px); }
        `)
        .appendTo('head');
});

function openModal(action, record = null) {
    const modal = $('#crud-modal');
    const title = $('#modal-title');
    const form = $('#crud-form');
    const actionInput = $('#form-action');
    const idInput = $('#form-id');

    if (action === 'create') {
        title.text('Add New ' + window.appConfig.tableName + ' Record');
        actionInput.val('create');
        idInput.val('');
        form[0].reset();
    } else if (action === 'edit' && record) {
        title.text('Edit ' + window.appConfig.tableName + ' Record');
        actionInput.val('update');
        const data = JSON.parse(record);
        idInput.val(data[window.appConfig.columns[0]] || '');
        window.appConfig.editableFields.forEach(column => {
            const element = document.getElementById(column);
            if (element) {
                // Strip HTML from server-side formatting for form input
                element.value = data[column] ? data[column].replace(/<[^>]+>/g, '') : '';
            }
        });
    }
    modal.css('display', 'flex');
    setTimeout(() => modal.addClass('active'), 10);
}

function closeModal() {
    const modal = $('#crud-modal');
    modal.removeClass('active');
    setTimeout(() => modal.css('display', 'none'), 300);
}

function deleteRecord(id) {
    if (confirm('Are you sure you want to delete this record?')) {
        const form = $('<form>', {
            method: 'POST',
            action: `${window.appConfig.basePath}/admin/manageTable/${window.appConfig.tableName}`
        }).append(
            $('<input>', { type: 'hidden', name: 'action', value: 'delete' }),
            $('<input>', { type: 'hidden', name: 'id', value: id })
        );
        $('body').append(form);
        form.submit();
    }
}

function updateStatus(jobId, button, currentCompletion) {
    $.ajax({
        url: `${window.appConfig.basePath}/admin/manageTable/jobs`,
        type: 'POST',
        data: { action: 'update_status', job_id: jobId },
        dataType: 'json',
        success: function(response) {
            console.log('Status Update Response:', response);
            if (!response.success) {
                console.error('Status update failed:', response.error);
                alert('Failed to update status: ' + response.error);
                return;
            }
            const completion = parseFloat(response.completion);
            let statusClass = '';
            let statusText = '';
            if (completion === 0.00) {
                statusClass = 'not-started';
                statusText = 'Start Project';
            } else if (completion === 50.00) {
                statusClass = 'ongoing';
                statusText = 'Mark as Completed';
            } else if (completion === 100.00) {
                statusClass = 'completed';
                statusText = 'Reset Status';
            } else {
                statusClass = 'unknown';
                statusText = 'Unknown Status';
            }
            button.className = `btn status-btn ${statusClass}`;
            button.textContent = statusText;
            button.setAttribute('onclick', `updateStatus('${jobId}', this, ${completion})`);
            $('#data-table').DataTable().ajax.reload(null, false); // Refresh table without resetting pagination
        },
        error: function(xhr, status, error) {
            console.error('Status Update Error:', status, error, xhr.responseText);
            alert('Failed to update status: ' + error);
        }
    });
}

$('#crud-modal').on('click', function(e) {
    if (e.target === this) closeModal();
});