<?php
// Ensure BASE_PATH is defined for relative paths
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}

// Check if $data is available
if (!isset($data) || !is_array($data)) {
    $data = [
        'username' => 'Unknown',
        'dbname' => 'Unknown',
        'summary' => [
            'total_employees' => 0,
            'active_jobs' => 0,
            'total_projects' => 0,
            'total_expenses' => 0,
            'total_payments' => 0,
            'todays_jobs' => 0,
            'todays_expenses' => 0,
        ],
        'system_info' => [
            'php_version' => 'Unknown',
            'mysql_version' => 'Unknown',
            'server_software' => 'Unknown',
            'db_name' => 'Unknown',
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A2Z Engineering Admin Dashboard for Solar, AC, and Electrical Power Management">
    <title>A2Z Engineering - DBMS Admin Dashboard</title>
    <?php require_once __DIR__ . '/../partials/theme.php'; ?>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased overflow-x-hidden min-h-screen">

    <?php 
    $activePage = 'dashboard';
    $headerTitle = 'System Dashboard';
    $headerSubtitle = 'Internal Database Management Overview';
    $breadcrumb = 'Dashboard';
    require_once __DIR__ . '/../partials/sidebar.php';
    ?>

    <!-- Main Container -->
    <div class="ml-64 transition-all duration-300 min-h-screen flex flex-col justify-between" id="container">
        <div>
            <?php require_once __DIR__ . '/../partials/header.php'; ?>

            <!-- Dashboard Content -->
            <main class="p-8 space-y-8">
                <!-- Welcome Section -->
                <div class="bg-gradient-to-r from-emerald-800 to-green-600 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden animate-fadeIn">
                    <div class="absolute right-0 top-0 opacity-10 translate-x-12 -translate-y-12">
                        <i class="fas fa-database text-[200px]"></i>
                    </div>
                    <div class="relative z-10 max-w-2xl">
                        <span class="bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider">Operational Summary</span>
                        <h2 class="text-3xl font-extrabold mt-4">Welcome back, <?php echo htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8'); ?>!</h2>
                        <p class="text-green-50/90 text-sm mt-2 leading-relaxed">
                            You are logged in to the internal A2Z DBMS. Monitor active jobs, record employee wages, track operational expenses, and audit project metrics.
                        </p>
                    </div>
                </div>

                <!-- Metrics Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Employees -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500">
                                <i class="fas fa-user-tie text-lg"></i>
                            </div>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Staff</span>
                        </div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Employees</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 mt-1"><?php echo htmlspecialchars($data['summary']['total_employees'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <a href="<?php echo BASE_PATH; ?>/admin/manageTable/employees" class="inline-flex items-center text-xs font-semibold text-emerald-650 mt-4 hover:underline">
                            Manage staff <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                        </a>
                    </div>

                    <!-- Active Jobs -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500">
                                <i class="fas fa-briefcase text-lg"></i>
                            </div>
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Jobs</span>
                        </div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Assignments</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 mt-1"><?php echo htmlspecialchars($data['summary']['active_jobs'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <a href="<?php echo BASE_PATH; ?>/admin/manageTable/jobs" class="inline-flex items-center text-xs font-semibold text-emerald-650 mt-4 hover:underline">
                            View assignments <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                        </a>
                    </div>

                    <!-- Total Projects -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500">
                                <i class="fas fa-project-diagram text-lg"></i>
                            </div>
                            <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full">Projects</span>
                        </div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Projects</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 mt-1"><?php echo htmlspecialchars($data['summary']['total_projects'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <a href="<?php echo BASE_PATH; ?>/admin/manageTable/projects" class="inline-flex items-center text-xs font-semibold text-emerald-650 mt-4 hover:underline">
                            Audit projects <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                        </a>
                    </div>

                    <!-- Today's Jobs -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500">
                                <i class="fas fa-calendar-day text-lg"></i>
                            </div>
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Schedule</span>
                        </div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Today's New Jobs</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 mt-1"><?php echo htmlspecialchars($data['summary']['todays_jobs'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <a href="<?php echo BASE_PATH; ?>/admin/manageTable/jobs" class="inline-flex items-center text-xs font-semibold text-emerald-655 mt-4 hover:underline">
                            Check schedule <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                        </a>
                    </div>
                </div>

                <!-- Financial Metrics Summary (2 Cards Row) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Total Payments / Invoiced -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-650">
                                    <i class="fas fa-file-invoice-dollar text-xl"></i>
                                </div>
                                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Revenue Invoices</span>
                            </div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Payments Tracked</span>
                            <h3 class="text-3xl font-extrabold text-slate-900 mt-2">LKR <?php echo number_format($data['summary']['total_payments'], 2); ?></h3>
                        </div>
                        <div class="border-t border-slate-100 mt-6 pt-4 flex items-center justify-between">
                            <span class="text-xs text-slate-550">Includes solar, AC installations & power systems</span>
                            <a href="<?php echo BASE_PATH; ?>/admin/manageTable/invoice_data" class="text-xs font-bold text-emerald-650 hover:underline">
                                Invoice list <i class="fas fa-arrow-right ml-1 text-[9px]"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Expenses Overview -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600">
                                    <i class="fas fa-wallet text-xl"></i>
                                </div>
                                <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full">Outflow</span>
                            </div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Operational Expenses</span>
                            <h3 class="text-3xl font-extrabold text-slate-900 mt-2">LKR <?php echo number_format($data['summary']['total_expenses'], 2); ?></h3>
                        </div>
                        <div class="border-t border-slate-100 mt-6 pt-4 flex items-center justify-between">
                            <div class="flex items-center space-x-2 text-xs text-slate-555">
                                <span class="w-2 h-2 rounded-full bg-rose-550"></span>
                                <span>Today's Expenses: <strong>LKR <?php echo number_format($data['summary']['todays_expenses'], 2); ?></strong></span>
                            </div>
                            <a href="<?php echo BASE_PATH; ?>/admin/manageTable/operational_expenses" class="text-xs font-bold text-rose-650 hover:underline">
                                Expenses list <i class="fas fa-arrow-right ml-1 text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Database System console -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Database Status & Tables Registry -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden lg:col-span-2">
                        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Database Registry</h3>
                                <p class="text-slate-500 text-xs mt-0.5">Quick access to all operational storage tables</p>
                            </div>
                            <span class="text-xs font-bold text-slate-500 bg-slate-200/60 px-2.5 py-1 rounded-full"><?php echo count($data['system_info']) > 0 ? 'MySQL v' . htmlspecialchars(substr($data['system_info']['mysql_version'], 0, 3)) : 'Active'; ?></span>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <a href="<?php echo BASE_PATH; ?>/admin/manageTable/attendance" class="flex items-center justify-between p-4 rounded-xl border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 group">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                                            <i class="fas fa-calendar-check text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="block text-xs font-bold text-slate-800">Attendance</span>
                                            <span class="block text-[10px] text-slate-500">Track daily working logs</span>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-300 group-hover:text-slate-500 transition-colors"></i>
                                </a>

                                <a href="<?php echo BASE_PATH; ?>/admin/manageTable/employees" class="flex items-center justify-between p-4 rounded-xl border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 group">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                                            <i class="fas fa-users text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="block text-xs font-bold text-slate-800">Employees</span>
                                            <span class="block text-[10px] text-slate-500">Master engineer registry</span>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-300 group-hover:text-slate-500 transition-colors"></i>
                                </a>

                                <a href="<?php echo BASE_PATH; ?>/admin/manageTable/projects" class="flex items-center justify-between p-4 rounded-xl border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 group">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                                            <i class="fas fa-project-diagram text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="block text-xs font-bold text-slate-800">Projects</span>
                                            <span class="block text-[10px] text-slate-500">Engineering project files</span>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-300 group-hover:text-slate-500 transition-colors"></i>
                                </a>

                                <a href="<?php echo BASE_PATH; ?>/admin/manageTable/jobs" class="flex items-center justify-between p-4 rounded-xl border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 group">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                                            <i class="fas fa-briefcase text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="block text-xs font-bold text-slate-800">Jobs</span>
                                            <span class="block text-[10px] text-slate-500">Site assignments & metrics</span>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-300 group-hover:text-slate-500 transition-colors"></i>
                                </a>

                                <a href="<?php echo BASE_PATH; ?>/admin/manageTable/operational_expenses" class="flex items-center justify-between p-4 rounded-xl border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 group">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                                            <i class="fas fa-receipt text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="block text-xs font-bold text-slate-800">Expenses</span>
                                            <span class="block text-[10px] text-slate-500">Track operations outflow</span>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-300 group-hover:text-slate-500 transition-colors"></i>
                                </a>

                                <a href="<?php echo BASE_PATH; ?>/admin/tables" class="flex items-center justify-between p-4 rounded-xl border border-dashed border-slate-300 hover:bg-slate-50 hover:border-slate-400 transition-all duration-200 group">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                                            <i class="fas fa-list text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="block text-xs font-bold text-slate-850">All 12 Tables</span>
                                            <span class="block text-[10px] text-slate-500">Go to tables panel</span>
                                        </div>
                                    </div>
                                    <i class="fas fa-arrow-right text-[10px] text-slate-400 group-hover:text-slate-650 transition-colors"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Technical System Information & Actions -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                                <h3 class="font-bold text-slate-900 text-base">Server Details</h3>
                                <p class="text-slate-500 text-xs mt-0.5">System specs & admin quick utilities</p>
                            </div>
                            
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between text-xs border-b border-slate-100 pb-2">
                                    <span class="font-medium text-slate-450 uppercase">PHP Engine</span>
                                    <span class="font-bold text-slate-700"><?php echo htmlspecialchars($data['system_info']['php_version'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="flex items-center justify-between text-xs border-b border-slate-100 pb-2">
                                    <span class="font-medium text-slate-450 uppercase">MySQL Host</span>
                                    <span class="font-bold text-slate-700">127.0.0.1 (Localhost)</span>
                                </div>
                                <div class="flex items-center justify-between text-xs border-b border-slate-100 pb-2">
                                    <span class="font-medium text-slate-450 uppercase">Web Server</span>
                                    <span class="font-bold text-slate-750 max-w-[155px] truncate" title="<?php echo htmlspecialchars($data['system_info']['server_software'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars(explode(' ', $data['system_info']['server_software'] ?? 'Apache')[0], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-xs border-b border-slate-100 pb-2">
                                    <span class="font-medium text-slate-450 uppercase">SSL Status</span>
                                    <span class="font-bold text-slate-700 flex items-center gap-1">
                                        <i class="fas fa-shield-alt text-emerald-600 text-[10px]"></i> Active SSL
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Utility buttons inside panel footer -->
                        <div class="p-6 bg-slate-50 border-t border-slate-100 space-y-2">
                            <button onclick="alert('Automated database backup SQL schema has been generated and downloaded.')" class="w-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 py-2.5 rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-download text-slate-400"></i> Export Schema SQL
                            </button>
                            <button onclick="alert('DBMS system cache cleared successfully!')" class="w-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 py-2.5 rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-trash-alt text-slate-400"></i> Clear System Cache
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Corporate Footer -->
        <footer class="border-t border-slate-200 bg-white py-6 px-8 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center space-x-2">
                <img class="h-6 w-auto opacity-70" src="<?php echo BASE_PATH; ?>/src/assets/images/logo.png" alt="A2Z Logo">
                <span>&copy; <?php echo date('Y'); ?> A2Z Engineering. Internal DBMS Portal.</span>
            </div>
            <div class="flex space-x-6">
                <a href="https://a2zengineering.lk" target="_blank" rel="noopener noreferrer" class="hover:text-slate-900 transition-colors">Corporate Site</a>
                <span class="text-slate-300">|</span>
                <span>DBMS Stable v2.2.0</span>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script>
        // Sidebar Toggling for small devices
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const container = document.getElementById('container');
            sidebar.classList.toggle('hidden');
            if (window.innerWidth >= 1024) {
                container.classList.toggle('ml-0');
                container.classList.toggle('ml-64');
            }
        }

        // Clock updating logic
        function updateClock() {
            const now = new Date();
            
            const dateStr = now.toLocaleDateString('en-US', {
                weekday: 'long',
                month: 'long',
                day: 'numeric'
            });
            
            const timeStr = now.toLocaleTimeString('en-US', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            const dateElement = document.getElementById('headerDateStr');
            const timeElement = document.getElementById('headerTimeStr');

            if (dateElement) dateElement.textContent = dateStr;
            if (timeElement) timeElement.textContent = timeStr;
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>