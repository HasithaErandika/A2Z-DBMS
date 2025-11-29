<?php
// Ensure BASE_PATH is defined for relative paths
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS'); // Matches index.php
}

// Define FULL_BASE_URL for absolute links, matching index.php
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
}

// Check if $data is available (passed from AdminController::dashboard)
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
    error_log("Warning: \$data not provided to dashboard.php. Using fallback values.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A2Z Engineering Admin Dashboard for Solar, AC, and Electrical Power Management">
    <title>A2Z Engineering - DBMS Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_PATH; ?>/src/assets/css/tailwind.css" rel="stylesheet">
</head>
<body class="font-poppins bg-gray-50 text-gray-900 leading-relaxed overflow-x-hidden">
    <!-- Enhanced Sidebar -->
    <div class="sidebar bg-white w-64 h-screen fixed left-0 top-0 shadow-xl z-50 flex flex-col transition-all duration-300" id="sidebar">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center space-x-3 mb-2">
                <div class="bg-gradient-to-br from-blue-900 to-blue-500 w-12 h-12 rounded-lg flex items-center justify-center">
                    <i class="fas fa-database text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-lg bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent">A2Z Engineering</h2>
                    <p class="text-xs text-gray-500">Internal Database System</p>
                </div>
            </div>
        </div>
        
        <nav class="flex-1 py-4">
            <ul class="space-y-1 px-4">
                <li>
                    <a href="<?php echo BASE_PATH; ?>/admin/dashboard" class="flex items-center space-x-3 p-3 rounded-lg bg-gradient-to-br from-blue-900 to-blue-500 text-white border-l-4 border-blue-700">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_PATH; ?>/admin/tables" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-table"></i>
                        <span>Data Tables</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_PATH; ?>/admin/reports" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li class="mt-8">
                    <a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/logout', ENT_QUOTES, 'UTF-8'); ?>" class="flex items-center space-x-3 p-3 rounded-lg text-red-600 hover:bg-red-50 transition-colors duration-200">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Enhanced Main Container -->
    <div class="ml-64 transition-all duration-300" id="container">
        <!-- Enhanced Dashboard Header -->
        <div class="bg-gradient-to-br from-blue-900 to-blue-500 p-8 rounded-b-xl shadow-2xl flex items-center justify-between mb-10 text-white relative overflow-hidden">
            <div class="flex items-center space-x-4 z-10">
                <button class="lg:hidden btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <!-- Custom Design Element -->
                <div class="relative">
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full w-12 h-12 flex items-center justify-center shadow-lg">
                        <div class="bg-white bg-opacity-30 rounded-full w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-database text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="absolute -top-1 -right-1 bg-yellow-400 rounded-full w-5 h-5 flex items-center justify-center shadow">
                        <i class="fas fa-users text-white text-xs"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-3xl font-semibold">System Dashboard</h1>
                    <p class="text-blue-100 mt-1">Internal Database Management Overview</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-4 z-10">
                <div class="hidden md:flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                    <i class="fas fa-user-circle text-xl"></i>
                    <div>
                        <div class="font-medium text-sm"><?php echo htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="text-xs text-blue-100">Database: <?php echo htmlspecialchars($data['dbname'], ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced DateTime Display -->
        <div class="px-8 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl flex items-center space-x-4 max-w-md">
                <div class="bg-gradient-to-br from-blue-900 to-blue-500 w-12 h-12 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <div class="text-gray-500 text-sm" id="currentDate"><?php echo date('l, F j, Y'); ?></div>
                    <div class="text-2xl font-bold text-gray-900" id="currentTime"><?php echo date('H:i:s'); ?></div>
                </div>
                <!-- Custom Design Element Next to Clock -->
                <div class="relative">
                    <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-full w-12 h-12 flex items-center justify-center shadow-lg">
                        <div class="bg-white bg-opacity-30 rounded-full w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="absolute -bottom-1 -left-1 bg-purple-500 rounded-full w-5 h-5 flex items-center justify-center shadow">
                        <i class="fas fa-cog text-white text-xs"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Summary Overview -->
        <div class="px-8 mb-10">
            <div class="bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div class="flex items-center space-x-3 mb-4 md:mb-0">
                        <i class="fas fa-chart-pie text-blue-600 text-xl"></i>
                        <div>
                            <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent">System Overview</h2>
                            <p class="text-gray-500 text-sm">Key metrics and performance indicators</p>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Employees -->
                    <div class="bg-white p-5 rounded-xl shadow-md transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-blue-500">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Employees</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">
                            <?php echo htmlspecialchars($data['summary']['total_employees'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/manageTable/employees" class="text-blue-600 text-sm font-medium flex items-center mt-3 hover:text-blue-800 transition-colors">
                            View All Employees <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>

                    <!-- Active Jobs -->
                    <div class="bg-white p-5 rounded-xl shadow-md transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-green-500">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-green-100 p-3 rounded-lg text-green-600">
                                <i class="fas fa-project-diagram text-xl"></i>
                            </div>
                            <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">Active Jobs</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">
                            <?php echo htmlspecialchars($data['summary']['active_jobs'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/manageTable/jobs" class="text-green-600 text-sm font-medium flex items-center mt-3 hover:text-green-800 transition-colors">
                            View All Jobs <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>

                    <!-- Total Projects -->
                    <div class="bg-white p-5 rounded-xl shadow-md transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-blue-500">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                                <i class="fas fa-tasks text-xl"></i>
                            </div>
                            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Projects</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">
                            <?php echo htmlspecialchars($data['summary']['total_projects'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/manageTable/projects" class="text-blue-600 text-sm font-medium flex items-center mt-3 hover:text-blue-800 transition-colors">
                            View All Projects <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>

                    <!-- Total Expenses -->
                    <div class="bg-white p-5 rounded-xl shadow-md transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-amber-500">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-amber-100 p-3 rounded-lg text-amber-600">
                                <i class="fas fa-money-bill-wave text-xl"></i>
                            </div>
                            <span class="bg-amber-500 text-white text-xs px-2 py-1 rounded-full">Expenses</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">
                            LKR <?php echo number_format($data['summary']['total_expenses'], 2); ?>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/manageTable/operational_expenses" class="text-amber-600 text-sm font-medium flex items-center mt-3 hover:text-amber-800 transition-colors">
                            View Expense Report <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>

                    <!-- Total Payments -->
                    <div class="bg-white p-5 rounded-xl shadow-md transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-green-500">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-green-100 p-3 rounded-lg text-green-600">
                                <i class="fas fa-credit-card text-xl"></i>
                            </div>
                            <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">Invoice Data</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">
                            LKR <?php echo number_format($data['summary']['total_payments'], 2); ?>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/manageTable/invoice_data" class="text-green-600 text-sm font-medium flex items-center mt-3 hover:text-green-800 transition-colors">
                            View Payment Records <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>

                    <!-- Today's Jobs -->
                    <div class="bg-white p-5 rounded-xl shadow-md transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-indigo-500">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-indigo-100 p-3 rounded-lg text-indigo-600">
                                <i class="fas fa-briefcase text-xl"></i>
                            </div>
                            <span class="bg-indigo-500 text-white text-xs px-2 py-1 rounded-full">Today's Jobs</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">
                            <?php echo htmlspecialchars($data['summary']['todays_jobs'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/manageTable/jobs" class="text-indigo-600 text-sm font-medium flex items-center mt-3 hover:text-indigo-800 transition-colors">
                            View Today's Jobs <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>

                    <!-- Today's Expenses -->
                    <div class="bg-white p-5 rounded-xl shadow-md transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-red-500">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-red-100 p-3 rounded-lg text-red-600">
                                <i class="fas fa-receipt text-xl"></i>
                            </div>
                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">Today's Expenses</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900 mb-1">
                            LKR <?php echo number_format($data['summary']['todays_expenses'], 2); ?>
                        </div>
                        <a href="<?php echo BASE_PATH; ?>/manageTable/operational_expenses" class="text-red-600 text-sm font-medium flex items-center mt-3 hover:text-red-800 transition-colors">
                            View Today's Expenses <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced System Information -->
        <div class="px-8 mb-10">
            <div class="bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl">
                <div class="flex items-center space-x-3 mb-6">
                    <i class="fas fa-server text-blue-600 text-xl"></i>
                    <div>
                        <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent">System Information</h2>
                        <p class="text-gray-500 text-sm">Technical details and system specifications</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-5 rounded-xl shadow-md transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-t-4 border-blue-500">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                                <i class="fas fa-server text-xl"></i>
                            </div>
                            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Server</span>
                        </div>
                        <div class="text-lg font-semibold text-gray-900 mb-2">Production Server</div>
                        <div class="text-sm text-gray-600">Status: <span class="text-green-600 font-medium">Operational</span></div>
                    </div>

                    <div class="bg-white p-5 rounded-xl shadow-md transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-t-4 border-blue-500">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                                <i class="fas fa-database text-xl"></i>
                            </div>
                            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Database</span>
                        </div>
                        <div class="text-lg font-semibold text-gray-900 mb-2">MySQL 8.0</div>
                        <div class="text-sm text-gray-600">Status: <span class="text-green-600 font-medium">Connected</span></div>
                    </div>

                    <div class="bg-white p-5 rounded-xl shadow-md transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-t-4 border-blue-500">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                                <i class="fas fa-shield-alt text-xl"></i>
                            </div>
                            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Security</span>
                        </div>
                        <div class="text-lg font-semibold text-gray-900 mb-2">SSL Enabled</div>
                        <div class="text-sm text-gray-600">Status: <span class="text-green-600 font-medium">Secure</span></div>
                    </div>

                    <div class="bg-white p-5 rounded-xl shadow-md transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-t-4 border-blue-500">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Uptime</span>
                        </div>
                        <div class="text-lg font-semibold text-gray-900 mb-2">99.9%</div>
                        <div class="text-sm text-gray-600">Status: <span class="text-green-600 font-medium">Stable</span></div>
                    </div>
                </div>
                
                <!-- Additional Custom Design Elements -->
                <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200">
                        <div class="flex items-center space-x-4">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-700 w-12 h-12 rounded-lg flex items-center justify-center text-white">
                                <i class="fas fa-sync-alt text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Auto Backup</h4>
                                <p class="text-sm text-gray-600">Daily automated backups</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-teal-50 p-6 rounded-xl border border-green-200">
                        <div class="flex items-center space-x-4">
                            <div class="bg-gradient-to-br from-green-500 to-green-700 w-12 h-12 rounded-lg flex items-center justify-center text-white">
                                <i class="fas fa-bell text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Notifications</h4>
                                <p class="text-sm text-gray-600">Real-time alerts system</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-6 rounded-xl border border-purple-200">
                        <div class="flex items-center space-x-4">
                            <div class="bg-gradient-to-br from-purple-500 to-purple-700 w-12 h-12 rounded-lg flex items-center justify-center text-white">
                                <i class="fas fa-shield-alt text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Encryption</h4>
                                <p class="text-sm text-gray-600">AES-256 data protection</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced sidebar functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const container = document.getElementById('container');
            sidebar.classList.toggle('lg:block');
            sidebar.classList.toggle('hidden');
            container.classList.toggle('ml-0');
            container.classList.toggle('ml-64');
        }

        // Enhanced date/time updates
        function updateDateTime() {
            const now = new Date();
            
            // Update date
            const currentDate = document.getElementById('currentDate');
            if (currentDate) {
                currentDate.textContent = now.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
            
            // Update time
            const currentTime = document.getElementById('currentTime');
            if (currentTime) {
                currentTime.textContent = now.toLocaleTimeString('en-US', {
                    hour12: false,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }
        }

        // Update time every second
        setInterval(updateDateTime, 1000);
        updateDateTime();

        // Enhanced card interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Menu item interactions
            document.querySelectorAll('a[href]').forEach(link => {
                link.addEventListener('click', function() {
                    // Remove active class from all menu items
                    document.querySelectorAll('a[href]').forEach(item => {
                        item.classList.remove('bg-gradient-to-br', 'from-blue-900', 'to-blue-500', 'text-white', 'border-l-4', 'border-blue-700');
                        item.classList.add('text-gray-700', 'hover:bg-gray-100');
                    });
                    
                    // Add active class to clicked item
                    this.classList.remove('text-gray-700', 'hover:bg-gray-100');
                    this.classList.add('bg-gradient-to-br', 'from-blue-900', 'to-blue-500', 'text-white', 'border-l-4', 'border-blue-700');
                });
            });
        });
    </script>
</body>
</html>