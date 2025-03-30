<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage <?php echo htmlspecialchars($data['table']); ?> - A2Z Engineering</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Reuse styles from dashboard.php with some additions */
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
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: var(--background);
            color: var(--text-dark);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: var(--primary);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.4rem;
            margin: 0;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: #006bb3;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #c53030;
        }

        .table-container {
            background: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 2px 10px var(--shadow);
            padding: 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--secondary);
        }

        th {
            background: var(--secondary);
            font-weight: 500;
        }

        tr:hover {
            background: #f1f5f8;
        }

        .actions {
            display: flex;
            gap: 10px;
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
        }

        .modal-content {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
        }

        .modal-content h2 {
            margin-top: 0;
            font-size: 1.2rem;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--secondary);
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage <?php echo htmlspecialchars($data['table']); ?></h1>
            <button class="btn btn-primary" onclick="openModal('create')">Add New</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <?php foreach ($data['columns'] as $column): ?>
                            <th><?php echo htmlspecialchars($column); ?></th>
                        <?php endforeach; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['records'] as $record): ?>
                        <tr>
                            <?php foreach ($data['columns'] as $column): ?>
                                <td><?php echo htmlspecialchars($record[$column] ?? ''); ?></td>
                            <?php endforeach; ?>
                            <td class="actions">
                                <button class="btn btn-primary" onclick="openModal('edit', '<?php echo htmlspecialchars(json_encode($record)); ?>')">Edit</button>
                                <button class="btn btn-danger" onclick="deleteRecord('<?php echo $record[$data['columns'][0]]; ?>')">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="crud-modal">
        <div class="modal-content">
            <h2 id="modal-title"></h2>
            <form id="crud-form" method="POST" action="<?php echo BASE_PATH; ?>/admin/manage_table.php?table=<?php echo htmlspecialchars($data['table']); ?>">
                <input type="hidden" name="action" id="form-action">
                <input type="hidden" name="id" id="form-id">
                <?php foreach ($data['columns'] as $column): ?>
                    <div class="form-group">
                        <label for="<?php echo $column; ?>"><?php echo htmlspecialchars($column); ?></label>
                        <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>">
                    </div>
                <?php endforeach; ?>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn" onclick="closeModal()">Cancel</button>
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
                title.textContent = 'Add New Record';
                actionInput.value = 'create';
                idInput.value = '';
                form.reset();
            } else if (action === 'edit' && record) {
                title.textContent = 'Edit Record';
                actionInput.value = 'update';
                const data = JSON.parse(record);
                idInput.value = data['<?php echo $data['columns'][0]; ?>'] || '';
                <?php foreach ($data['columns'] as $column): ?>
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
                form.action = '<?php echo BASE_PATH; ?>/admin/manage_table.php?table=<?php echo htmlspecialchars($data['table']); ?>';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>