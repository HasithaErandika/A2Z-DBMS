<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage <?php echo htmlspecialchars($data['table']); ?> - A2Z Engineering</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #3b82f6, #8b5cf6);
            --secondary-gradient: linear-gradient(135deg, #10b981, #3b82f6);
            --accent-gradient: linear-gradient(135deg, #f59e0b, #ef4444);
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .modal.show {
            opacity: 1;
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: translateY(-50px);
            transition: transform 0.3s ease, opacity 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
            opacity: 0;
        }
        .modal.show .modal-content {
            transform: translateY(0);
            opacity: 1;
        }
        .btn {
            transition: all 0.2s ease-in-out;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            margin: 0 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .btn:active {
            transform: translateY(0);
        }
        .tooltip {
            position: relative;
        }
        .tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #1f2937;
            color: white;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s;
            z-index: 1000;
        }
        .tooltip:hover::after {
            opacity: 1;
            visibility: visible;
        }
        .status-select {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background-color: white;
            font-size: 14px;
            width: 100%;
            transition: all 0.2s ease;
        }
        .status-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .table-wrapper {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            background: white;
        }
        #data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        #data-table th,
        #data-table td {
            padding: 16px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        #data-table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        #data-table tr:hover {
            background-color: #f9fafb;
        }
        .btn-option.active {
            box-shadow: 0 0 0 2px #3b82f6;
        }
        .form-group {
            margin-bottom: 24px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 15px;
            transition: all 0.2s ease;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .primary-key-field {
            background-color: #f3f4f6;
            cursor: not-allowed;
        }
        .button-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn-option {
            flex: 1;
            min-width: 100px;
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background-color: white;
            font-size: 14px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }
        .btn-option:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .btn-green {
            background-color: #10b981;
            color: white;
            border-color: #059669;
        }
        .btn-yellow {
            background-color: #f59e0b;
            color: white;
            border-color: #d97706;
        }
        .btn-red {
            background-color: #ef4444;
            color: white;
            border-color: #dc2626;
        }
        .btn-blue {
            background-color: #3b82f6;
            color: white;
            border-color: #2563eb;
        }
        .btn-orange {
            background-color: #f97316;
            color: white;
            border-color: #ea580c;
        }
        .btn-purple {
            background-color: #8b5cf6;
            color: white;
            border-color: #7c3aed;
        }
        .invoice-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        .invoice-item {
            padding: 16px;
            background-color: #f9fafb;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .invoice-item.full-width {
            grid-column: 1 / -1;
        }
        .label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            font-size: 14px;
        }
        .stat-box {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            background: var(--secondary-gradient);
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .search-input {
            width: 100%;
            padding: 14px 20px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            font-size: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .search-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }
        .custom-design-element {
            position: relative;
        }
        .custom-design-element::before {
            content: '';
            position: absolute;
            top: -10px;
            right: -10px;
            width: 28px;
            height: 28px;
            background: var(--primary-gradient);
            border-radius: 50%;
            z-index: 1;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .custom-design-element::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: -10px;
            width: 20px;
            height: 20px;
            background: var(--secondary-gradient);
            border-radius: 50%;
            z-index: 1;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.6rem 0.9rem;
            margin-left: 0.3rem;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            background-color: #fff;
            color: #374151 !important;
            transition: all 0.2s;
            font-weight: 500;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #f3f4f6;
            color: #1f2937 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-gradient);
            color: #fff !important;
            border-color: transparent;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            pointer-events: none;
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            padding: 0.3rem 0.6rem;
            background-color: #fff;
            color: #374151;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            padding: 0.3rem 0.6rem;
        }
        .dataTables_info {
            color: #6b7280;
            font-size: 0.9rem;
        }
        .clock-picker {
            position: relative;
        }
        .clock-picker .clock-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }
        .entries-control {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .entries-control select {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            padding: 0.3rem 0.6rem;
            background-color: #fff;
            color: #374151;
        }
        .action-btn {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 0 4px;
        }
        .action-btn.edit {
            background-color: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .action-btn.edit:hover {
            background-color: #bfdbfe;
        }
        .action-btn.delete {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .action-btn.delete:hover {
            background-color: #fecaca;
        }
        .action-btn.invoice {
            background-color: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        .action-btn.invoice:hover {
            background-color: #bbf7d0;
        }
        .action-btn.status-change {
            background-color: #dbeafe;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }
        .action-btn.status-change:hover {
            background-color: #bfdbfe;
        }
        .status-option-btn.selected {
            border-color: #3b82f6;
            background-color: #dbeafe;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }
        .status-not-started {
            background-color: #e5e7eb;
            color: #374151;
        }
        .status-cancelled {
            background-color: #fecaca;
            color: #dc2626;
        }
        .status-started {
            background-color: #fef3c7;
            color: #d97706;
        }
        .status-ongoing {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        .status-completed {
            background-color: #d1fae5;
            color: #059669;
        }
        .status-postponed {
            background-color: #ddd6fe;
            color: #7c3aed;
        }
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .form-actions {
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            margin-top: 16px;
        }
        .paid-cell {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
        }
        .paid-cell.pending-status {
            gap: 8px;
            flex-wrap: wrap;
        }
        .paid-cell.paid-status,
        .paid-cell.unpaid-status {
            justify-content: center;
        }
        .btn-paid-action,
        .btn-unpaid-action {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
            background-color: white;
            min-width: fit-content;
            margin-right: 6px;
        }
        .btn-paid-action {
            color: #10b981;
            border-color: #10b981;
        }
        .btn-paid-action:hover {
            background-color: #ecfdf5;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
            transform: translateY(-1px);
        }
        .btn-paid-action:active {
            background-color: #d1fae5;
        }
        .btn-unpaid-action {
            color: #ef4444;
            border-color: #ef4444;
        }
        .btn-unpaid-action:hover {
            background-color: #fef2f2;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
            transform: translateY(-1px);
        }
        .btn-unpaid-action:active {
            background-color: #fee2e2;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="bg-white shadow-xl">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between py-6 gap-6">
                <div class="flex items-center space-x-5">
                    <div class="custom-design-element">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-14 h-14 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-table text-white text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Manage <?php echo htmlspecialchars($data['table']); ?></h1>
                        <p class="text-base text-gray-600 mt-1">Internal Database Management System</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button class="btn bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700 shadow-lg hover:shadow-xl" onclick="openModal('create')">
                        <i class="fas fa-plus"></i> Add Record
                    </button>
                    <button class="btn bg-white border-2 border-gray-200 text-gray-700 hover:bg-gray-50 shadow hover:shadow-md" onclick="window.location.href='<?php echo BASE_PATH; ?>/admin/tables'">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full">
        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php if ($data['table'] === 'jobs' && isset($data['totalCapacity'])): ?>
                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white mr-4">
                            <i class="fas fa-bolt text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">PV Capacity</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo number_format($data['totalCapacity'], 2); ?> kW</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($data['table'] === 'employees'): ?>
                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white mr-4">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Employees</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $data['activeEmployees'] ?? 'N/A'; ?></p>
                        </div>
                    </div>
                </div>
            <?php elseif ($data['table'] === 'attendance'): ?>
                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 text-white mr-4">
                            <i class="fas fa-calendar-check text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Today's Attendance</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $data['todaysAttendance'] ?? 'N/A'; ?></p>
                        </div>
                    </div>
                </div>
            <?php elseif ($data['table'] === 'operational_expenses'): ?>
                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-gradient-to-r from-red-500 to-orange-600 text-white mr-4">
                            <i class="fas fa-money-bill-wave text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">This Month Expenses</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $data['monthlyExpenses'] ?? 'N/A'; ?></p>
                        </div>
                    </div>
                </div>
            <?php elseif ($data['table'] === 'employee_payments'): ?>
                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-gradient-to-r from-purple-500 to-pink-600 text-white mr-4">
                            <i class="fas fa-rupee-sign text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending Payments</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $data['pendingPayments'] ?? 'N/A'; ?></p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white mr-4">
                            <i class="fas fa-database text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Records</p>
                            <p class="text-2xl font-bold text-gray-900" id="record-count"><?php echo $data['totalRecords']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- Filters Section -->
        <div class="card p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input class="search-input sm:w-96 pl-10 pr-4 py-3" id="searchInput" type="text" placeholder="Search table (e.g., '2025-08-04 Meals 2310')" aria-label="Search table data">
                    </div>
                </div>
                <form class="flex flex-col sm:flex-row gap-4 export-form" method="POST" action="<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>">
                    <input type="hidden" name="action" value="export_csv">
                    <div class="flex gap-3">
                        <input type="date" name="start_date" required aria-label="Start Date" class="px-4 py-3 border border-gray-300 rounded-xl text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="YYYY-MM-DD">
                        <input type="date" name="end_date" required aria-label="End Date" class="px-4 py-3 border border-gray-300 rounded-xl text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="YYYY-MM-DD">
                    </div>
                    <button type="submit" class="btn bg-gradient-to-r from-green-500 to-emerald-600 text-white hover:from-green-600 hover:to-emerald-700 shadow-md hover:shadow-lg">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </form>
            </div>
        </div>
        <!-- Pagination Info Section -->
        <div class="card p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex items-center gap-2">
                        <label for="entries-per-page" class="text-gray-700 font-medium">Show</label>
                        <select id="entries-per-page" class="border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-gray-700 font-medium">entries</span>
                    </div>
                    <div class="dataTables_info text-gray-600" id="pagination-info">
                        Showing 1 to 10 of <?php echo $data['totalRecords']; ?> entries
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button id="refresh-table" class="btn bg-gradient-to-r from-cyan-500 to-blue-600 text-white hover:from-cyan-600 hover:to-blue-700">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
        <!-- Table Section -->
        <div class="card overflow-hidden w-full">
            <div class="overflow-x-auto">
                <table id="data-table" class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <?php foreach ($data['columns'] as $column): ?>
                                <?php if ($data['table'] === 'jobs' && $column === 'status') continue; ?>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap"><?php echo htmlspecialchars($column); ?></th>
                            <?php endforeach; ?>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
                <div class="spinner" id="loading-spinner"><i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i></div>
            </div>
        </div>
    </div>
    <!-- CRUD Modal -->
    <div class="modal" id="crud-modal">
        <div class="modal-content">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 rounded-t-xl">
                <h2 id="modal-title" class="text-2xl font-bold text-white text-center" aria-live="polite"></h2>
            </div>
            <div class="p-6">
                <form id="crud-form" method="POST" action="<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>">
                    <input type="hidden" name="action" id="form-action">
                    <input type="hidden" name="id" id="form-id">
                    <?php
                    $primaryKeys = [
                        'employees' => 'emp_id', 'employee_payment_rates' => 'rate_id', 'attendance' => 'attendance_id',
                        'salary_increments' => 'increment_id', 'employee_payments' => 'payment_id', 'invoice_data' => 'invoice_id',
                        'operational_expenses' => 'expense_id', 'projects' => 'project_id', 'employee_bank_details' => 'id',
                        'jobs' => 'job_id', 'cash_hand' => 'cash_id', 'maintenance_schedule' => 'schedule_id'
                    ];
                    $primaryKey = $primaryKeys[$data['table']] ?? $data['columns'][0];
                    $dateColumns = ['date_started', 'date_completed', 'date', 'attendance_date', 'date_of_joined', 'date_of_resigned', 'date_of_birth', 'effective_date', 'end_date', 'expensed_date', 'invoice_date', 'payment_date', 'increment_date', 'txn_date', 'payment_received_date'];
                    $timeColumns = ['start_time', 'end_time'];
                    foreach ($data['columns'] as $column):
                        if ($column === 'completion') continue;
                        if ($data['table'] === 'jobs' && $column === 'status') continue;
                        ?>
                        <div class="form-group">
                            <label for="<?php echo $column; ?>" class="block text-sm font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($column); ?></label>
                            <?php if ($column === $primaryKey): ?>
                                <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" readonly aria-label="<?php echo htmlspecialchars($column); ?>" class="primary-key-field">
                            <?php elseif (($column === 'emp_id' && $data['table'] !== 'employees') || ($data['table'] === 'cash_hand' && in_array($column, ['given_by', 'received_by']))): ?>
                                <select class="select2-dropdown" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Select Employee">
                                    <option value="">Select Employee</option>
                                    <?php echo $data['tableManager']->getEmployeeOptions(); ?>
                                </select>
                            <?php elseif ($column === 'job_id' && $data['table'] !== 'jobs'): ?>
                                <select class="select2-dropdown" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Select Job">
                                    <option value="">Select Job</option>
                                    <?php echo $data['tableManager']->getJobDetails(); ?>
                                </select>
                            <?php elseif ($column === 'project_id' && $data['table'] !== 'projects'): ?>
                                <select class="select2-dropdown" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Select Project">
                                    <option value="">Select Project</option>
                                    <?php echo $data['tableManager']->getProjectDetailsForJobs(); ?>
                                </select>
                            <?php elseif ($column === 'project_id' && $data['table'] === 'jobs'): ?>
                                <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Project ID">
                            <?php elseif ($column === 'expenses_category' && $data['table'] === 'operational_expenses'): ?>
                                <select class="select2-dropdown" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="Expenses Category">
                                    <option value="">Select Category</option>
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
                            <?php elseif ($column === 'presence'): ?>
                                <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>" class="hidden">
                                <div class="button-group">
                                    <button type="button" class="btn-option btn-green" data-value="1.0" onclick="selectOption('<?php echo $column; ?>', '1.0')">Full Day</button>
                                    <button type="button" class="btn-option btn-yellow" data-value="0.5" onclick="selectOption('<?php echo $column; ?>', '0.5')">Half Day</button>
                                    <button type="button" class="btn-option btn-red" data-value="0.0" onclick="selectOption('<?php echo $column; ?>', '0.0')">Not Attended</button>
                                </div>
                            <?php elseif ($column === 'paid'): ?>
                                <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>" class="hidden">
                                <div class="button-group">
                                    <button type="button" class="btn-option btn-green" data-value="1" onclick="selectOption('<?php echo $column; ?>', '1')"><i class="fas fa-check mr-1"></i>Yes</button>
                                    <button type="button" class="btn-option btn-red" data-value="0" onclick="selectOption('<?php echo $column; ?>', '0')"><i class="fas fa-times mr-1"></i>No</button>
                                </div>
                            <?php elseif (($column === 'rate_type' && $data['table'] === 'employee_payment_rates') || ($column === 'payment_type' && $data['table'] === 'employees')): ?>
                                <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>" class="hidden">
                                <div class="button-group">
                                    <button type="button" class="btn-option btn-blue" data-value="Fixed" onclick="selectOption('<?php echo $column; ?>', 'Fixed')">Fixed</button>
                                    <button type="button" class="btn-option btn-orange" data-value="Daily" onclick="selectOption('<?php echo $column; ?>', 'Daily')">Daily</button>
                                </div>
                            <?php elseif ($column === 'payment_type' && $data['table'] === 'employee_payments'): ?>
                                <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>" class="hidden">
                                <div class="button-group">
                                    <button type="button" class="btn-option btn-green" data-value="Monthly Salary" onclick="selectOption('<?php echo $column; ?>', 'Monthly Salary')">Monthly Salary</button>
                                    <button type="button" class="btn-option btn-yellow" data-value="Daily Wage" onclick="selectOption('<?php echo $column; ?>', 'Daily Wage')">Daily Wage</button>
                                    <button type="button" class="btn-option btn-orange" data-value="Advance" onclick="selectOption('<?php echo $column; ?>', 'Advance')">Advance</button>
                                    <button type="button" class="btn-option btn-purple" data-value="Other" onclick="selectOption('<?php echo $column; ?>', 'Other')">Other</button>
                                </div>
                            <?php elseif ($column === 'transaction_type' && $data['table'] === 'cash_hand'): ?>
                                <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>" class="hidden">
                                <div class="button-group">
                                    <button type="button" class="btn-option btn-green" data-value="In" onclick="selectOption('<?php echo $column; ?>', 'In')">In</button>
                                    <button type="button" class="btn-option btn-red" data-value="Out" onclick="selectOption('<?php echo $column; ?>', 'Out')">Out</button>
                                </div>
                            <?php elseif ($column === 'increment_type' && $data['table'] === 'salary_increments'): ?>
                                <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>" class="hidden">
                                <div class="button-group">
                                    <button type="button" class="btn-option btn-blue" data-value="Promotion" onclick="selectOption('<?php echo $column; ?>', 'Promotion')">Promotion</button>
                                    <button type="button" class="btn-option btn-green" data-value="Merit" onclick="selectOption('<?php echo $column; ?>', 'Merit')">Merit</button>
                                    <button type="button" class="btn-option btn-yellow" data-value="Annual" onclick="selectOption('<?php echo $column; ?>', 'Annual')">Annual</button>
                                    <button type="button" class="btn-option btn-purple" data-value="Other" onclick="selectOption('<?php echo $column; ?>', 'Other')">Other</button>
                                </div>
                            <?php elseif ($column === 'status' && $data['table'] === 'maintenance_schedule'): ?>
                                <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>" class="hidden">
                                <div class="button-group grid grid-cols-2 gap-3">
                                    <button type="button" class="btn-option btn-blue" data-value="scheduled" onclick="selectOption('<?php echo $column; ?>', 'scheduled')"><i class="fas fa-calendar-alt mr-1"></i> Scheduled</button>
                                    <button type="button" class="btn-option btn-green" data-value="completed" onclick="selectOption('<?php echo $column; ?>', 'completed')"><i class="fas fa-check mr-1"></i> Completed</button>
                                    <button type="button" class="btn-option btn-yellow" data-value="overdue" onclick="selectOption('<?php echo $column; ?>', 'overdue')"><i class="fas fa-exclamation mr-1"></i> Overdue</button>
                                    <button type="button" class="btn-option btn-red" data-value="cancelled" onclick="selectOption('<?php echo $column; ?>', 'cancelled')"><i class="fas fa-times mr-1"></i> Cancelled</button>
                                </div>
                            <?php elseif (($column === 'scheduled_date' || $column === 'actual_date') && $data['table'] === 'maintenance_schedule'): ?>
                                <input type="date" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <?php elseif (in_array($column, $dateColumns)): ?>
                                <input type="date" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <?php elseif (in_array($column, $timeColumns)): ?>
                                <div class="clock-picker">
                                    <input type="time" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>" class="w-full">
                                    <div class="clock-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                            <?php else: ?>
                                <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" aria-label="<?php echo htmlspecialchars($column); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="form-actions flex justify-end space-x-4 pt-6">
                        <button type="button" class="btn bg-gray-200 text-gray-800 hover:bg-gray-300" onclick="closeModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" class="btn bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700 shadow-md hover:shadow-lg" onclick="openConfirmModal('update')">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Invoice Modal -->
    <div class="modal" id="invoice-modal">
        <div class="modal-content">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6 rounded-t-xl">
                <h2 class="text-2xl font-bold text-white text-center"><i class="fas fa-file-invoice"></i> Invoice Details</h2>
            </div>
            <div class="p-6">
                <div id="invoice-details" class="invoice-details">
                    <div class="invoice-grid">
                        <div class="invoice-item"><span class="label">Invoice Number:</span><span id="invoice-no" class="font-medium">-</span></div>
                        <div class="invoice-item"><span class="label">Invoice Date:</span><span id="invoice-date" class="font-medium">-</span></div>
                        <div class="invoice-item"><span class="label">Invoice Value:</span><span id="invoice-value" class="font-medium">-</span></div>
                        <div class="invoice-item"><span class="label">Job Details:</span><span id="invoice-job" class="font-medium">-</span></div>
                        <div class="invoice-item"><span class="label">Receiving Payment:</span><span id="invoice-receiving" class="font-medium">-</span></div>
                        <div class="invoice-item"><span class="label">Received Amount:</span><span id="invoice-received" class="font-medium">-</span></div>
                        <div class="invoice-item"><span class="label">Payment Received Date:</span><span id="invoice-payment-date" class="font-medium">-</span></div>
                        <div class="invoice-item full-width"><span class="label">Remarks:</span><span id="invoice-remarks" class="font-medium">-</span></div>
                    </div>
                    <div class="form-actions flex justify-end pt-6">
                        <button class="btn bg-gray-200 text-gray-800 hover:bg-gray-300" onclick="closeInvoiceModal()">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </div>
                <div class="spinner" id="invoice-spinner"><i class="fas fa-spinner fa-spin text-3xl text-green-500"></i></div>
            </div>
        </div>
    </div>
    <!-- Confirmation Modal -->
    <div class="modal" id="confirm-modal">
        <div class="modal-content">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 p-6 rounded-t-xl">
                <h2 id="confirm-title" class="text-2xl font-bold text-white text-center" aria-live="polite"></h2>
            </div>
            <div class="p-6">
                <p id="confirm-message" class="text-gray-700 mb-8 text-lg text-center"></p>
                <div class="form-actions flex justify-end space-x-4">
                    <button type="button" class="btn bg-gray-200 text-gray-800 hover:bg-gray-300" onclick="closeConfirmModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700 shadow-md hover:shadow-lg" id="confirm-action-btn">
                        <i class="fas fa-check"></i> Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Job Status Change Modal -->
    <div class="modal" id="status-change-modal">
        <div class="modal-content">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 rounded-t-xl">
                <h2 class="text-2xl font-bold text-white text-center">Change Job Status</h2>
            </div>
            <div class="p-6">
                <p class="text-gray-700 mb-6 text-center">Select the new status for this job:</p>
                <div class="status-options grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                    <button type="button" class="status-option-btn p-4 rounded-lg border-2 border-gray-200 text-left hover:border-blue-500 hover:bg-blue-50 transition-all duration-200" data-value="0.0">
                        <div class="font-semibold text-gray-900">Not Started</div>
                        <div class="text-sm text-gray-600 mt-1">Job has not been started yet</div>
                    </button>
                    <button type="button" class="status-option-btn p-4 rounded-lg border-2 border-gray-200 text-left hover:border-blue-500 hover:bg-blue-50 transition-all duration-200" data-value="0.2">
                        <div class="font-semibold text-gray-900">Started</div>
                        <div class="text-sm text-gray-600 mt-1">Job has been started</div>
                    </button>
                    <button type="button" class="status-option-btn p-4 rounded-lg border-2 border-gray-200 text-left hover:border-blue-500 hover:bg-blue-50 transition-all duration-200" data-value="0.5">
                        <div class="font-semibold text-gray-900">Ongoing</div>
                        <div class="text-sm text-gray-600 mt-1">Job is currently in progress</div>
                    </button>
                    <button type="button" class="status-option-btn p-4 rounded-lg border-2 border-gray-200 text-left hover:border-blue-500 hover:bg-blue-50 transition-all duration-200" data-value="1.0">
                        <div class="font-semibold text-gray-900">Completed</div>
                        <div class="text-sm text-gray-600 mt-1">Job has been completed</div>
                    </button>
                    <button type="button" class="status-option-btn p-4 rounded-lg border-2 border-gray-200 text-left hover:border-blue-500 hover:bg-blue-50 transition-all duration-200" data-value="0.1">
                        <div class="font-semibold text-gray-900">Cancelled</div>
                        <div class="text-sm text-gray-600 mt-1">Job has been cancelled</div>
                    </button>
                    <button type="button" class="status-option-btn p-4 rounded-lg border-2 border-gray-200 text-left hover:border-blue-500 hover:bg-blue-50 transition-all duration-200" data-value="0.3">
                        <div class="font-semibold text-gray-900">Postponed</div>
                        <div class="text-sm text-gray-600 mt-1">Job has been postponed</div>
                    </button>
                </div>
                <div class="form-actions flex justify-end space-x-4">
                    <button type="button" class="btn bg-gray-200 text-gray-800 hover:bg-gray-300" onclick="closeStatusChangeModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700 shadow-md hover:shadow-lg" id="status-change-confirm-btn" disabled>
                        <i class="fas fa-sync-alt"></i> Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            var columns = <?php echo json_encode($data['columns']); ?>;
            var dateColumns = <?php echo json_encode($dateColumns); ?>;
            var table;
            $('#loading-spinner').show();
            $('#data-table').hide();
            $('#crud-modal').hide();
            $('#invoice-modal').hide();
            $('#confirm-modal').hide();
            $('.select2-dropdown').select2({
                placeholder: "Search and select...",
                allowClear: true,
                width: '100%'
            });
            var columnDefs = [
                <?php foreach ($data['columns'] as $column): ?>
                    <?php if ($data['table'] === 'jobs' && $column === 'status') continue; ?>
                    {
                        data: "<?php echo htmlspecialchars($column); ?>",
                        title: "<?php echo htmlspecialchars($column); ?>",
                        name: "<?php echo htmlspecialchars($column); ?>",
                        <?php if ($column === 'paid' && $data['table'] === 'operational_expenses'): ?>
                        render: function(data, type, row) {
                            if (data === null || data === undefined || data === '') {
                                return '<div class="paid-cell pending-status"><span style="color: #9ca3af;">—</span></div>';
                            }
                            var v = String(data).toLowerCase();
                            if (v === '1' || v === 'true' || v === 'yes' || v === 'y') {
                                return '<div class="paid-cell paid-status"><i class="fas fa-check" style="color: #10B981; font-size: 18px;"></i></div>';
                            } else if (v === '0' || v === 'false' || v === 'no' || v === 'n') {
                                return '<div class="paid-cell unpaid-status"><i class="fas fa-times" style="color: #EF4444; font-size: 18px;"></i></div>';
                            }
                            return '<div class="paid-cell pending-status"><span style="color: #9ca3af;">—</span></div>';
                        }
                        <?php endif; ?>
                    },
                <?php endforeach; ?>
                {
                    data: null,
                    title: "Actions",
                    name: "actions",
                    render: function(data, type, row) {
                        var buttons = '<button class="action-btn edit mr-2" data-id="' + row.<?php echo htmlspecialchars($primaryKey); ?> + '"><i class="fas fa-edit"></i> Edit</button>' +
                                      '<button class="action-btn delete" data-id="' + row.<?php echo htmlspecialchars($primaryKey); ?> + '"><i class="fas fa-trash"></i> Delete</button>';
                        <?php if ($data['table'] === 'operational_expenses'): ?>
                            var paidStatus = row.paid;
                            if (paidStatus !== '1' && paidStatus !== 1) {
                                buttons += '<button class="action-btn mark-paid ml-2" onclick="markAsPaid(this, ' + row.expense_id + ')" title="Mark as Paid"><i class="fas fa-check"></i> Mark as Paid</button>';
                            }
                        <?php elseif ($data['table'] === 'jobs'): ?>
                            if (row.has_invoice) {
                                buttons += '<button class="action-btn invoice ml-2" data-id="' + row.<?php echo htmlspecialchars($primaryKey); ?> + '"><i class="fas fa-file-invoice"></i> Invoice</button>';
                            }
                            var statusVal = String(row.completion || '0.0');
                            buttons += '<button class="action-btn status-change ml-2' + '" ' +
                                       'data-id="' + row.<?php echo htmlspecialchars($primaryKey); ?> + '" ' +
                                       'data-completion="' + statusVal + '">' +
                                       '<i class="fas fa-sync-alt"></i> Change Status</button>';
                            if (row.company_reference && /a2z/i.test(row.company_reference)) {
                                buttons += '<button class="action-btn generate-maintenance ml-2" data-job-id="' + row.job_id + '"><i class="fas fa-tools"></i> Generate Maintenance</button>';
                            }
                        <?php endif; ?>
                        return buttons;
                    }
                }
            ];
            table = $('#data-table').DataTable({
                paging: true,
                pageLength: 10,
                lengthChange: true,
                lengthMenu: [10, 25, 50, 100],
                processing: true,
                serverSide: true,
                searching: false,
                scrollX: false,
                autoWidth: false,
                order: [[0, 'desc']],
                dom: 'rtip',
                ajax: {
                    url: "<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>",
                    type: "POST",
                    data: function(d) {
                        var searchValue = $('#searchInput').val().trim();
                        var searchTerms = searchValue ? searchValue.split(/\s+/) : [];
                        d.action = 'get_records';
                        d.search = {
                            terms: searchTerms,
                            isDate: /^\d{4}-\d{2}-\d{2}$/.test(searchValue)
                        };
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
                        updatePaginationInfo();
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
                    updatePaginationInfo();
                },
                initComplete: function() {
                    $('#data-table tbody').on('click', '.edit', function() {
                        var id = $(this).data('id');
                        var rowData = table.row($(this).closest('tr')).data();
                        if (rowData) {
                            openModal('update', id, rowData);
                        }
                    });
                    $('#data-table tbody').on('click', '.delete', function() {
                        var id = $(this).data('id');
                        openConfirmModal('delete', id);
                    });
                    $('#data-table tbody').on('click', '.invoice', function() {
                        var id = $(this).data('id');
                        openInvoiceModal(id);
                    });
                    $('#data-table tbody').on('click', '.status-change', function() {
                        var jobId = $(this).data('id');
                        var currentCompletion = $(this).data('completion');
                        openStatusChangeModal(jobId, currentCompletion);
                    });
                }
            });
            function updateRecordCount() {
                var info = table.ajax.json() || {};
                var totalRecords = info.recordsTotal || 0;
                var filteredRecords = info.recordsFiltered !== undefined ? info.recordsFiltered : totalRecords;
                var searchValue = $('#searchInput').val();
                var displayText = totalRecords;
                if (searchValue && filteredRecords !== totalRecords) {
                    displayText += ' (' + filteredRecords + ' Filtered)';
                }
                $('#record-count').text(displayText);
                updatePaginationInfo();
            }
            function updatePaginationInfo() {
                var info = table.page.info();
                var totalRecords = info.recordsTotal;
                var start = info.start + 1;
                var end = info.end;
                var paginationText = 'Showing ' + start + ' to ' + end + ' of ' + totalRecords + ' entries';
                $('#pagination-info').text(paginationText);
            }
            $('#searchInput').on('keyup', function(e) {
                var searchValue = $(this).val().trim();
                if (/^\d{2}[-\\/]\d{2}[-\\/]\d{4}$/.test(searchValue)) {
                    var parts = searchValue.split(/[-\\/]/);
                    searchValue = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    $(this).val(searchValue);
                }
                if (e.key === 'Enter' || searchValue.length >= 3 || searchValue === '') {
                    table.ajax.reload(function() {
                        updateRecordCount();
                        updatePaginationInfo();
                    }, false);
                }
            });
            $(window).on('resize', function() {
                table.columns.adjust();
            });
            $('#entries-per-page').on('change', function() {
                var length = parseInt($(this).val());
                table.page.len(length).draw();
            });
            $('#refresh-table').on('click', function() {
                table.ajax.reload(function() {
                    updateRecordCount();
                    updatePaginationInfo();
                }, false);
            });
            $('#data-table tbody').on('click', '.generate-maintenance', function() {
                var btn = $(this);
                var jobId = btn.data('job-id');
                if (!jobId) return alert('Job ID missing');
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
                $.post('<?php echo BASE_PATH; ?>/admin/manageTable/jobs', { action: 'generate_maintenance_for_job', job_id: jobId }, function(resp) {
                    if (resp && resp.success) {
                        alert('Maintenance generated. Inserted: ' + (resp.inserted || 0));
                        $('#refresh-table').click();
                    } else {
                        alert('Error: ' + (resp.error || 'Unknown error'));
                    }
                }, 'json').fail(function() {
                    alert('Request failed');
                }).always(function() {
                    btn.prop('disabled', false).html('<i class="fas fa-tools"></i> Generate Maintenance');
                });
            });
        });
        function openModal(action, id = null, data = null) {
            if (!$('#data-table').is(':visible')) {
                setTimeout(() => openModal(action, id, data), 100);
                return;
            }
            $('#crud-modal').addClass('show');
            setTimeout(() => {
                $('#crud-modal').show();
            }, 10);
            $('#form-action').val(action);
            $('#modal-title').text(action === 'create' ? 'Add New Record' : 'Edit Record');
            $('.primary-key-field').closest('.form-group').show();
            $('#crud-form')[0].reset();
            $('#form-id').val(id || '');
            $('.btn-option').removeClass('active');
            $('.select2-dropdown').val('').trigger('change');
            if (action === 'create') {
                $('.primary-key-field').closest('.form-group').hide();
            } else if (action === 'update' && data) {
                $('.primary-key-field').closest('.form-group').show();
                var dateColumns = <?php echo json_encode($dateColumns); ?>;
                <?php foreach ($data['columns'] as $column):
                    if ($column === 'completion') continue;
                    ?>
                    if (dateColumns.includes('<?php echo $column; ?>') && data.<?php echo htmlspecialchars($column); ?>) {
                        let dateValue = data.<?php echo htmlspecialchars($column); ?>;
                        if (dateValue && !/^\d{4}-\d{2}-\d{2}$/.test(dateValue)) {
                            try {
                                let date = new Date(dateValue);
                                dateValue = date.toISOString().split('T')[0];
                            } catch (e) {
                                dateValue = '';
                            }
                        }
                        $('#<?php echo $column; ?>').val(dateValue || '');
                    } else if (data.<?php echo htmlspecialchars($column); ?> !== undefined) {
                        let value = data.<?php echo htmlspecialchars($column); ?> || '';
                        $('#<?php echo $column; ?>').val(value);
                        if ($('#<?php echo $column; ?>').hasClass('select2-dropdown')) {
                            $('#<?php echo $column; ?>').val(value).trigger('change');
                        }
                        var button = document.querySelector(`#crud-form .form-group button[data-value="${value}"][onclick*="selectOption('<?php echo $column; ?>')"]`);
                        if (button) {
                            document.querySelectorAll(`#crud-form .form-group button[onclick*="selectOption('<?php echo $column; ?>')"]`).forEach(btn => btn.classList.remove('active'));
                            button.classList.add('active');
                        }
                    }
                <?php endforeach; ?>
            }
        }
        function closeModal() {
            $('#crud-modal').removeClass('show');
            setTimeout(() => {
                $('#crud-modal').hide();
            }, 300);
        }
        function selectOption(fieldId, value) {
            document.getElementById(fieldId).value = value;
            const buttons = document.querySelectorAll(`#crud-form .form-group button[data-value][onclick*="${fieldId}"]`);
            buttons.forEach(btn => btn.classList.remove('active'));
            const selectedButton = document.querySelector(`#crud-form .form-group button[data-value="${value}"][onclick*="${fieldId}"]`);
            if (selectedButton) selectedButton.classList.add('active');
        }
        function markAsPaid(button, expenseId) {
            updatePaidStatus(button, expenseId, 1, 'paid');
        }
        function updatePaidStatus(button, expenseId, status, statusClass) {
            const originalHtml = button.outerHTML;
            const actionCell = button.closest('td');
            actionCell.innerHTML = '<i class="fas fa-spinner fa-spin" style="color: #3b82f6; font-size: 18px;"></i>';
            $.ajax({
                url: "<?php echo BASE_PATH; ?>/admin/manageTable/operational_expenses",
                type: "POST",
                dataType: "json",
                data: {
                    action: 'update',
                    id: expenseId,
                    paid: status
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload(function() {
                            showToast('Status updated successfully', 'success');
                        }, false);
                    } else {
                        actionCell.innerHTML = originalHtml;
                        showToast('Error updating status', 'error');
                    }
                },
                error: function() {
                    actionCell.innerHTML = originalHtml;
                    showToast('Error updating status', 'error');
                }
            });
        }
        function openInvoiceModal(jobId) {
            if (!$('#data-table').is(':visible')) {
                setTimeout(() => openInvoiceModal(jobId), 100);
                return;
            }
            $('#invoice-modal').addClass('show');
            setTimeout(() => {
                $('#invoice-modal').show();
            }, 10);
            $('#invoice-spinner').show();
            $('#invoice-details').hide();
            $.post("<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>", {
                action: 'get_invoice_details',
                job_id: jobId
            }, function(data) {
                $('#invoice-spinner').hide();
                $('#invoice-details').show();
                const formatDate = (dateStr) => {
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
                $('#invoice-no').text(data.invoice_no || '-');
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
        }
        function closeInvoiceModal() {
            $('#invoice-modal').removeClass('show');
            setTimeout(() => {
                $('#invoice-modal').hide();
            }, 300);
        }
        function openConfirmModal(action, id = null) {
            if (!$('#data-table').is(':visible')) {
                setTimeout(() => openConfirmModal(action, id), 100);
                return;
            }
            $('#confirm-modal').addClass('show');
            setTimeout(() => {
                $('#confirm-modal').show();
            }, 10);
            const titles = {
                'create': 'Confirm Add',
                'update': 'Confirm Update',
                'delete': 'Confirm Delete'
            };
            const messages = {
                'create': 'Are you sure you want to add this new record?',
                'update': 'Are you sure you want to save changes to this record?',
                'delete': 'Are you sure you want to delete this record? This action cannot be undone.'
            };
            $('#confirm-title').text(titles[action]);
            $('#confirm-message').text(messages[action]);
            $('#confirm-action-btn').off('click').on('click', function() {
                if (action === 'create' || action === 'update') {
                    $.ajax({
                        url: "<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>",
                        type: 'POST',
                        data: $('#crud-form').serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                closeConfirmModal();
                                closeModal();
                                table.ajax.reload(function() {
                                    updateRecordCount();
                                    updatePaginationInfo();
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
                    $.post("<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>", {
                        action: 'delete',
                        id: id
                    }, function(response) {
                        if (response.success) {
                            closeConfirmModal();
                            table.ajax.reload(function() {
                                updateRecordCount();
                                updatePaginationInfo();
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
        }
        function closeConfirmModal() {
            $('#confirm-modal').removeClass('show');
            setTimeout(() => {
                $('#confirm-modal').hide();
            }, 300);
        }
        let selectedJobId = null;
        let selectedCompletion = null;
        function openStatusChangeModal(jobId, currentCompletion) {
            selectedJobId = jobId;
            selectedCompletion = currentCompletion;
            $('.status-option-btn').removeClass('selected');
            $('#status-change-confirm-btn').prop('disabled', true);
            $('#status-change-modal').addClass('show');
            setTimeout(() => {
                $('#status-change-modal').show();
            }, 10);
            if (currentCompletion !== null && currentCompletion !== undefined) {
                $('.status-option-btn[data-value="' + currentCompletion + '"]').addClass('selected');
            }
        }
        function closeStatusChangeModal() {
            $('#status-change-modal').removeClass('show');
            setTimeout(() => {
                $('#status-change-modal').hide();
            }, 300);
        }
        $(document).on('click', '.status-option-btn', function() {
            $('.status-option-btn').removeClass('selected');
            $(this).addClass('selected');
            selectedCompletion = $(this).data('value');
            $('#status-change-confirm-btn').prop('disabled', false);
        });
        $('#status-change-confirm-btn').on('click', function() {
            if (!selectedJobId || selectedCompletion === null) {
                alert('Please select a status');
                return;
            }
            $.ajax({
                url: "<?php echo BASE_PATH; ?>/admin/manageTable/jobs",
                type: 'POST',
                data: {
                    action: 'update_status',
                    job_id: selectedJobId,
                    completion: selectedCompletion
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        closeStatusChangeModal();
                        table.ajax.reload(function() {
                            updateRecordCount();
                            updatePaginationInfo();
                        }, false);
                        alert('Job status updated successfully!');
                    } else {
                        alert('Error updating job status: ' + (response.error || 'Unknown error'));
                    }
                },
                error: function(xhr, error) {
                    console.error('Status update error:', error);
                    alert('Error updating job status: ' + (xhr.responseJSON?.error || 'Server error'));
                }
            });
        });
    </script>
</body>
</html>