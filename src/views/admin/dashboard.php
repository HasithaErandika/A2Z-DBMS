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
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/src/assets/css/dashboard.css">
</head>
<body>
    <!-- Enhanced Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-database"></i>
                <div class="logo-glow"></div>
            </div>
            <h2 class="sidebar-title">A2Z Engineering</h2>
            <p class="sidebar-subtitle">Internal Database System</p>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="sidebar-menu">
                <li class="menu-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/dashboard" class="menu-link active">
                        <i class="fas fa-tachometer-alt menu-icon"></i>
                        <span class="menu-text">Dashboard</span>
                        <span class="menu-indicator"></span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/tables" class="menu-link">
                        <i class="fas fa-table menu-icon"></i>
                        <span class="menu-text">Data Tables</span>
                        <span class="menu-indicator"></span>
                    </a>
                </li>
                <!-- <li class="menu-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/manage_table" class="menu-link">
                        <i class="fas fa-edit menu-icon"></i>
                        <span class="menu-text">Manage Data</span>
                        <span class="menu-indicator"></span>
                    </a>
                </li> -->
                <li class="menu-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/reports" class="menu-link">
                        <i class="fas fa-chart-bar menu-icon"></i>
                        <span class="menu-text">Reports</span>
                        <span class="menu-indicator"></span>
                    </a>
                </li>
                <li class="menu-divider"></li>
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="fas fa-cog menu-icon"></i>
                        <span class="menu-text">Settings</span>
                        <span class="menu-indicator"></span>
                    </a>
                </li>
                <li class="menu-divider"></li>
                <li class="menu-item">
                    <a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/logout', ENT_QUOTES, 'UTF-8'); ?>" class="menu-link logout-link">
                        <div class="menu-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <span class="menu-text">Logout</span>
                        <div class="menu-indicator"></div>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="system-status">
                <div class="status-indicator online"></div>
                <span>System Online</span>
            </div>
        </div>
    </div>

    <!-- Enhanced Main Container -->
    <div class="container" id="container">
        <!-- Enhanced Dashboard Header -->
        <div class="dashboard-header">
            <div class="header-left">
                <button class="btn btn-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="header-title">System Dashboard</h1>
                    <p class="header-subtitle">Internal Database Management Overview</p>
                </div>
            </div>
            
            <div class="header-right">
                <div class="header-actions">
                    <button class="action-btn" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <button class="action-btn" title="Quick Actions">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="action-btn" title="Search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?php echo htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="user-role">Database: <?php echo htmlspecialchars($data['dbname'], ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                    <div class="user-menu-toggle">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced DateTime Display -->
        <div class="datetime-display">
            <div class="datetime-card">
                <div class="datetime-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="datetime-content">
                    <div class="current-date" id="currentDate"><?php echo date('l, F j, Y'); ?></div>
                    <div class="current-time" id="currentTime"><?php echo date('H:i:s'); ?></div>
                </div>
            </div>
        </div>

        <!-- Enhanced Summary Overview -->
        <div class="main-content">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-chart-pie"></i>
                    <div>
                        <h2>System Overview</h2>
                        <p>Key metrics and performance indicators</p>
                    </div>
                </div>
                <div class="section-actions">
                    <button class="btn btn-outline">
                        <i class="fas fa-download"></i>
                        Export Report
                    </button>
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Record
                    </button>
                </div>
            </div>
            
            <div class="summary-grid">
                <div class="summary-card primary">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-badge">Total</div>
                    </div>
                    <div class="card-body">
                        <div class="metric-value"><?php echo $totalEmployees; ?></div>
                        <div class="metric-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+12 this month</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="#" class="view-details">View All Employees <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="summary-card success">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="card-badge">Active</div>
                    </div>
                    <div class="card-body">
                        <div class="metric-value"><?php echo $activeProjects; ?></div>
                        <div class="metric-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+3 this week</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="#" class="view-details">View All Projects <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="summary-card warning">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-badge">Pending</div>
                    </div>
                    <div class="card-body">
                        <div class="metric-value"><?php echo $pendingTasks; ?></div>
                        <div class="metric-change negative">
                            <i class="fas fa-arrow-down"></i>
                            <span>-5 today</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="#" class="view-details">View Pending Tasks <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="summary-card info">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="card-badge">Storage</div>
                    </div>
                    <div class="card-body">
                        <div class="metric-value"><?php echo $storageUsed; ?>%</div>
                        <div class="metric-change neutral">
                            <i class="fas fa-minus"></i>
                            <span>No change</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="#" class="view-details">Storage Details <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced System Information -->
        <div class="main-content">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-server"></i>
                    <div>
                        <h2>System Information</h2>
                        <p>Technical details and system status</p>
                    </div>
                </div>
                <div class="section-actions">
                    <button class="btn btn-outline btn-sm">
                        <i class="fas fa-refresh"></i> Refresh
                    </button>
                </div>
            </div>
            
            <div class="system-info-grid">
                <div class="system-info-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="card-badge">Server</div>
                    </div>
                    <div class="card-body">
                        <div class="info-value">Production Server</div>
                        <div class="info-status">
                            <span class="status-dot online"></span>
                            <span>Online</span>
                        </div>
                    </div>
                </div>
                <div class="system-info-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="card-badge">Database</div>
                    </div>
                    <div class="card-body">
                        <div class="info-value">MySQL 8.0</div>
                        <div class="info-status">
                            <span class="status-dot online"></span>
                            <span>Connected</span>
                        </div>
                    </div>
                </div>
                <div class="system-info-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="card-badge">Security</div>
                    </div>
                    <div class="card-body">
                        <div class="info-value">SSL Enabled</div>
                        <div class="info-status">
                            <span class="status-dot online"></span>
                            <span>Secure</span>
                        </div>
                    </div>
                </div>
                <div class="system-info-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="card-badge">Uptime</div>
                    </div>
                    <div class="card-body">
                        <div class="info-value">99.9%</div>
                        <div class="info-status">
                            <span class="status-dot online"></span>
                            <span>Stable</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Quick Actions -->
        <div class="main-content">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-bolt"></i>
                    <div>
                        <h2>Quick Actions</h2>
                        <p>Common tasks and operations</p>
                    </div>
                </div>
            </div>
            
            <div class="quick-actions-grid">
                <div class="quick-action-card">
                    <div class="action-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h4>Add Employee</h4>
                    <p>Register new employee in the system</p>
                    <button class="btn btn-primary">Add Employee</button>
                </div>
                <div class="quick-action-card">
                    <div class="action-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h4>Create Job</h4>
                    <p>Set up new project or job assignment</p>
                    <button class="btn btn-primary">Create Job</button>
                </div>
                <div class="quick-action-card">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h4>Generate Report</h4>
                    <p>Create custom reports and analytics</p>
                    <button class="btn btn-primary">Generate Report</button>
                </div>
                <div class="quick-action-card">
                    <div class="action-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <h4>System Settings</h4>
                    <p>Configure system preferences and options</p>
                    <button class="btn btn-primary">Settings</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced sidebar functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const container = document.getElementById('container');
            sidebar.classList.toggle('collapsed');
            container.classList.toggle('full-width');
            
            // Add animation class
            sidebar.classList.add('animating');
            setTimeout(() => sidebar.classList.remove('animating'), 300);
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
            // Add hover effects to summary cards
            document.querySelectorAll('.summary-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                    this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                    this.style.boxShadow = '0 8px 24px rgba(0, 0, 0, 0.1)';
                });
            });

            // Add hover effects to system info cards
            document.querySelectorAll('.system-info-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.05)';
                    this.style.boxShadow = '0 12px 32px rgba(0, 0, 0, 0.15)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                    this.style.boxShadow = '0 6px 20px rgba(0, 0, 0, 0.1)';
                });
            });

            // Add hover effects to quick action cards
            document.querySelectorAll('.quick-action-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.15)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 8px 24px rgba(0, 0, 0, 0.1)';
                });
            });

            // Enhanced button interactions
            document.querySelectorAll('.btn').forEach(btn => {
                btn.addEventListener('mousedown', function() {
                    this.style.transform = 'scale(0.95)';
                });
                
                btn.addEventListener('mouseup', function() {
                    this.style.transform = 'scale(1)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // Menu item interactions
            document.querySelectorAll('.menu-link').forEach(link => {
                link.addEventListener('click', function() {
                    // Remove active class from all menu items
                    document.querySelectorAll('.menu-link').forEach(item => {
                        item.classList.remove('active');
                    });
                    
                    // Add active class to clicked item
                    this.classList.add('active');
                });
            });

            // Action button interactions
            document.querySelectorAll('.action-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Add click effect
                    this.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });
        });

        // Enhanced scroll effects
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.dashboard-header');
            if (parallax) {
                parallax.style.transform = `translateY(${scrolled * 0.1}px)`;
            }
        });

        // Add loading states for buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.classList.contains('btn-loading')) return;
                
                this.classList.add('btn-loading');
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                
                // Simulate loading (remove in production)
                setTimeout(() => {
                    this.classList.remove('btn-loading');
                    this.innerHTML = this.getAttribute('data-original-text') || 'Button';
                }, 2000);
            });
        });
    </script>
</body>
</html>