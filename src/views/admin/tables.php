<?php
// Ensure BASE_PATH is defined for relative paths
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}

// Define FULL_BASE_URL for absolute links
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'http://localhost/A2Z-DBMS/');
}

// Check if $data is available (passed from AdminController::tables)
if (!isset($data) || !is_array($data)) {
    $data = [
        'username' => 'Administrator',
        'message' => '',
        'error' => '',
        'operationalCards' => []
    ];
    error_log("Warning: \$data not provided to tables.php. Using fallback values.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A2Z Engineering Internal Database System - Data Tables Management">
    <title>A2Z Engineering - Data Tables</title>
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
                    <a href="<?php echo BASE_PATH; ?>/admin/dashboard" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_PATH; ?>/admin/tables" class="flex items-center space-x-3 p-3 rounded-lg bg-gradient-to-br from-blue-900 to-blue-500 text-white border-l-4 border-blue-700">
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
                    <a href="<?php echo BASE_PATH; ?>/logout" class="flex items-center space-x-3 p-3 rounded-lg text-red-600 hover:bg-red-50 transition-colors duration-200">
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
                    <h1 class="text-3xl font-semibold">Data Tables</h1>
                    <p class="text-blue-100 mt-1">Comprehensive Data Management System</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-4 z-10">
                <div class="hidden md:flex items-center space-x-2 bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                    <i class="fas fa-user-circle text-xl"></i>
                    <div>
                        <div class="font-medium text-sm"><?php echo htmlspecialchars($data['username'] ?? 'Administrator'); ?></div>
                        <div class="text-xs text-blue-100">System Admin</div>
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

        <!-- System Messages -->
        <?php if (!empty($data['message'])): ?>
            <div class="px-8 mb-6">
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center space-x-2">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo htmlspecialchars($data['message']); ?></span>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($data['error'])): ?>
            <div class="px-8 mb-6">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center space-x-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?php echo htmlspecialchars($data['error']); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Content -->
        <div class="px-8 pb-10">
            <div class="bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div class="flex items-center space-x-3 mb-4 md:mb-0">
                        <i class="fas fa-database text-blue-600 text-xl"></i>
                        <div>
                            <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent">Data Management</h2>
                            <p class="text-gray-500 text-sm">Access and manage internal company data tables</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
                        <input type="text" class="border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition-all" placeholder="Search tables..." onkeyup="filterCards(this, 'operational')" aria-label="Search operational data tables">
                        <button class="bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30">
                            <i class="fas fa-plus"></i> Add Table
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8" id="operational-grid">
                    <?php if (empty($data['operationalCards'] ?? [])): ?>
                        <div class="col-span-full text-center py-12">
                            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                                <i class="fas fa-table text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Data Tables Available</h3>
                            <p class="text-gray-500 mb-6">Create your first data table to start managing company information</p>
                            <button class="bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 mx-auto hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30">
                                <i class="fas fa-plus"></i> Create Table
                            </button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($data['operationalCards'] as $card): ?>
                            <div class="bg-white p-6 rounded-xl shadow-md transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border border-gray-200 hover:border-blue-300">
                                <a href="<?php echo htmlspecialchars(BASE_PATH . $card['link'], ENT_QUOTES, 'UTF-8'); ?>" class="block">
                                    <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center text-blue-600 mb-4">
                                        <i class="fas <?php echo htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8'); ?> text-xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars($card['desc'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <div class="flex items-center text-blue-600 text-sm font-medium hover:text-blue-800 transition-colors">
                                        View Table <i class="fas fa-arrow-right ml-2 text-xs"></i>
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
                        <i class="fas fa-database text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Enterprise Data Management</h3>
                        <p class="text-gray-700 mb-4">
                            Our robust data management system ensures secure storage, efficient retrieval, and seamless 
                            integration of all your business data. With advanced filtering and search capabilities, 
                            you can quickly access the information you need to make informed decisions.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="text-blue-600 font-semibold mb-1">Data Security</div>
                                <div class="text-sm text-gray-600">Enterprise-grade encryption and access controls</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="text-green-600 font-semibold mb-1">Scalability</div>
                                <div class="text-sm text-gray-600">Handle growing data volumes with ease</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="text-purple-600 font-semibold mb-1">Performance</div>
                                <div class="text-sm text-gray-600">Fast queries and real-time data access</div>
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

        // Enhanced card filtering
        function filterCards(input, gridId) {
            const searchTerm = input.value.toLowerCase().trim();
            const grid = document.getElementById(`${gridId}-grid`);
            const cards = grid.getElementsByClassName('bg-white');
            
            Array.from(cards).forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const desc = card.querySelector('p').textContent.toLowerCase();
                const shouldShow = title.includes(searchTerm) || desc.includes(searchTerm);
                
                card.style.display = shouldShow ? 'block' : 'none';
            });
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

        // Enhanced interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Update date/time every second
            setInterval(updateDateTime, 1000);
            updateDateTime();
            
            // Menu item active state management
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

            // Auto-hide messages after 5 seconds
            setTimeout(() => {
                const messages = document.querySelectorAll('[class*="bg-"][class*="-50"]');
                messages.forEach(message => {
                    message.style.transition = 'opacity 0.3s';
                    message.style.opacity = '0';
                    setTimeout(() => message.remove(), 300);
                });
            }, 5000);
        });
    </script>
</body>
</html>