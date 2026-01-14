<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage <?php echo htmlspecialchars($data['table']); ?> - A2Z Engineering</title>
    <!-- Fonts & Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa',
                            500: '#2563eb', 600: '#1d4ed8', 700: '#1e40af', 800: '#1e3a8a', 900: '#172554', // Deep Blue
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom Overrides */
        body { background-color: #f1f5f9; } /* Slate 100 - Ashy background */
        
        /* Select2 Tailwind Integration */
        .select2-container .select2-selection--single {
            height: 46px !important;
            border-color: #cbd5e1 !important; /* Slate 300 */
            border-radius: 0.5rem !important;
            display: flex !important;
            align-items: center !important;
            background-color: #f8fafc !important; /* Slate 50 */
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 10px !important;
        }

        /* Modal Transitions */
        .modal {
            transition: opacity 0.3s ease;
            opacity: 0;
            pointer-events: none;
        }
        .modal.show {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-content {
            transition: transform 0.3s ease-out;
            transform: scale(0.95);
        }
        .modal.show .modal-content {
            transform: scale(1);
        }

        /* DataTables Customization */
        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }
        
        /* Custom Scrollbar for heavy tables */
        .custom-scrollbar::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #94a3b8; 
            border-radius: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #64748b; 
        }

        /* Table Row Styling */
        /* Table Row Styling */
        table.dataTable tbody tr, table.dataTable tbody tr.odd, table.dataTable tbody tr.even {
            background-color: #ffffff !important; /* Force White background */
        }
        table.dataTable tbody tr:hover {
            background-color: #e2e8f0; /* Slightly darker ash on hover */
        }
    </style>
