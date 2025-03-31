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
    <style>
        :root {
            --primary: #1E40AF;
            --secondary: #6B7280;
            --background: #F9FAFB;
            --card-bg: #FFFFFF;
            --text-dark: #111827;
            --text-muted: #6B7280;
            --border: #E5E7EB;
            --shadow: rgba(0, 0, 0, 0.1);
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --gradient: linear-gradient(135deg, #1E40AF, #3B82F6);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--background);
            color: var(--text-dark);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: var(--card-bg);
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            position: sticky;
            top: 0;
            z-index: 10;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--gradient);
            -webkit-background-clip: text;
            color: transparent;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.4s ease, height 0.4s ease;
        }

        .btn:hover::after {
            width: 200%;
            height: 200%;
        }

        .btn-primary {
            background: var(--gradient);
            color: #fff;
        }

        .btn-primary:hover {
            box-shadow: 0 6px 16px rgba(30, 64, 175, 0.3);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .btn-danger:hover {
            background: #DC2626;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--secondary);
            color: #fff;
        }

        .btn-secondary:hover {
            background: #4B5563;
            transform: translateY(-2px);
        }

        .filters {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
            background: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px var(--shadow);
        }

        @media (max-width: 768px) {
            .filters {
                grid-template-columns: 1fr;
            }
        }

        .stats-box {
            background: #F3F4F6;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .export-form {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .export-form input[type="date"] {
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .export-form input[type="date"]:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.2);
            outline: none;
        }

        .table-container {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 4px 12px var(--shadow);
            padding: 20px;
            overflow-x: auto;
            animation: fadeIn 0.5s ease;
            min-height: 300px; /* Ensure visibility even if empty */
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        #data-table {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0 8px;
            min-width: max-content;
        }

        #data-table th, #data-table td {
            padding: 14px 20px;
            text-align: left;
            font-size: 0.95rem;
            white-space: nowrap;
        }

        #data-table th {
            background: var(--gradient);
            color: #fff;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 5;
        }

        #data-table td {
            background: #fff;
            border-bottom: 1px solid var(--border);
            transition: background 0.2s ease;
        }

        #data-table tr:hover td {
            background: #F9FAFB;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.2);
            outline: none;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            width: 700px;
            max-width: 90%;
            box-shadow: 0 8px 32px var(--shadow);
            max-height: 85vh;
            overflow-y: auto;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }

        .modal.active .modal-content {
            transform: scale(1);
        }

        .modal-content h2 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 25px;
            background: var(--gradient);
            -webkit-background-clip: text;
            color: transparent;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            color: var(--text-dark);
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .form-group input:focus, .form-group select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.2);
            outline: none;
        }

        .spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner i {
            font-size: 2rem;
            color: var(--primary);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        .green { color: var(--success); }
        .red { color: var(--danger); }
        .yellow { color: var(--warning); }

        .tooltip {
            position: relative;
        }

        .tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #111827;
            color: #fff;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease;
        }

        .tooltip:hover::after {
            opacity: 1;
            visibility: visible;
        }

        @media (max-width: 600px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .header-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                justify-content: center;
            }

            .btn {
                padding: 10px 18px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-table"></i> Manage <?php echo htmlspecialchars($data['table']); ?></h1>
            <div class="header-actions">
                <button class="btn btn-primary tooltip" onclick="openModal('create')" data-tooltip="Add a new record"><i class="fas fa-plus"></i> Add Record</button>
                <button class="btn btn-secondary tooltip" onclick="window.location.href='<?php echo BASE_PATH; ?>/admin'" data-tooltip="Return to dashboard"><i class="fas fa-arrow-left"></i> Back to Dashboard</button>
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
                                <td>-</td> <!-- Placeholder for actions, overridden by DataTables -->
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

    <div class="modal" id="crud-modal">
        <div class="modal-content">
            <h2 id="modal-title" aria-live="polite"></h2>
            <form id="crud-form" method="POST" action="<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>">
                <input type="hidden" name="action" id="form-action">
                <input type="hidden" name="id" id="form-id">
                <?php 
                $editableFields = $data['config']['editableFields'] ?? $data['columns'];
                foreach ($editableFields as $column): ?>
                    <div class="form-group">
                        <label for="<?php echo $column; ?>"><?php echo htmlspecialchars($column); ?></label>
                        <?php if ($column === 'emp_id' && $data['table'] !== 'employees'): ?>
                            <select name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Employee">
                                <option value="">Select Employee</option>
                                <?php
                                $tableManager = new TableManager();
                                $employees = $tableManager->getRecords('employees');
                                foreach ($employees as $employee) {
                                    echo "<option value='{$employee['emp_id']}'>" . htmlspecialchars($employee['emp_name']) . "</option>";
                                }
                                ?>
                            </select>
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
                        <?php elseif ($column === 'date_started' || $column === 'date_completed'): ?>
                            <input type="date" name="<?php echo $column; ?>" id="<?php echo $column; ?>" <?php echo in_array($column, $data['config']['validation']['required'] ?? []) ? 'required' : ''; ?> aria-label="<?php echo htmlspecialchars($column); ?>">
                        <?php elseif ($column === 'completion'): ?>
                            <input type="number" step="0.01" name="<?php echo $column; ?>" id="<?php echo $column; ?>" readonly aria-label="<?php echo htmlspecialchars($column); ?>">
                        <?php else: ?>
                            <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" <?php echo in_array($column, $data['config']['validation']['required'] ?? []) ? 'required' : ''; ?> aria-label="<?php echo htmlspecialchars($column); ?>">
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

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        window.appConfig = {
            basePath: '<?php echo BASE_PATH; ?>',
            tableName: '<?php echo htmlspecialchars($data['table']); ?>',
            columns: <?php echo json_encode($data['columns']); ?>,
            editableFields: <?php echo json_encode($editableFields); ?>,
            completionIndex: <?php echo json_encode(array_search('completion', $data['columns']) !== false ? array_search('completion', $data['columns']) : -1); ?>
        };
        console.log('App Config:', window.appConfig);
    </script>
    <script src="<?php echo BASE_PATH; ?>/public/js/manage_table.js"></script>
</body>
</html>