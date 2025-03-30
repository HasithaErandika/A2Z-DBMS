<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage <?php echo htmlspecialchars($data['table']); ?> - A2Z Engineering</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1E40AF;
            --secondary: #6B7280;
            --background: #F9FAFB;
            --card-bg: #FFFFFF;
            --text-dark: #111827;
            --text-muted: #9CA3AF;
            --border: #E5E7EB;
            --shadow: rgba(0, 0, 0, 0.05);
            --shadow-hover: rgba(0, 0, 0, 0.1);
            --success: #10B981;
            --danger: #EF4444;
            --transition: all 0.2s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--background);
            color: var(--text-dark);
            line-height: 1.5;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            margin: 0;
            padding: 16px;
        }

        .header {
            background: var(--card-bg);
            color: var(--text-dark);
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 8px var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .header h1 {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 6px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 4px;
            background: var(--card-bg);
            color: var(--text-dark);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--card-bg);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: #1E3A8A;
            border-color: #1E3A8A;
            box-shadow: 0 2px 6px var(--shadow-hover);
        }

        .btn-danger {
            background: var(--danger);
            color: var(--card-bg);
            border-color: var(--danger);
        }

        .btn-danger:hover {
            background: #DC2626;
            border-color: #DC2626;
            box-shadow: 0 2px 6px var(--shadow-hover);
        }

        .btn-secondary:hover {
            background: var(--border);
        }

        .filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 0 20px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-bar {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            width: 240px;
            font-size: 0.85rem;
            transition: var(--transition);
        }

        .search-bar:focus {
            width: 280px;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(30, 64, 175, 0.2);
            outline: none;
        }

        .stats {
            display: flex;
            gap: 8px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .stats-box {
            background: var(--card-bg);
            padding: 4px 10px;
            border: 1px solid var(--border);
            border-radius: 4px;
        }

        .export-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .export-form input[type="date"] {
            padding: 6px 10px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 0.85rem;
        }

        .table-container {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 6px;
            box-shadow: 0 2px 8px var(--shadow);
            padding: 0;
            overflow-x: auto;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        th, td {
            padding: 10px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        th {
            background: var(--background);
            color: var(--text-dark);
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 1;
            cursor: pointer;
            user-select: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        th:hover {
            background: #F3F4F6;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr {
            transition: var(--transition);
        }

        tr:hover {
            background: #F9FAFB;
        }

        .actions {
            display: flex;
            gap: 6px;
        }

        .pagination {
            margin: 12px 20px 0;
            display: flex;
            justify-content: flex-end;
            gap: 6px;
        }

        .pagination a {
            padding: 6px 10px;
            text-decoration: none;
            color: var(--primary);
            border: 1px solid var(--border);
            border-radius: 4px;
            transition: var(--transition);
            font-size: 0.8rem;
        }

        .pagination a:hover {
            background: var(--border);
            border-color: var(--primary);
        }

        .pagination a.active {
            background: var(--primary);
            color: var(--card-bg);
            border-color: var(--primary);
            font-weight: 600;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: var(--card-bg);
            padding: 16px;
            border-radius: 6px;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 4px 16px var(--shadow-hover);
            animation: fadeIn 0.2s ease;
            border: 1px solid var(--border);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content h2 {
            margin: 0 0 12px;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: block;
            margin-bottom: 4px;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 0.85rem;
            transition: border-color 0.2s;
            background: var(--card-bg);
        }

        .form-group input:focus, .form-group select:focus {
            border-color: var(--primary);
            outline: none;
        }

        .green { color: var(--success); }
        .red { color: var(--danger); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-table"></i> Manage <?php echo htmlspecialchars($data['table']); ?></h1>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="openModal('create')"><i class="fas fa-plus"></i> Add</button>
                <button class="btn btn-secondary" onclick="window.location.href='<?php echo BASE_PATH; ?>/admin'"><i class="fas fa-arrow-left"></i> Back</button>
            </div>
        </div>

        <div class="filters">
            <input type="text" class="search-bar" id="search-bar" placeholder="Search <?php echo htmlspecialchars($data['table']); ?>..." onkeyup="searchTable()">
            <div class="stats">
                <?php if ($data['table'] === 'jobs' && $data['totalCapacity'] !== null): ?>
                    <span class="stats-box">Total Capacity: <?php echo number_format($data['totalCapacity'], 2); ?></span>
                <?php endif; ?>
                <span class="stats-box" id="record-count"><?php echo count($data['records']); ?> Records</span>
            </div>
            <form class="export-form" method="POST" action="<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>">
                <input type="hidden" name="action" value="export_csv">
                <input type="date" name="start_date" required>
                <input type="date" name="end_date" required>
                <button type="submit" class="btn btn-primary"><i class="fas fa-download"></i> Export CSV</button>
            </form>
        </div>

        <div class="table-container">
            <table id="data-table">
                <thead>
                    <tr>
                        <?php foreach ($data['columns'] as $column): ?>
                            <th onclick="sortTable('<?php echo $column; ?>')"><?php echo htmlspecialchars($column); ?> <i class="fas fa-sort"></i></th>
                        <?php endforeach; ?>
                        <?php if ($data['table'] === 'jobs'): ?>
                            <th>Invoice</th>
                        <?php endif; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <?php 
                    $tableManager = new TableManager();
                    foreach ($data['records'] as $record): ?>
                        <tr>
                            <?php 
                            $customDisplay = $data['config']['customDisplay'] ?? [];
                            $formatters = $data['config']['formatters'] ?? [];
                            foreach ($data['columns'] as $column): 
                                $value = $record[$column] ?? '';
                                if (isset($customDisplay[$column])) {
                                    $value = $tableManager->{$customDisplay[$column]}($record[$data['columns'][0]]);
                                } elseif (isset($formatters[$column])) {
                                    $value = $tableManager->{$formatters[$column]}($value);
                                } else {
                                    $value = htmlspecialchars($value);
                                }
                            ?>
                                <td><?php echo $value; ?></td>
                            <?php endforeach; ?>
                            <?php if ($data['table'] === 'jobs'): ?>
                                <td><?php echo $tableManager->checkInvoiceExists($record['job_id']) ? '<i class="fas fa-check green"></i>' : '<i class="fas fa-times red"></i>'; ?></td>
                            <?php endif; ?>
                            <td class="actions">
                                <button class="btn btn-primary" onclick="openModal('edit', '<?php echo htmlspecialchars(json_encode($record)); ?>')"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-danger" onclick="deleteRecord('<?php echo $record[$data['columns'][0]]; ?>')"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <?php 
            $totalPages = 5; // Replace with dynamic calculation later
            for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo urlencode($data['table']); ?>?page=<?php echo $i; ?>" class="<?php echo $i == $data['page'] ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <div class="modal" id="crud-modal">
        <div class="modal-content">
            <h2 id="modal-title"></h2>
            <form id="crud-form" method="POST" action="<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>">
                <input type="hidden" name="action" id="form-action">
                <input type="hidden" name="id" id="form-id">
                <?php 
                $editableFields = $data['config']['editableFields'] ?? $data['columns'];
                foreach ($editableFields as $column): ?>
                    <div class="form-group">
                        <label for="<?php echo $column; ?>"><?php echo htmlspecialchars($column); ?></label>
                        <?php if (in_array($column, ['presence', 'paid'])): ?>
                            <select name="<?php echo $column; ?>" id="<?php echo $column; ?>">
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                            </select>
                        <?php else: ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" <?php echo in_array($column, $data['config']['validation']['required'] ?? []) ? 'required' : ''; ?>>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="form-group" style="display: flex; gap: 6px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(action, record = null) {
            const modal = document.getElementById('crud-modal');
            const title = document.getElementById('modal-title');
            const form = document.getElementById('crud-form');
            const actionInput = document.getElementById('form-action');
            const idInput = document.getElementById('form-id');

            if (action === 'create') {
                title.textContent = 'Add New <?php echo htmlspecialchars($data['table']); ?>';
                actionInput.value = 'create';
                idInput.value = '';
                form.reset();
            } else if (action === 'edit' && record) {
                title.textContent = 'Edit <?php echo htmlspecialchars($data['table']); ?>';
                actionInput.value = 'update';
                const data = JSON.parse(record);
                idInput.value = data['<?php echo $data['columns'][0]; ?>'] || '';
                <?php foreach ($editableFields as $column): ?>
                    document.getElementById('<?php echo $column; ?>').value = data['<?php echo $column; ?>'] || '';
                <?php endforeach; ?>
            }
            modal.style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('crud-modal').style.display = 'none';
        }

        function deleteRecord(id) {
            if (confirm('Are you sure you want to delete this record?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function searchTable() {
            const searchTerm = document.getElementById('search-bar').value;
            fetch('<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=search&search_term=${encodeURIComponent(searchTerm)}`
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('table-body');
                tbody.innerHTML = '';
                data.forEach(record => {
                    const row = document.createElement('tr');
                    <?php foreach ($data['columns'] as $column): ?>
                        const <?php echo $column; ?>Cell = document.createElement('td');
                        <?php if (isset($data['config']['customDisplay'][$column])): ?>
                            <?php echo $column; ?>Cell.innerHTML = record['<?php echo $column; ?>'] || '';
                        <?php elseif (isset($data['config']['formatters'][$column])): ?>
                            <?php echo $column; ?>Cell.innerHTML = record['<?php echo $column; ?>'] || '';
                        <?php else: ?>
                            <?php echo $column; ?>Cell.textContent = record['<?php echo $column; ?>'] || '';
                        <?php endif; ?>
                        row.appendChild(<?php echo $column; ?>Cell);
                    <?php endforeach; ?>
                    <?php if ($data['table'] === 'jobs'): ?>
                        const invoiceCell = document.createElement('td');
                        invoiceCell.innerHTML = record['invoice_exists'] ? '<i class="fas fa-check green"></i>' : '<i class="fas fa-times red"></i>';
                        row.appendChild(invoiceCell);
                    <?php endif; ?>
                    const actionsCell = document.createElement('td');
                    actionsCell.className = 'actions';
                    actionsCell.innerHTML = `
                        <button class="btn btn-primary" onclick="openModal('edit', '${JSON.stringify(record).replace(/'/g, "\\'")}')"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger" onclick="deleteRecord('${record['<?php echo $data['columns'][0]; ?>']}')"><i class="fas fa-trash"></i></button>
                    `;
                    row.appendChild(actionsCell);
                    tbody.appendChild(row);
                });
                document.getElementById('record-count').textContent = `${data.length} Records`;
            })
            .catch(error => console.error('Error:', error));
        }

        let sortDirection = {};
        function sortTable(column) {
            const tbody = document.getElementById('table-body');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const index = Array.from(document.querySelectorAll('th')).findIndex(th => th.textContent.includes(column));
            sortDirection[column] = !sortDirection[column];

            rows.sort((a, b) => {
                const aValue = a.cells[index].textContent.trim();
                const bValue = b.cells[index].textContent.trim();
                return sortDirection[column] ? 
                    aValue.localeCompare(bValue, undefined, { numeric: true }) : 
                    bValue.localeCompare(aValue, undefined, { numeric: true });
            });

            rows.forEach(row => tbody.appendChild(row));
        }
    </script>
</body>
</html>