</head>
<body class="text-slate-800 antialiased">
    
    <!-- Top Navigation Bar -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-18 py-2">
                <div class="flex items-center gap-4">
                    <a href="<?php echo BASE_PATH; ?>/admin/tables" class="p-2 -ml-2 rounded-full text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-600 text-white p-2.5 rounded-lg shadow-md shadow-blue-200">
                            <i class="fas fa-table text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-900 leading-tight tracking-tight">
                                <?php echo ucfirst(str_replace('_', ' ', $data['table'])); ?>
                            </h1>
                            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Admin Console</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="openModal('create')" class="inline-flex items-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i> New Record
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="w-full px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
             <div class="bg-white hover:bg-slate-50 overflow-hidden shadow-sm rounded-xl border border-slate-200 p-5 flex items-center justify-between transition-all duration-200 group">
                <div>
                     <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Records</p>
                     <p class="text-3xl font-extrabold text-blue-600 mt-1" id="record-count"><?php echo $data['totalRecords']; ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center text-blue-500 group-hover:bg-blue-100 transition-colors">
                    <i class="fas fa-database text-xl"></i>
                </div>
            </div>
            
            <?php if ($data['table'] === 'jobs' && isset($data['totalCapacity'])): ?>
            <div class="bg-white hover:bg-slate-50 overflow-hidden shadow-sm rounded-xl border border-slate-200 p-5 flex items-center justify-between transition-all duration-200 group">
                <div>
                     <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Capacity</p>
                     <p class="text-3xl font-extrabold text-blue-600 mt-1"><?php echo number_format($data['totalCapacity'], 2); ?> <span class="text-lg text-slate-400 font-medium">kW</span></p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center text-amber-500 group-hover:bg-amber-100 transition-colors">
                    <i class="fas fa-bolt text-xl"></i>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($data['table'] === 'operational_expenses'): ?>
            <div class="bg-white hover:bg-slate-50 overflow-hidden shadow-sm rounded-xl border border-slate-200 p-5 flex items-center justify-between transition-all duration-200 group">
                <div>
                     <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Month's Expenses</p>
                     <p class="text-3xl font-extrabold text-red-600 mt-1"><?php echo $data['monthlyExpenses'] ?? '0.00'; ?></p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center text-red-500 group-hover:bg-red-100 transition-colors">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Controls & Filters -->
        <div class="bg-white rounded-xl shadow-lg border border-slate-200 mb-6 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="w-full sm:w-1/2 xl:w-1/3 relative group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                    <input type="text" id="searchInput" 
                        class="block w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg leading-5 bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all duration-200 shadow-sm"
                        placeholder="Search...">
                </div>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button id="refresh-table" class="inline-flex items-center px-4 py-2.5 border border-slate-300 shadow-sm text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                         <i class="fas fa-sync-alt mr-2"></i> Refresh
                    </button>
                    
                    <form class="flex gap-2" method="POST" action="<?php echo BASE_PATH; ?>/admin/manageTable/<?php echo htmlspecialchars($data['table']); ?>">
                        <input type="hidden" name="action" value="export_csv">
                        <div class="hidden sm:flex gap-2">
                            <input type="date" name="start_date" required class="block w-36 pl-3 pr-3 py-2 border border-slate-300 rounded-lg text-sm bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <input type="date" name="end_date" required class="block w-36 pl-3 pr-3 py-2 border border-slate-300 rounded-lg text-sm bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all">
                            <i class="fas fa-file-csv mr-2"></i> Export
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Table Container -->
            <div class="overflow-x-auto custom-scrollbar">
                <table id="data-table" class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-blue-600 text-white">
                        <tr>
                            <?php foreach ($data['columns'] as $column): ?>
                                <?php if ($data['table'] === 'jobs' && $column === 'status') continue; ?>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">
                                    <?php echo str_replace('_', ' ', $column); ?>
                                </th>
                            <?php endforeach; ?>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider sticky right-0 bg-blue-600 shadow-xl z-10 w-32">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200 text-sm text-slate-700">
                        <!-- Data populated by DataTables -->
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Footer (Customized) -->
             <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4" id="custom-pagination-footer">
                <div class="text-sm font-medium text-slate-500" id="pagination-info">
                    Loading records...
                </div>
                <div id="pagination-controls" class="flex items-center gap-2">
                    <!-- Controls injected by JS -->
                </div>
            </div>
        </div>
    </main>

    <!-- CRUD Modal -->
    <div id="crud-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col relative z-20 border border-slate-100">
            <!-- Modal Header -->
            <div class="px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-white rounded-t-2xl">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2" id="modal-title">
                        New Record
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">Fill in the details below.</p>
                </div>
                <button onclick="closeModal()" class="text-slate-400 hover:text-red-500 transition-colors p-2 hover:bg-red-50 rounded-full">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar bg-slate-50/50">
                <form id="crud-form">
                    <input type="hidden" name="action" id="form-action">
                    <input type="hidden" name="id" id="form-id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <?php
                    $primaryKeys = [
                        'employees' => 'emp_id', 'employee_payment_rates' => 'rate_id', 'attendance' => 'attendance_id',
                        'salary_increments' => 'increment_id', 'employee_payments' => 'payment_id', 'invoice_data' => 'invoice_id',
                        'operational_expenses' => 'expense_id', 'projects' => 'project_id', 'employee_bank_details' => 'id',
                        'jobs' => 'job_id', 'cash_hand' => 'cash_id', 'maintenance_schedule' => 'schedule_id'
                    ];
                    $primaryKey = $primaryKeys[$data['table']] ?? $data['columns'][0];
                    $dateColumns = ['date_started', 'date_completed', 'date', 'attendance_date', 'date_of_joined', 'date_of_resigned', 'date_of_birth', 'effective_date', 'end_date', 'expensed_date', 'invoice_date', 'payment_date', 'increment_date', 'txn_date', 'payment_received_date', 'scheduled_date', 'actual_date'];
                    
                    foreach ($data['columns'] as $column):
                        if ($column === 'completion') continue;
                        if ($data['table'] === 'jobs' && $column === 'status') continue;
                        
                        $label = ucfirst(str_replace('_', ' ', $column));
                        $isFullWidth = in_array($column, ['remarks', 'description', 'project_description', 'reason']);
                        $colSpanClass = $isFullWidth ? 'md:col-span-2' : '';
                    ?>
                        <div class="form-group <?php echo $colSpanClass; ?>">
                            <label for="<?php echo $column; ?>" class="block text-sm font-semibold text-slate-700 mb-2"><?php echo $label; ?></label>
                            
                            <?php if ($column === $primaryKey): ?>
                                <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" readonly class="block w-full rounded-lg border-slate-200 bg-slate-100 text-slate-500 shadow-sm sm:text-sm cursor-not-allowed">
                            
                            <?php elseif (in_array($column, ['emp_id', 'job_id', 'project_id', 'expenses_category', 'given_by', 'received_by']) || ($column === 'rate_type' && $data['table'] === 'employee_payment_rates')): ?>
                                <!-- Dynamic Select Handling -->
                                <?php if ($column === 'emp_id' || in_array($column, ['given_by', 'received_by'])): ?>
                                    <select class="select2-dropdown block w-full" name="<?php echo $column; ?>" id="<?php echo $column; ?>">
                                        <option value="">Select Employee</option>
                                        <?php echo $data['tableManager']->getEmployeeOptions(); ?>
                                    </select>
                                <?php elseif ($column === 'job_id'): ?>
                                    <select class="select2-dropdown block w-full" name="<?php echo $column; ?>" id="<?php echo $column; ?>">
                                        <option value="">Select Job</option>
                                        <?php echo $data['tableManager']->getJobDetails(); ?>
                                    </select>
                                <?php elseif ($column === 'project_id' && $data['table'] !== 'projects'): ?>
                                    <select class="select2-dropdown block w-full" name="<?php echo $column; ?>" id="<?php echo $column; ?>">
                                        <option value="">Select Project</option>
                                        <?php echo $data['tableManager']->getProjectDetailsForJobs(); ?>
                                    </select>
                                <?php elseif ($column === 'expenses_category'): ?>
                                    <select class="block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" name="<?php echo $column; ?>" id="<?php echo $column; ?>">
                                        <option value="">Select Category</option>
                                        <?php 
                                        $cats = ['Meals', 'Tools', 'Fuel', 'Materials', 'Hiring of labor', 'Hiring of vehicle', 'Mobile', 'Professional Charges', 'Clearance Charges', 'Documentation', 'Other'];
                                        foreach($cats as $cat) echo "<option value='$cat'>$cat</option>"; 
                                        ?>
                                    </select>
                                <?php else: ?>
                                    <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                <?php endif; ?>

                            <?php elseif ($column === 'presence' || $column === 'paid' || $column === 'payment_type' || $column === 'transaction_type' || $column === 'increment_type' || ($column === 'status' && $data['table'] === 'maintenance_schedule')): ?>
                                <div class="relative">
                                    <select class="block w-full rounded-lg border-slate-300 bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 appearance-none" name="<?php echo $column; ?>" id="<?php echo $column; ?>">
                                        <option value="">Select Option</option>
                                        <?php if ($column === 'presence'): ?>
                                            <option value="1.0">Full Day</option><option value="0.5">Half Day</option><option value="0.0">Not Attended</option>
                                        <?php elseif ($column === 'paid'): ?>
                                            <option value="1">Yes</option><option value="0">No</option>
                                        <?php elseif ($column === 'payment_type'): ?>
                                            <option value="Monthly Salary">Monthly Salary</option><option value="Daily Wage">Daily Wage</option><option value="Advance">Advance</option><option value="Other">Other</option>
                                        <?php elseif ($column === 'transaction_type'): ?>
                                            <option value="In">In (Received)</option><option value="Out">Out (Paid)</option>
                                        <?php elseif ($column === 'status'): ?>
                                            <option value="scheduled">Scheduled</option><option value="completed">Completed</option><option value="overdue">Overdue</option><option value="cancelled">Cancelled</option>
                                        <?php endif; ?>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                    </div>
                                </div>

                            <?php elseif (in_array($column, $dateColumns)): ?>
                                <input type="date" name="<?php echo $column; ?>" id="<?php echo $column; ?>" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                            
                            <?php elseif (strpos($column, 'time') !== false): ?>
                                <input type="time" name="<?php echo $column; ?>" id="<?php echo $column; ?>" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                            
                            <?php else: ?>
                                <input type="text" name="<?php echo $column; ?>" id="<?php echo $column; ?>" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5 placeholder-slate-400" placeholder="Type here...">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/50 rounded-b-2xl">
                <button onclick="closeModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition">Cancel</button>
                <button onclick="saveRecord()" class="px-4 py-2 bg-brand-600 border border-transparent text-white rounded-lg text-sm font-bold shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition">Save Record</button>
            </div>
        </div>
    </div>
    
    <!-- Change Status Modal -->
    <div id="status-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeStatusModal()"></div>
        <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-20 border border-slate-100">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white rounded-t-2xl">
                <h3 class="text-lg font-bold text-slate-900">Update Job Status</h3>
                <button onclick="closeStatusModal()" class="text-slate-400 hover:text-slate-600 transition p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="status-form">
                    <input type="hidden" name="job_id" id="status-job-id">
                    <div class="form-group">
                        <label class="block text-sm font-semibold text-slate-700 mb-3">Select New Status</label>
                        <div class="grid grid-cols-1 gap-3">
                            <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                <input type="radio" name="completion" value="0.0" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300">
                                <span class="ml-3 text-sm font-medium text-slate-700">Not Started (0%)</span>
                            </label>
                            <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                <input type="radio" name="completion" value="0.2" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300">
                                <span class="ml-3 text-sm font-medium text-slate-700">Started / In Progress (20%)</span>
                            </label>
                            <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                <input type="radio" name="completion" value="0.5" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300">
                                <span class="ml-3 text-sm font-medium text-slate-700">Half Way (50%)</span>
                            </label>
                            <label class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
                                <input type="radio" name="completion" value="1.0" class="h-4 w-4 text-green-600 focus:ring-green-500 border-slate-300">
                                <span class="ml-3 text-sm font-medium text-slate-700">Completed (100%)</span>
                            </label>
                            <label class="flex items-center p-3 border border-red-200 bg-red-50 rounded-lg cursor-pointer hover:bg-red-100 transition">
                                <input type="radio" name="completion" value="0.1" class="h-4 w-4 text-red-600 focus:ring-red-500 border-red-300">
                                <span class="ml-3 text-sm font-medium text-red-700">Cancelled / Hold</span>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3 bg-slate-50 rounded-b-2xl">
                <button onclick="closeStatusModal()" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition">Cancel</button>
                <button onclick="saveStatus()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-blue-700 transition">Update Status</button>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="modal-content bg-white rounded-2xl shadow-xl w-full max-w-sm relative z-20 border border-slate-100 overflow-hidden transform transition-all">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Delete Record?</h3>
                <p class="text-sm text-slate-500 mb-6">Are you sure you want to delete this record? This action cannot be undone.</p>
                <div class="flex gap-3 justify-center">
                    <button onclick="closeDeleteModal()" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmDelete()" class="px-5 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition-all transform hover:scale-105">
                        Yes, Delete It
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-24 right-5 z-[60] flex flex-col gap-3 pointer-events-none"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        let table; 
        const tableId = '#data-table';
        const modalId = '#crud-modal';
        const primaryKey = '<?php echo $primaryKey; ?>';
        const tableName = '<?php echo $data['table']; ?>';
        let deleteId = null;
        
        // Add Generate Button for Maintenance
        <?php if ($data['table'] === 'maintenance_schedule'): ?>
        $(document).ready(function() {
             $('.flex.items-center.gap-3').eq(1).prepend(`
                <button onclick="generateSchedule()" class="inline-flex items-center px-4 py-2.5 bg-amber-500 border border-transparent rounded-lg font-semibold text-sm text-white shadow-md hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all mr-2">
                    <i class="fas fa-magic mr-2"></i> Generate Schedule
                </button>
             `);
        });
        <?php endif; ?>

        $(document).ready(function() {
            // Initialize Select2
            $('.select2-dropdown').select2({
                dropdownParent: $(modalId),
                width: '100%',
                placeholder: 'Search and Select...',
                allowClear: true
            });

            // Initialize DataTable
            table = $(tableId).DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: "<?php echo BASE_PATH; ?>/admin/manageTable/" + tableName,
                    type: "POST",
                    data: function(d) {
                         d.action = 'get_records';
                         // Pass custom search input
                         var searchVal = $('#searchInput').val();
                         if(searchVal) {
                             d.search = { terms: searchVal.split(' ') };
                         }
                    }
                },
                columns: [
                    <?php foreach ($data['columns'] as $column): ?>
                        <?php if ($data['table'] === 'jobs' && $column === 'status') continue; ?>
                        { 
                            data: "<?php echo $column; ?>",
                            render: function(data, type, row) {
                                // Custom Renderers based on column name
                                if('<?php echo $column; ?>' === 'paid') {
                                    // Check if data is explicitly 1/Yes OR if it contains the check icon (from backend formatter)
                                    if (data == 1 || data === 'Yes' || String(data).includes('fa-check')) {
                                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i> Paid</span>';
                                    } else {
                                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i> Unpaid</span>';
                                    }
                                }
                                if('<?php echo $column; ?>' === 'completion') {
                                    // Completion Progress Bar or Badge
                                    let val = parseFloat(data) || 0;
                                    let color = 'bg-slate-200';
                                    let text = 'Not Started';
                                    let width = val * 100;
                                    
                                    if(val >= 1.0) { color = 'bg-green-500'; text = 'Completed'; width=100; }
                                    else if(val >= 0.5) { color = 'bg-blue-500'; text = 'In Progress'; }
                                    else if(val >= 0.2) { color = 'bg-blue-400'; text = 'Started'; }
                                    else if(val === 0.1) { color = 'bg-red-400'; text = 'Cancelled'; width=100; }
                                    else { width = 0; }
                                    
                                    if(type === 'display') {
                                        return `<div class="w-full max-w-[140px]">
                                            <div class="flex justify-between text-xs mb-1">
                                                <span class="font-medium text-slate-700">${text}</span>
                                                <span class="text-slate-500">${Math.round(val*100)}%</span>
                                            </div>
                                            <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full ${color} transition-all duration-500" style="width: ${width}%"></div>
                                            </div>
                                        </div>`;
                                    }
                                    return val;
                                }
                                return data ? String(data).substring(0, 50) + (String(data).length > 50 ? '...' : '') : '-';
                            }
                        },
                    <?php endforeach; ?>
                    {
                        data: null,
                        orderable: false,
                        className: 'text-right sticky right-0 bg-white shadow-l p-2',
                        render: function(data, type, row) {
                            let btns = `<div class="flex items-center justify-end gap-2">`;
                            
                            // Edit
                            btns += `<button onclick='openModal("update", ${JSON.stringify(row)})' class="p-2 text-blue-600 hover:bg-blue-50 hover:text-blue-700 rounded-lg transition-all duration-200" title="Edit"><i class="fas fa-edit"></i></button>`;
                            
                            // Job Specifics
                            if(tableName === 'jobs') {
                                btns += `<button onclick='changeStatus("${row[primaryKey]}")' class="p-2 text-amber-600 hover:bg-amber-50 hover:text-amber-700 rounded-lg transition-all duration-200" title="Change Status"><i class="fas fa-tasks"></i></button>`;
                                if(row.has_invoice) {
                                    btns += `<button onclick='viewInvoice("${row[primaryKey]}")' class="p-2 text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 rounded-lg transition-all duration-200" title="See Invoice"><i class="fas fa-file-invoice-dollar"></i></button>`;
                                }
                            }
                            
                            // Operational Expenses Specifics
                            // The backend returns formatted HTML for 'paid' (fa-times icon for No/0)
                            if(tableName === 'operational_expenses' && String(row.paid).includes('fa-times')) {
                                btns += `<button onclick='markAsPaid("${row[primaryKey]}")' class="p-2 text-green-600 hover:bg-green-50 hover:text-green-700 rounded-lg transition-all duration-200" title="Mark as Paid"><i class="fas fa-check-double"></i></button>`;
                            }
                            
                            // Delete

                            btns += `<button onclick='deleteRecord("${row[primaryKey]}")' class="p-2 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-lg transition-all duration-200" title="Delete"><i class="fas fa-trash-alt"></i></button>`;
                            
                            btns += `</div>`;
                            return btns;
                        }
                    }
                ],
                dom: 'rt', // Custom pagination handling
                language: {
                    emptyTable: "No records found matching your search.",
                    paginate: {
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    }
                },
                drawCallback: function(settings) {
                    $('#record-count').text(settings._iRecordsTotal);
                    var info = this.api().page.info();
                    
                    // Update Info Text
                    if(info.recordsTotal > 0) {
                         $('#pagination-info').html(`Showing <span class="font-bold text-slate-700">${info.start+1}</span> to <span class="font-bold text-slate-700">${info.end}</span> of <span class="font-bold text-slate-700">${info.recordsTotal}</span> entries`);
                    } else {
                         $('#pagination-info').text(`No records found`);
                    }

                    // Custom Pagination Controls
                    const pages = info.pages;
                    const page = info.page;
                    
                    let paginationHtml = `<div class="flex items-center gap-1">`;
                    
                    // Previous Button
                    paginationHtml += `<button type="button" ${page === 0 ? 'disabled' : ''} data-page="previous" class="pagination-btn px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium ${page === 0 ? 'bg-slate-50 text-slate-300 cursor-not-allowed' : 'bg-white text-slate-600 hover:bg-slate-50 hover:text-blue-600 shadow-sm transition-all'}"><i class="fas fa-chevron-left"></i></button>`;
                    
                    // Page Numbers
                    let startPage = Math.max(0, page - 2);
                    let endPage = Math.min(pages - 1, page + 2);
                    
                    if(startPage > 0) {
                         paginationHtml += `<button type="button" data-page="0" class="pagination-btn px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium bg-white text-slate-600 hover:bg-slate-50 hover:text-blue-600 shadow-sm transition-all">1</button>`;
                         if(startPage > 1) paginationHtml += `<span class="px-2 text-slate-400">...</span>`;
                    }

                    for(let i = startPage; i <= endPage; i++) {
                        let isActive = i === page;
                        let activeClass = 'border-blue-500 bg-blue-600 text-white hover:bg-blue-700';
                        let inactiveClass = 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50 hover:text-blue-600';
                        paginationHtml += `<button type="button" data-page="${i}" class="pagination-btn px-3 py-2 border rounded-lg text-sm font-medium shadow-sm transition-all ${isActive ? activeClass : inactiveClass}">${i + 1}</button>`;
                    }
                    
                    if(endPage < pages - 1) {
                        if(endPage < pages - 2) paginationHtml += `<span class="px-2 text-slate-400">...</span>`;
                        paginationHtml += `<button type="button" data-page="${pages - 1}" class="pagination-btn px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium bg-white text-slate-600 hover:bg-slate-50 hover:text-blue-600 shadow-sm transition-all">${pages}</button>`;
                    }

                    // Next Button
                    paginationHtml += `<button type="button" ${page === pages - 1 || pages === 0 ? 'disabled' : ''} data-page="next" class="pagination-btn px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium ${page === pages - 1 || pages === 0 ? 'bg-slate-50 text-slate-300 cursor-not-allowed' : 'bg-white text-slate-600 hover:bg-slate-50 hover:text-blue-600 shadow-sm transition-all'}"><i class="fas fa-chevron-right"></i></button>`;
                    
                    paginationHtml += `</div>`;
                    
                    $('#pagination-controls').html(paginationHtml);
                }
            });
            
            // Pagination Click Handler
            $(document).off('click', '.pagination-btn').on('click', '.pagination-btn', function(e) {
                e.preventDefault();
                if($(this).is(':disabled')) return;
                
                let pageVal = $(this).attr('data-page'); // Use attr to get string first
                let action = pageVal;
                
                // Check if it's a number
                if(!isNaN(pageVal) && pageVal !== 'next' && pageVal !== 'previous') {
                    action = parseInt(pageVal);
                }

                // Perform paging
                table.page(action).draw(false); // draw(false) retains paging state but triggers reload
                
                // Optional: Scroll table to top smoothly
                $('html, body').animate({
                    scrollTop: $(tableId).offset().top - 100
                }, 300);
            });
            
            // Custom Search Trigger
            let searchTimeout;
            $('#searchInput').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    table.draw();
                }, 300);
            });
            
            $('#refresh-table').on('click', function() {
                table.draw();
            });
        });

        // Toast Notification System
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            let icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            let color = type === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-red-50 text-red-800 border-red-200';
            let iconColor = type === 'success' ? 'text-emerald-500' : 'text-red-500';
            
            toast.className = `pointer-events-auto flex items-center w-full max-w-xs p-4 space-x-3 text-sm rounded-xl shadow-lg border ${color} transform translate-x-full transition-all duration-300 ease-out`;
            toast.innerHTML = `
                <i class="fas ${icon} ${iconColor} text-lg"></i>
                <div class="flex-1 font-medium">${message}</div>
            `;
            
            container.appendChild(toast);
            
            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full');
            });
            
            // Auto remove
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Modal Functions
        function openModal(action, data = null) {
            $('#form-action').val(action);
            $('#modal-title').html(action === 'create' 
                ? '<i class="fas fa-plus-circle text-blue-500 mr-2"></i> Create New Record' 
                : '<i class="fas fa-edit text-blue-500 mr-2"></i> Edit Record');
            
            // Reset form
            $('#crud-form')[0].reset();
            $('.select2-dropdown').val(null).trigger('change');
            
            // ID Field Handling
            let $idField = $(`[name="${primaryKey}"]`);
            if(action === 'create') {
                $idField.val('Auto-Generated').prop('disabled', true).addClass('bg-slate-50 text-slate-400 italic');
            } else {
                $idField.prop('disabled', false).removeClass('bg-slate-50 text-slate-400 italic').addClass('bg-slate-100').prop('readonly', true);
            }
            
            if(action === 'update' && data) {
                $('#form-id').val(data[primaryKey]);
                $.each(data, function(key, value) {
                    let $el = $(`[name="${key}"]`);
                    if($el.length) {
                        if($el.is('select.select2-dropdown')) {
                            $el.val(value).trigger('change');
                        } else if($el.attr('type') === 'checkbox') {
                             $el.prop('checked', value == 1);
                        } else {
                             if(key !== primaryKey) $el.val(value);
                             else $el.val(value);
                        }
                         
                         let $groupSelect = $(`#group-${key} select`);
                         if($groupSelect.length) {
                             $groupSelect.val(value);
                             $el.val(value); 
                         }
                    }
                });
            }
            $('#crud-modal').addClass('show');
        }

        function closeModal() {
            $('#crud-modal').removeClass('show');
        }

        // Status Modal Functions
        function changeStatus(id) {
            $('#status-job-id').val(id);
            $('input[name="completion"]').prop('checked', false);
            
            $.post("<?php echo BASE_PATH; ?>/admin/manageTable/" + tableName, {
                action: 'get_job_completion',
                job_id: id
            }, function(response) {
                if(response.completion !== undefined) {
                    $(`input[name="completion"][value="${response.completion}"]`).prop('checked', true);
                }
            }, 'json');

            $('#status-modal').addClass('show');
        }

        function closeStatusModal() {
            $('#status-modal').removeClass('show');
        }

        function saveStatus() {
            const formData = $('#status-form').serialize();
            $.post("<?php echo BASE_PATH; ?>/admin/manageTable/" + tableName, formData + '&action=update_status', function(response) {
                if(response.success) {
                    closeStatusModal();
                    table.draw();
                    showToast('Job status updated successfully');
                } else {
                    showToast('Error: ' + response.error, 'error');
                }
            }, 'json');
        }
        
        function generateSchedule() {
            if(!confirm('This will generate maintenance schedules for all active jobs based on their started dates. Continue?')) return;
            
            $.post("<?php echo BASE_PATH; ?>/admin/manageTable/" + tableName, {
                action: 'generate_maintenance'
            }, function(response) {
                if(response.success) {
                    showToast('Schedules generated successfully!');
                    table.draw();
                } else {
                    showToast('Error: ' + (response.error || 'Unknown error'), 'error');
                }
            }, 'json');
        }
        
        function saveRecord() {
            const formData = $('#crud-form').serialize();
            
            $.ajax({
                url: "<?php echo BASE_PATH; ?>/admin/manageTable/" + tableName,
                type: "POST",
                data: formData,
                dataType: "json",
                success: function(response) {
                    if(response.success) {
                        closeModal();
                        table.draw();
                        showToast(response.message || 'Record saved successfully');
                    } else {
                        showToast('Error: ' + (response.error || 'Unknown error occurred'), 'error');
                    }
                },
                error: function(xhr) {
                    showToast('Server Error. Check console.', 'error');
                    console.error(xhr);
                }
            });
        }
        
        function markAsPaid(id) {
            if(!confirm('Are you sure you want to mark this expense as paid?')) return;
            
            $.post("<?php echo BASE_PATH; ?>/admin/manageTable/" + tableName, {
                action: 'mark_as_paid',
                id: id
            }, function(response) {
                if(response.success) {
                    showToast('Expense marked as paid');
                    table.draw();
                } else {
                    showToast('Error: ' + response.error, 'error');
                }
            }, 'json');
        }

        function deleteRecord(id) {
            deleteId = id;
            $('#delete-modal').addClass('show');
        }
        
        function closeDeleteModal() {
            $('#delete-modal').removeClass('show');
            deleteId = null;
        }
        
        function confirmDelete() {
            if(!deleteId) return;
            
            $.post("<?php echo BASE_PATH; ?>/admin/manageTable/" + tableName, {
                action: 'delete',
                id: deleteId
            }, function(response) {
                if(response.success) {
                    closeDeleteModal();
                    table.draw();
                    showToast('Record deleted successfully');
                } else {
                    closeDeleteModal();
                    showToast('Error deleting: ' + response.error, 'error');
                }
            }, 'json');
        }
    </script>
    <script>
        // Invoice View Function
        function viewInvoice(jobId) {
            // Improved Loading State (Skeleton UI)
            let skeletonHtml = `
                <div class="animate-pulse space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="h-32 bg-slate-200 rounded-xl"></div>
                        <div class="h-32 bg-slate-200 rounded-xl"></div>
                    </div>
                    <div class="h-16 bg-slate-200 rounded-xl"></div>
                </div>
            `;
            
            $('#invoice-content').html(skeletonHtml);
            $('#invoice-id-display').text('...');
            $('#invoice-modal').addClass('show');

            $.post("<?php echo BASE_PATH; ?>/admin/manageTable/jobs", {
                action: 'get_invoice_details',
                job_id: jobId
            }, function(response) {
                if(response.error) {
                    $('#invoice-content').html(`<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-circle text-3xl mb-2"></i><p>${response.error}</p></div>`);
                    return;
                }
                
                if(!response.invoice_no) {
                     $('#invoice-content').html(`<div class="text-center py-8 text-slate-400"><i class="fas fa-file-invoice text-3xl mb-2"></i><p>No Invoice generated for this job yet.</p></div>`);
                     return;
                }

                // Populate Data
                $('#invoice-id-display').text(response.invoice_no);
                
                let totalValue = parseFloat(response.invoice_value) || 0;
                let receivedAmount = parseFloat(response.received_amount) || 0;
                let balance = totalValue - receivedAmount;
                
                // Status Determination
                let statusBadge = '';
                if(balance <= 0 && totalValue > 0) {
                     statusBadge = '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold ring-1 ring-green-200">Paid</span>';
                } else if(receivedAmount > 0) {
                     statusBadge = '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold ring-1 ring-blue-200">Partial</span>';
                } else {
                     statusBadge = '<span class="px-2 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold ring-1 ring-amber-200">Pending</span>';
                }

                let html = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden group hover:border-blue-300 transition-colors">
                            <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                                <i class="fas fa-file-invoice text-5xl text-blue-600"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">Invoice Info</h4>
                            <div class="space-y-3 relative z-10">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500 text-sm">Invoice No</span>
                                    <span class="font-bold text-slate-800 font-mono bg-slate-50 px-2 py-0.5 rounded">${response.invoice_no}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500 text-sm">Date Issued</span>
                                    <span class="font-medium text-slate-800">${response.invoice_date || 'N/A'}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500 text-sm">Status</span>
                                    ${statusBadge}
                                </div>
                            </div>
                        </div>

                         <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden group hover:border-emerald-300 transition-colors">
                            <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                                <i class="fas fa-coins text-5xl text-emerald-600"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 border-b border-slate-100 pb-2">Financials</h4>
                            <div class="space-y-3 relative z-10">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500 text-sm">Total Value</span>
                                    <span class="font-bold text-slate-800 text-lg">${totalValue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500 text-sm">Received</span>
                                    <span class="font-medium text-emerald-600">${receivedAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                </div>
                                <div class="flex justify-between items-center border-t border-slate-100 pt-2 mt-1">
                                    <span class="text-slate-500 text-sm font-semibold">Balance Due</span>
                                    <span class="font-bold ${balance > 0 ? 'text-red-500' : 'text-slate-400'} text-lg">${balance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Job & Project</h4>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-sm text-slate-600 leading-relaxed shadow-inner">
                            ${response.job_details && response.job_details.includes(' - ') 
                                ? response.job_details.split(' - ').map(part => `<span class="inline-block bg-white px-2 py-1 rounded border border-slate-200 mr-1 mb-1 shadow-sm">${part.trim()}</span>`).join('') 
                                : '<span class="italic text-slate-400">No job details available</span>'}
                        </div>
                    </div>
                `;
                
                // Invoice File / Attachment
                if(response.invoice) {
                     html += `
                        <div class="mt-6 flex justify-center">
                             <a href="${response.invoice}" target="_blank" class="group relative inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all duration-200 bg-blue-600 font-lg rounded-xl hover:bg-blue-700 hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600">
                                <i class="fas fa-file-pdf mr-2 text-blue-200 group-hover:text-white transition-colors"></i> View Original Invoice
                             </a>
                        </div>
                     `;
                } else {
                     html += `
                        <div class="mt-6 text-center">
                           <span class="inline-flex items-center px-4 py-2 rounded-lg bg-slate-100 text-slate-400 text-sm border border-slate-200">
                                <i class="fas fa-slash mr-2"></i> No Document Attached
                           </span>
                        </div>
                     `;
                }

                $('#invoice-content').html(html);

            }, 'json').fail(function() {
                 $('#invoice-content').html(`<div class="text-center py-8 text-red-500"><i class="fas fa-wifi text-3xl mb-2"></i><p>Network Error. Please try again.</p></div>`);
            });
        }

        function closeInvoiceModal() {
            $('#invoice-modal').removeClass('show');
        }
    </script>
    <!-- Invoice Details Modal -->
    <div id="invoice-modal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeInvoiceModal()"></div>
        <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-2xl relative z-20 border border-slate-100 flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white rounded-t-2xl">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-emerald-500"></i> Invoice <span id="invoice-id-display" class="text-slate-400 font-normal">#...</span>
                    </h3>
                </div>
                <button onclick="closeInvoiceModal()" class="text-slate-400 hover:text-slate-600 transition p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto custom-scrollbar" id="invoice-content">
                <!-- Content Loaded via AJAX -->
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end bg-slate-50 rounded-b-2xl">
                <button onclick="closeInvoiceModal()" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition">Close</button>
            </div>
        </div>
    </div>
</body>
</html>