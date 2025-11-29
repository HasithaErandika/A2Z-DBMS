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
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Include Select2 for searchable dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
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
        }
        
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalFadeIn 0.3s ease-out;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        @keyframes modalFadeIn {
            from {opacity: 0; transform: translateY(-50px);}
            to {opacity: 1; transform: translateY(0);}
        }
        
        .btn {
            transition: all 0.2s ease-in-out;
        }
        
        .btn:hover {
            transform: translateY(-2px);
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
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            background-color: white;
            font-size: 14px;
        }
        
        .spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .table-wrapper {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        #data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        #data-table th,
        #data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        #data-table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        
        #data-table tr:hover {
            background-color: #f9fafb;
        }
        
        .btn-option.active {
            box-shadow: 0 0 0 2px #3b82f6;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #374151;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 14px;
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
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .btn-option {
            flex: 1;
            min-width: 80px;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            background-color: white;
            font-size: 13px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-option:hover {
            opacity: 0.9;
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
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .invoice-item {
            padding: 12px;
            background-color: #f9fafb;
            border-radius: 6px;
        }
        
        .invoice-item.full-width {
            grid-column: 1 / -1;
        }
        
        .label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }
        
        .stat-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background-color: #eff6ff;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            color: #1e40af;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 16px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        
        .search-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        
        /* Custom design elements */
        .custom-design-element {
            position: relative;
        }
        
        .custom-design-element::before {
            content: '';
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 50%;
            z-index: 1;
        }
        
        .custom-design-element::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: -8px;
            width: 16px;
            height: 16px;
            background: linear-gradient(135deg, #10b981, #3b82f6);
            border-radius: 50%;
            z-index: 1;
        }
        
        /* Custom styles for DataTables pagination */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5rem 0.75rem;
            margin-left: 0.25rem;
            border-radius: 0.375rem;
            border: 1px solid #e5e7eb;
            background-color: #fff;
            color: #374151 !important;
            transition: all 0.2s;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #f3f4f6;
            color: #1f2937 !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #3b82f6;
            color: #fff !important;
            border-color: #3b82f6;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            pointer-events: none;
        }
        
        .dataTables_wrapper .dataTables_length select {
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            padding: 0.25rem 0.5rem;
            background-color: #fff;
            color: #374151;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            padding: 0.25rem 0.5rem;
        }
        
        .dataTables_info {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        /* Clock picker styling */
        .clock-picker {
            position: relative;
        }
        
        .clock-picker .clock-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }
        
        /* Enhanced pagination info */
        .pagination-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .entries-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .entries-control select {
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            padding: 0.25rem 0.5rem;
            background-color: #fff;
            color: #374151;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Enhanced Header with Custom Design Elements -->
    <div class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-0 sm:px-0 lg:px-0">
            <div class="flex justify-between items-center py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center space-x-4">
                    <div class="custom-design-element">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-12 h-12 rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-table text-white text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Manage <?php echo htmlspecialchars($data['table']); ?></h1>
                        <p class="text-sm text-gray-500">Internal Database Management System</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center gap-2" onclick="openModal('create')">
                        <i class="fas fa-plus"></i> Add Record
                    </button>
                    <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition-all duration-300 flex items-center gap-2" onclick="window.location.href='<?php echo BASE_PATH; ?>/admin/tables'">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-0 sm:px-0 lg:px-0 py-8">
        <!-- Enhanced Filters Section -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 mx-4 sm:mx-6 lg:mx-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <?php if ($data['table'] === 'jobs' && isset($data['totalCapacity'])): ?>
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg text-sm font-medium shadow-md">
                            <i class="fas fa-bolt"></i>
                            <span>PV Capacity: <?php echo number_format($data['totalCapacity'], 2); ?> kW</span>
                        </div>
                    <?php endif; ?>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg text-sm font-medium shadow-md" id="record-count">
                        <i class="fas fa-database"></i>
                        <span><?php echo $data['totalRecords']; ?> Records</span>
                    </div>
                </div>
                
                <form class="flex flex-col sm:flex-row gap-3 export-form" method="POST" action="<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>">
                    <input type="hidden" name="action" value="export_csv">
                    <div class="flex gap-2">
                        <input type="date" name="start_date" required aria-label="Start Date" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="YYYY-MM-DD">
                        <input type="date" name="end_date" required aria-label="End Date" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="YYYY-MM-DD">
                    </div>
                    <button type="submit" class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-4 py-2 rounded-lg font-medium hover:from-green-600 hover:to-emerald-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center gap-2">
                        <i class="fas fa-download"></i> Export
                    </button>
                </form>
            </div>
        </div>

        <!-- Enhanced Search Bar -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 mx-4 sm:mx-6 lg:mx-8">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm" id="searchInput" type="text" placeholder="Search table (e.g., '2025-08-04 Meals 2310')" aria-label="Search table data">
            </div>
        </div>

        <!-- Full Width Table Wrapper -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mx-4 sm:mx-6 lg:mx-8">
            <div class="overflow-x-auto">
                <table id="data-table" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <?php foreach ($data['columns'] as $column): ?>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo htmlspecialchars($column); ?></th>
                            <?php endforeach; ?>
                            <?php if ($data['table'] === 'jobs'): ?>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <?php endif; ?>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Data will be populated by DataTables -->
                    </tbody>
                </table>
                <div class="spinner" id="loading-spinner"><i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i></div>
            </div>
            
            <!-- Enhanced Pagination Info -->
            <div class="pagination-info px-6 py-4 border-t border-gray-200">
                <div class="entries-control">
                    <label for="entries-per-page" class="text-gray-700">Show</label>
                    <select id="entries-per-page" class="border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-gray-700">entries</span>
                </div>
                <div class="dataTables_info" id="pagination-info">
                    Showing 1 to 10 of <?php echo $data['totalRecords']; ?> entries
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced CRUD Modal -->
    <div class="modal" id="crud-modal">
        <div class="modal-content">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 rounded-t-lg">
                <h2 id="modal-title" class="text-xl font-bold text-white" aria-live="polite"></h2>
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
                        'jobs' => 'job_id', 'cash_hand' => 'cash_id'
                    ];
                    $primaryKey = $primaryKeys[$data['table']] ?? $data['columns'][0];
                    $dateColumns = ['date_started', 'date_completed', 'date', 'attendance_date', 'date_of_joined', 'date_of_resigned', 'date_of_birth', 'effective_date', 'end_date', 'expensed_date', 'invoice_date', 'payment_date', 'increment_date', 'txn_date', 'payment_received_date'];
                    $timeColumns = ['start_time', 'end_time'];
                    foreach ($data['columns'] as $column): 
                        if ($column === 'completion') continue; // Skip completion column
                        ?>
                        <div class="form-group">
                            <label for="<?php echo $column; ?>" class="block text-sm font-medium text-gray-700 mb-1"><?php echo htmlspecialchars($column); ?></label>
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
                                    <button type="button" class="btn-option btn-green" data-value="Yes" onclick="selectOption('<?php echo $column; ?>', 'Yes')">Yes</button>
                                    <button type="button" class="btn-option btn-red" data-value="No" onclick="selectOption('<?php echo $column; ?>', 'No')">No</button>
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
                    <div class="form-actions flex justify-end space-x-3 pt-4">
                        <button type="button" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-all duration-300 flex items-center gap-2" onclick="closeModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center gap-2" onclick="openConfirmModal('update')">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enhanced Invoice Modal -->
    <div class="modal" id="invoice-modal">
        <div class="modal-content">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6 rounded-t-lg">
                <h2 class="text-xl font-bold text-white"><i class="fas fa-file-invoice"></i> Invoice Details</h2>
            </div>
            <div class="p-6">
                <div id="invoice-details" class="invoice-details">
                    <div class="invoice-grid">
                        <div class="invoice-item"><span class="label">Invoice Number:</span><span id="invoice-no">-</span></div>
                        <div class="invoice-item"><span class="label">Invoice Date:</span><span id="invoice-date">-</span></div>
                        <div class="invoice-item"><span class="label">Invoice Value:</span><span id="invoice-value">-</span></div>
                        <div class="invoice-item"><span class="label">Job Details:</span><span id="invoice-job">-</span></div>
                        <div class="invoice-item"><span class="label">Receiving Payment:</span><span id="invoice-receiving">-</span></div>
                        <div class="invoice-item"><span class="label">Received Amount:</span><span id="invoice-received">-</span></div>
                        <div class="invoice-item"><span class="label">Payment Received Date:</span><span id="invoice-payment-date">-</span></div>
                        <div class="invoice-item full-width"><span class="label">Remarks:</span><span id="invoice-remarks">-</span></div>
                    </div>
                    <div class="form-actions flex justify-end pt-4">
                        <button class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-all duration-300 flex items-center gap-2" onclick="closeInvoiceModal()">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </div>
                <div class="spinner" id="invoice-spinner"><i class="fas fa-spinner fa-spin text-2xl text-green-500"></i></div>
            </div>
        </div>
    </div>

    <!-- Enhanced Confirmation Modal -->
    <div class="modal" id="confirm-modal">
        <div class="modal-content">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 p-6 rounded-t-lg">
                <h2 id="confirm-title" class="text-xl font-bold text-white" aria-live="polite"></h2>
            </div>
            <div class="p-6">
                <p id="confirm-message" class="text-gray-700 mb-6"></p>
                <div class="form-actions flex justify-end space-x-3">
                    <button type="button" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-all duration-300 flex items-center gap-2" onclick="closeConfirmModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center gap-2" id="confirm-action-btn">
                        <i class="fas fa-check"></i> Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <!-- Include Select2 for searchable dropdowns -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            var columns = <?php echo json_encode($data['columns']); ?>;
            var dateColumns = <?php echo json_encode($dateColumns); ?>;
            var table;

            // Initialize spinner and hide table
            $('#loading-spinner').show();
            $('#data-table').hide();

            // Ensure modals are hidden on page load
            $('#crud-modal').hide();
            $('#invoice-modal').hide();
            $('#confirm-modal').hide();

            // Initialize Select2 dropdowns
            $('.select2-dropdown').select2({
                placeholder: "Search and select...",
                allowClear: true,
                width: '100%'
            });

            // Define column definitions for DataTables
            var columnDefs = [
                <?php foreach ($data['columns'] as $column): ?>
                    { 
                        data: "<?php echo htmlspecialchars($column); ?>",
                        title: "<?php echo htmlspecialchars($column); ?>",
                        name: "<?php echo htmlspecialchars($column); ?>"
                    },
                <?php endforeach; ?>
                <?php if ($data['table'] === 'jobs'): ?>
                    {
                        data: "completion",
                        title: "Status",
                        name: "completion",
                        render: function(data, type, row) {
                            return '<select class="status-select" data-id="' + row.<?php echo htmlspecialchars($primaryKey); ?> + '">' +
                                '<option value="0.0" ' + (data == '0.0' ? 'selected' : '') + '>Not Started</option>' +
                                '<option value="0.1" ' + (data == '0.1' ? 'selected' : '') + '>Cancelled</option>' +
                                '<option value="0.2" ' + (data == '0.2' ? 'selected' : '') + '>Started</option>' +
                                '<option value="0.5" ' + (data == '0.5' ? 'selected' : '') + '>Ongoing</option>' +
                                '<option value="1.0" ' + (data == '1.0' ? 'selected' : '') + '>Completed</option>' +
                                '</select>';
                        }
                    },
                <?php endif; ?>
                {
                    data: null,
                    title: "Actions",
                    name: "actions",
                    render: function(data, type, row) {
                        var buttons = '<button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm mr-1 edit-btn" data-id="' + row.<?php echo htmlspecialchars($primaryKey); ?> + '"><i class="fas fa-edit"></i></button>' +
                                      '<button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm delete-btn" data-id="' + row.<?php echo htmlspecialchars($primaryKey); ?> + '"><i class="fas fa-trash"></i></button>';
                        <?php if ($data['table'] === 'jobs'): ?>
                            if (row.has_invoice) {
                                buttons += '<button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-sm invoice-btn" data-id="' + row.<?php echo htmlspecialchars($primaryKey); ?> + '"><i class="fas fa-file-invoice"></i></button>';
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
                    // Event delegation for edit button
                    $('#data-table tbody').on('click', '.edit-btn', function() {
                        var id = $(this).data('id');
                        var rowData = table.row($(this).closest('tr')).data();
                        if (rowData) {
                            openModal('update', id, rowData);
                        }
                    });

                    // Event delegation for delete button
                    $('#data-table tbody').on('click', '.delete-btn', function() {
                        var id = $(this).data('id');
                        openConfirmModal('delete', id);
                    });

                    // Event delegation for status select
                    $('#data-table tbody').on('change', '.status-select', function() {
                        var id = $(this).data('id');
                        var newStatus = $(this).val();
                        $.post("<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>", {
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

                    // Event delegation for invoice button
                    $('#data-table tbody').on('click', '.invoice-btn', function() {
                        var id = $(this).data('id');
                        openInvoiceModal(id);
                    });
                }
            });

            // Function to update record count display
            function updateRecordCount() {
                var info = table.ajax.json() || {};
                var totalRecords = info.recordsTotal || 0;
                var filteredRecords = info.recordsFiltered !== undefined ? info.recordsFiltered : totalRecords;
                var searchValue = $('#searchInput').val();
                var displayText = totalRecords + ' Records';
                if (searchValue && filteredRecords !== totalRecords) {
                    displayText += ' (' + filteredRecords + ' Filtered)';
                }
                $('#record-count span').text(displayText);
            }

            // Function to update pagination info
            function updatePaginationInfo() {
                var info = table.page.info();
                var totalRecords = info.recordsTotal;
                var start = info.start + 1;
                var end = info.end;
                $('#pagination-info').text('Showing ' + start + ' to ' + end + ' of ' + totalRecords + ' entries');
            }

            // Custom search bar functionality
            $('#searchInput').on('keyup', function(e) {
                var searchValue = $(this).val().trim();
                // Normalize date formats (e.g., DD-MM-YYYY or DD/MM/YYYY to YYYY-MM-DD)
                if (/^\d{2}[-\\/]\d{2}[-\\/]\d{4}$/.test(searchValue)) {
                    var parts = searchValue.split(/[-\\/]/);
                    searchValue = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    $(this).val(searchValue);
                }
                // Trigger search on Enter key or after typing delay
                if (e.key === 'Enter' || searchValue.length >= 3 || searchValue === '') {
                    table.ajax.reload(function() {
                        updateRecordCount();
                        updatePaginationInfo();
                    }, false);
                }
            });

            // Adjust table on window resize
            $(window).on('resize', function() {
                table.columns.adjust();
            });

            // Entries per page change
            $('#entries-per-page').on('change', function() {
                var length = parseInt($(this).val());
                table.page.len(length).draw();
            });
        });

        function openModal(action, id = null, data = null) {
            if (!$('#data-table').is(':visible')) {
                console.warn('Table not loaded yet, delaying modal open');
                setTimeout(() => openModal(action, id, data), 100);
                return;
            }
            $('#crud-modal').show();
            $('#form-action').val(action);
            $('#modal-title').text(action === 'create' ? 'Add New Record' : 'Edit Record');
            if (action === 'create') {
                $('.primary-key-field').closest('.form-group').hide();
                $('#crud-form')[0].reset();
                $('#form-id').val('');
                $('.btn-option').removeClass('active');
                // Reset Select2 dropdowns
                $('.select2-dropdown').val('').trigger('change');
            } else if (action === 'update' && data) {
                $('.primary-key-field').closest('.form-group').show();
                $('#form-id').val(id);
                var dateColumns = <?php echo json_encode($dateColumns); ?>;
                <?php foreach ($data['columns'] as $column): 
                    if ($column === 'completion') continue; // Skip completion column
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
                        // Update Select2 dropdowns
                        if ($('#<?php echo $column; ?>').hasClass('select2-dropdown')) {
                            $('#<?php echo $column; ?>').val(value).trigger('change');
                        }
                        // Update button groups
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
            $('#crud-modal').hide();
            $('.primary-key-field').closest('.form-group').show();
        }

        function selectOption(fieldId, value) {
            document.getElementById(fieldId).value = value;
            const buttons = document.querySelectorAll(`#crud-form .form-group button[data-value][onclick*="${fieldId}"]`);
            buttons.forEach(btn => btn.classList.remove('active'));
            const selectedButton = document.querySelector(`#crud-form .form-group button[data-value="${value}"][onclick*="${fieldId}"]`);
            if (selectedButton) selectedButton.classList.add('active');
        }

        function openInvoiceModal(jobId) {
            if (!$('#data-table').is(':visible')) {
                console.warn('Table not loaded yet, delaying invoice modal open');
                setTimeout(() => openInvoiceModal(jobId), 100);
                return;
            }
            $('#invoice-modal').show();
            $('#invoice-spinner').show();
            $('#invoice-details').hide();
            $.post("<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>", {
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
        }

        function closeInvoiceModal() {
            $('#invoice-modal').hide();
        }

        function openConfirmModal(action, id = null) {
            if (!$('#data-table').is(':visible')) {
                console.warn('Table not loaded yet, delaying confirm modal open');
                setTimeout(() => openConfirmModal(action, id), 100);
                return;
            }
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
                    // Submit create or update via AJAX
                    $.ajax({
                        url: "<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>",
                        type: 'POST',
                        data: formData,
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
                    // Perform the delete action
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
            $('#confirm-modal').hide();
        }
    </script>
</body>
</html>