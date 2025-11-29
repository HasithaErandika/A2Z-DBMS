<?php
// Ensure BASE_PATH is defined for relative paths
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}

// Define FULL_BASE_URL for absolute links
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'http://localhost/A2Z-DBMS/');
}

// Check if $data is available (passed from AdminController::records)
if (!isset($data) || !is_array($data)) {
    $data = [
        'username' => 'Unknown',
        'dbname' => 'Unknown',
        'reportCards' => []
    ];
    error_log("Warning: \$data not provided to reports.php. Using fallback values.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A2Z Engineering Internal Database System - Reports and Analytics">
    <title>A2Z Engineering - Reports</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/admin/dashboard', ENT_QUOTES, 'UTF-8'); ?>" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/admin/tables', ENT_QUOTES, 'UTF-8'); ?>" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-table"></i>
                        <span>Data Tables</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>" class="flex items-center space-x-3 p-3 rounded-lg bg-gradient-to-br from-blue-900 to-blue-500 text-white">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li class="mt-8">
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/logout', ENT_QUOTES, 'UTF-8'); ?>" class="flex items-center space-x-3 p-3 rounded-lg text-red-600 hover:bg-red-50 transition-colors duration-200">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Container -->
    <div class="ml-64 transition-all duration-300" id="container">
        <!-- Enhanced Dashboard Header -->
        <div class="bg-gradient-to-br from-blue-900 to-blue-500 p-8 rounded-b-xl shadow-2xl flex items-center justify-between mb-10 text-white relative overflow-hidden">
            <div class="flex items-center space-x-4 z-10">
                <button class="lg:hidden btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="text-3xl font-semibold">Reports & Analytics</h1>
                    <p class="text-blue-100 mt-1">Comprehensive Business Intelligence Reports</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-4 z-10">
                <div class="hidden md:flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                    <i class="fas fa-user-circle text-xl"></i>
                    <div>
                        <div class="font-medium text-sm"><?php echo htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="text-xs text-blue-100">Administrator</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DateTime Display -->
        <div class="px-8 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl flex items-center space-x-4 max-w-md">
                <div class="bg-gradient-to-br from-blue-900 to-blue-500 w-12 h-12 rounded-lg flex items-center justify-center text-white">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <div class="text-gray-500 text-sm" id="current-date"><?php echo date('l, F j, Y'); ?></div>
                    <div class="text-2xl font-bold text-gray-900" id="current-time"><?php echo date('H:i:s'); ?></div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="px-8 pb-10">
            <div class="bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div class="flex items-center space-x-3 mb-4 md:mb-0">
                        <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                        <div>
                            <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent">Business Reports</h2>
                            <p class="text-gray-500 text-sm">Financial, operational, and performance analytics</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <button class="bg-transparent text-blue-900 border-2 border-blue-900 px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:bg-blue-900 hover:text-white hover:translate-y-[-2px]">
                            <i class="fas fa-download"></i> Export All
                        </button>
                        <button class="bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30">
                            <i class="fas fa-plus"></i> New Report
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8" id="reports-grid">
                    <?php if (empty($data['reportCards'])): ?>
                        <div class="col-span-full text-center py-12">
                            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                                <i class="fas fa-chart-bar text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Reports Available</h3>
                            <p class="text-gray-500 mb-6">Create your first report to get started with analytics</p>
                            <button class="bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 mx-auto hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30">
                                <i class="fas fa-plus"></i> Create Report
                            </button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($data['reportCards'] as $card): ?>
                            <div class="bg-white p-6 rounded-xl shadow-md transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border border-gray-200">
                                <a href="<?php echo htmlspecialchars($card['link'], ENT_QUOTES, 'UTF-8'); ?>" class="block">
                                    <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center text-blue-600 mb-4">
                                        <i class="fas <?php echo htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8'); ?> text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars($card['desc'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <div class="flex items-center text-blue-600 text-sm font-medium hover:text-blue-800 transition-colors">
                                        View Report <i class="fas fa-arrow-right ml-2 text-xs"></i>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Professional Content Section -->
            <div class="mt-8 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl shadow-lg">
                <div class="flex items-start space-x-4">
                    <div class="bg-gradient-to-br from-blue-900 to-blue-500 w-12 h-12 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                        <i class="fas fa-lightbulb text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Data-Driven Decision Making</h3>
                        <p class="text-gray-700 mb-4">
                            Our comprehensive reporting system provides actionable insights into your business operations. 
                            Track financial performance, monitor operational efficiency, and identify growth opportunities 
                            through detailed analytics and visualizations.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-blue-600 font-semibold mb-1">Financial Reports</div>
                                <div class="text-sm text-gray-600">Track revenue, expenses, and profitability</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-green-600 font-semibold mb-1">Operational Metrics</div>
                                <div class="text-sm text-gray-600">Monitor efficiency and productivity</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <div class="text-purple-600 font-semibold mb-1">Performance Analytics</div>
                                <div class="text-sm text-gray-600">Measure KPIs and business outcomes</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced sidebar toggle
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
            const dateElement = document.getElementById('current-date');
            if (dateElement) {
                dateElement.textContent = now.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
            
            // Update time
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.textContent = now.toLocaleTimeString('en-US', {
                    hour12: false,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }
        }

        // Update date/time every second
        setInterval(updateDateTime, 1000);
        updateDateTime();

        // Enhanced card interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Menu item interactions
            document.querySelectorAll('a[href]').forEach(link => {
                link.addEventListener('click', function() {
                    // Remove active class from all menu items
                    document.querySelectorAll('a[href]').forEach(item => {
                        item.classList.remove('bg-gradient-to-br', 'from-blue-900', 'to-blue-500', 'text-white');
                        item.classList.add('text-gray-700', 'hover:bg-gray-100');
                    });
                    
                    // Add active class to clicked item
                    this.classList.remove('text-gray-700', 'hover:bg-gray-100');
                    this.classList.add('bg-gradient-to-br', 'from-blue-900', 'to-blue-500', 'text-white');
                });
            });
        });
    </script>
</body>
</html>