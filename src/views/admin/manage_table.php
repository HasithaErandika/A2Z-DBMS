<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage <?php echo htmlspecialchars($data['table']); ?> - A2Z Engineering</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #005f99;
            --secondary: #e6ecf0;
            --accent: #007acc;
            --background: #f7f9fc;
            --card-bg: #ffffff;
            --text-dark: #2d3748;
            --text-muted: #718096;
            --shadow: rgba(0, 0, 0, 0.05);
            --success: #38a169;
            --danger: #e53e3e;
            --warning: #f6ad55;
            --border-radius: 8px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: var(--background);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .container {
            max-width: 1400px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .header {
            background: var(--primary);
            color: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 10px var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 1.6rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: #006bb3;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #c53030;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--secondary);
            color: var(--text-dark);
        }

        .btn-secondary:hover {
            background: #d1dbe3;
        }

        .filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 20px;
        }

        .search-bar {
            padding: 10px 15px;
            border: 1px solid var(--secondary);
            border-radius: 6px;
            width: 300px;
            font-size: 0.9rem;
            transition: width 0.3s ease, border-color 0.2s;
        }

        .search-bar:focus {
            width: 350px;
            border-color: var(--accent);
            outline: none;
        }

        .stats {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .table-container {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: 0 2px 10px var(--shadow);
            padding: 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.9rem;
        }

        th, td {
            padding: 14px 20px;
            text-align: left;
            border-bottom: 1px solid var(--secondary);
        }

        th {
            background: var(--secondary);
            font-weight: 500;
            position: sticky;
            top: 0;
            z-index: 1;
            cursor: pointer;
            user-select: none;
        }

        th:hover {
            background: #d1dbe3;
        }

        tr {
            transition: background 0.2s;
        }

        tr:hover {
            background: #f1f5f8;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .pagination a {
            padding: 8px 14px;
            text-decoration: none;
            color: var(--accent);
            border: 1px solid var(--secondary);
            border-radius: 6px;
            transition: var(--transition);
        }

        .pagination a:hover {
            background: var(--secondary);
        }

        .pagination a.active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: var(--card-bg);
            padding: 25px;
            border-radius: var(--border-radius);
            width: 500px;
            max-width: 90%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-content h2 {
            margin: 0 0 20px;
            font-size: 1.4rem;
            color: var(--text-dark);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--secondary);
            border-radius: 6px;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus, .form-group select:focus {
            border-color: var(--accent);
            outline: none;
        }

        .green { color: var(--success); }
        .red { color: var(--danger); }
        .stats-box {
            background: var(--secondary);
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-table"></i> Manage <?php echo htmlspecialchars($data['table']); ?></h1>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="openModal('create')"><i class="fas fa-plus"></i> Add New</button>
                <button class="btn btn-secondary" onclick="window.location.href='<?php echo BASE_PATH; ?>/admin'"><i class="fas fa-arrow-left"></i> Back</button>
            </div>
        </div>

        <div class="filters">
            <input type="text" class="search-bar" id="search-bar" placeholder="Search <?php echo htmlspecialchars($data['table']); ?>..." onkeyup="filterTable()">
            <div class="stats">
                <?php if ($data['table'] === 'jobs' && $data['totalCapacity'] !== null): ?>
                    <span class="stats-box">Total Job Capacity: <?php echo number_format($data['totalCapacity'], 2); ?></span>
                <?php endif; ?>
                <span class="stats-box">Showing <?php echo count($data['records']); ?> of many records</span>
            </div>
        </div>

        <div class="table-container">
            <table id="data-table">
                <thead>
                    <tr>
                        <?php foreach ($data['columns'] as $column): ?>
                            <th onclick="sortTable('<?php echo $column; ?>')"><?php echo htmlspecialchars($column); ?> <i class="fas fa-sort"></i></th>
                        <?php endforeach; ?>
                        <?php if ($data['table'] === 'jobs'): ?>
                            <th>Invoice Exists</th>
                        <?php endif; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
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
                <div class="form-group">
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
                title.textContent = 'Add New <?php echo htmlspecialchars($data['table']); ?> Record';
                actionInput.value = 'create';
                idInput.value = '';
                form.reset();
            } else if (action === 'edit' && record) {
                title.textContent = 'Edit <?php echo htmlspecialchars($data['table']); ?> Record';
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

        function filterTable() {
            const search = document.getElementById('search-bar').value.toLowerCase();
            const rows = document.querySelectorAll('#data-table tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(search) ? '' : 'none';
            });
        }

        let sortDirection = {};
        function sortTable(column) {
            const tbody = document.querySelector('#data-table tbody');
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