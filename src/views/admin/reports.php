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
                <!-- Added logo image -->
                <img src="<?php echo BASE_PATH; ?>/src/assets/images/logo.png" alt="A2Z Engineering Logo" class="w-12 h-12 object-contain">
                <div>
                    <h2 class="font-bold text-lg bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent">A2Z Engineering</h2>
                    <!-- Updated text to full system name -->
                    <p class="text-xs text-gray-500">Internal Database System</p>
                </div>
            </div>
        </div>
        
        <nav class="flex-1 py-4">
            <ul class="space-y-1 px-4">
                <li>
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/admin/dashboard', ENT_QUOTES, 'UTF-8'); ?>" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-indigo-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/admin/tables', ENT_QUOTES, 'UTF-8'); ?>" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-indigo-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <i class="fas fa-table"></i>
                        <span>Data Tables</span>
                    </a>
                </li>
                <li>
                    <!-- Enhanced active bar design with custom border style -->
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>" class="flex items-center space-x-3 p-3 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-900 border-l-4 border-blue-600 shadow-sm relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-indigo-500/10 opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                        <i class="fas fa-chart-bar"></i>
                        <span class="font-medium">Reports</span>
                        <!-- Custom design element for active state -->
                        <div class="absolute right-2 top-1/2 transform -translate-y-1/2 w-2 h-2 rounded-full bg-blue-500"></div>
                    </a>
                </li>
                <li class="mt-8">
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/logout', ENT_QUOTES, 'UTF-8'); ?>" class="flex items-center space-x-3 p-3 rounded-lg text-red-600 hover:bg-red-50 transition-colors duration-200 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-r from-red-500/5 to-orange-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
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
    <div class="bg-gradient-to-br from-orange-900 to-blue-500 p-8 rounded-b-xl shadow-2xl flex items-center justify-between mb-10 text-white relative overflow-hidden">
        <div class="flex items-center space-x-4 z-10">
            <button class="lg:hidden btn bg-gradient-to-br from-orange-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-orange-900/30" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Custom Design Element -->
            <div class="relative">
                <div class="bg-gradient-to-br from-orange-500 to-blue-600 rounded-full w-12 h-12 flex items-center justify-center shadow-lg">
                    <div class="bg-white bg-opacity-30 rounded-full w-8 h-8 flex items-center justify-center">
                        <i class="fas fa-chart-bar text-white text-lg"></i>
                    </div>
                </div>
                <div class="absolute -top-1 -right-1 bg-blue-400 rounded-full w-5 h-5 flex items-center justify-center shadow">
                    <i class="fas fa-chart-line text-white text-xs"></i>
                </div>
            </div>
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
    <!-- Enhanced DateTime Display -->
    <div class="px-8 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl flex items-center space-x-4 max-w-md">
            <div class="bg-gradient-to-br from-orange-900 to-blue-500 w-12 h-12 rounded-lg flex items-center justify-center text-white">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div>
                <div class="text-gray-500 text-sm" id="current-date"><?php echo date('l, F j, Y'); ?></div>
                <div class="text-2xl font-bold text-gray-900" id="current-time"><?php echo date('H:i:s'); ?></div>
            </div>
            <!-- Custom Design Element Next to Clock -->
            <div class="relative">
                <div class="bg-gradient-to-br from-orange-500 to-blue-600 rounded-full w-12 h-12 flex items-center justify-center shadow-lg">
                    <div class="bg-white bg-opacity-30 rounded-full w-8 h-8 flex items-center justify-center">
                        <i class="fas fa-file-alt text-white text-lg"></i>
                    </div>
                </div>
                <div class="absolute -bottom-1 -left-1 bg-orange-500 rounded-full w-5 h-5 flex items-center justify-center shadow">
                    <i class="fas fa-cog text-white text-xs"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content -->
    <div class="px-8 pb-10">
        <div class="bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                    <div>
                        <h2 class="text-xl font-semibold bg-gradient-to-br from-orange-900 to-blue-500 bg-clip-text text-transparent">Business Reports</h2>
                        <p class="text-gray-500 text-sm">Financial, operational, and performance analytics</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button class="bg-transparent text-orange-900 border-2 border-orange-900 px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:bg-orange-900 hover:text-white hover:translate-y-[-2px]">
                        <i class="fas fa-download"></i> Export All
                    </button>
                    <button class="bg-gradient-to-br from-orange-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-orange-900/30">
                        <i class="fas fa-plus"></i> New Report
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8" id="reports-grid">
                <?php if (empty($data['reportCards'])): ?>
                    <div class="col-span-full bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
                        <div class="bg-gradient-to-br from-orange-100 to-blue-100 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-chart-bar text-3xl text-orange-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">No Reports Available</h3>
                        <p class="text-gray-600 mb-8 max-w-md mx-auto">Create your first report to get started with analytics and gain valuable insights into your business operations.</p>
                        <button class="bg-gradient-to-br from-orange-600 to-blue-700 text-white px-6 py-3 rounded-xl font-semibold cursor-pointer transition-all duration-300 flex items-center gap-3 mx-auto hover:shadow-lg hover:-translate-y-1">
                            <i class="fas fa-plus"></i> Generate Your First Report
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($data['reportCards'] as $card): ?>
                        <!-- Enhanced grid card with advanced UI/UX and integrated smaller custom design -->
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                            <a href="<?php echo htmlspecialchars($card['link'], ENT_QUOTES, 'UTF-8'); ?>" class="block h-full flex flex-col">
                                <!-- Card header with gradient background -->
                                <div class="bg-gradient-to-r from-orange-500 to-blue-600 p-4 flex items-center justify-between relative">
                                    <div class="bg-white bg-opacity-20 w-10 h-10 rounded-lg flex items-center justify-center">
                                        <i class="fas <?php echo htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8'); ?> text-white text-lg"></i>
                                    </div>
                                    <div class="bg-white bg-opacity-20 w-6 h-6 rounded-full flex items-center justify-center">
                                        <i class="fas fa-arrow-right text-white text-xs"></i>
                                    </div>
                                </div>
                                <!-- Card content -->
                                <div class="p-5 flex-grow flex flex-col">
                                    <div class="flex items-center mb-2">
                                        <!-- Smaller custom design in front of the report name -->
                                        <div class="relative inline-block mr-3">
                                            <div class="bg-gradient-to-br from-orange-500 to-blue-600 rounded-2xl w-20 h-20 flex items-center justify-center shadow-xl">
                                                <div class="bg-white bg-opacity-20 rounded-full w-16 h-16 flex items-center justify-center">
                                                    <div class="bg-white bg-opacity-30 rounded-full w-12 h-12 flex items-center justify-center">
                                                        <i class="fas fa-chart-bar text-white text-2xl"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="absolute -top-2 -right-2 bg-blue-400 rounded-full w-8 h-8 flex items-center justify-center shadow-md">
                                                <i class="fas fa-file-pdf text-white text-sm"></i>
                                            </div>
                                            <div class="absolute -bottom-2 -left-2 bg-orange-500 rounded-full w-8 h-8 flex items-center justify-center shadow-md">
                                                <i class="fas fa-chart-pie text-white text-sm"></i>
                                            </div>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-4 flex-grow"><?php echo htmlspecialchars($card['desc'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <!-- Animated button -->
                                    <div class="mt-auto">
                                        <div class="inline-flex items-center text-orange-600 text-sm font-semibold group">
                                            View Report
                                            <i class="fas fa-arrow-right ml-2 text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card footer with subtle gradient -->
                                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-5 py-3 text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-chart-bar mr-2"></i>
                                    <span>Click to view analytics</span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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
            // Menu item interactions (sidebar only)
            document.querySelectorAll('.sidebar a').forEach(link => {
                link.addEventListener('click', function() {
                    // Remove active class from all menu items
                    document.querySelectorAll('.sidebar a').forEach(item => {
                        item.classList.remove('bg-gradient-to-r', 'from-blue-50', 'to-indigo-50', 'text-blue-900', 'border-l-4', 'border-blue-600', 'shadow-sm');
                        item.classList.add('text-gray-700', 'hover:bg-gray-100');
                        
                        // Remove custom design element
                        const indicator = item.querySelector('.absolute.right-2');
                        if (indicator) {
                            indicator.remove();
                        }
                    });
                    
                    // Add active class to clicked item with enhanced styling
                    this.classList.remove('text-gray-700', 'hover:bg-gray-100');
                    this.classList.add('bg-gradient-to-r', 'from-blue-50', 'to-indigo-50', 'text-blue-900', 'border-l-4', 'border-blue-600', 'shadow-sm');
                    
                    // Add custom design element for active state
                    if (!this.querySelector('.absolute.right-2')) {
                        const indicator = document.createElement('div');
                        indicator.className = 'absolute right-2 top-1/2 transform -translate-y-1/2 w-2 h-2 rounded-full bg-blue-500';
                        this.appendChild(indicator);
                    }
                });
            });
            
            // Set initial active state for reports
            const reportsLink = document.querySelector('.sidebar a[href="<?php echo htmlspecialchars(BASE_PATH . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>"]');
            if (reportsLink) {
                reportsLink.classList.remove('text-gray-700', 'hover:bg-gray-100');
                reportsLink.classList.add('bg-gradient-to-r', 'from-blue-50', 'to-indigo-50', 'text-blue-900', 'border-l-4', 'border-blue-600', 'shadow-sm');
                
                // Add custom design element for active state if not present
                if (!reportsLink.querySelector('.absolute.right-2')) {
                    const indicator = document.createElement('div');
                    indicator.className = 'absolute right-2 top-1/2 transform -translate-y-1/2 w-2 h-2 rounded-full bg-blue-500';
                    reportsLink.appendChild(indicator);
                }
            }
        });
    </script>
</body>
</html>