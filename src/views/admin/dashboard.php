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
    <link rel="stylesheet" href="<?php echo htmlspecialchars(FULL_BASE_URL . '/src/assets/css/adminDashboard.css', ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="<?php echo htmlspecialchars(FULL_BASE_URL . '/src/assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="A2Z Engineering Logo">
            </div>
            <h2>A2Z Engineering</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/dashboard', ENT_QUOTES, 'UTF-8'); ?>" class="active"><i class="fas fa-tachometer-alt"></i> <span class="sidebar-text">Dashboard</span></a></li>
            <li><a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/tables', ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-table"></i> <span class="sidebar-text">Tables</span></a></li>
            <li><a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-file-alt"></i> <span class="sidebar-text">Reports</span></a></li>
            <li><a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/logout', ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-sign-out-alt"></i> <span class="sidebar-text">Logout</span></a></li>
        </ul>
    </div>

    <div class="container" id="container">
        <div class="dashboard-header">
            <div class="header-title">
                <button class="toggle-btn" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                A2Z Engineering DBMS Dashboard
                <span id="datetime"><?php echo date('l, F j, Y - H:i:s'); ?></span>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8'); ?> | DB: <?php echo htmlspecialchars($data['dbname'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>

        <div class="main-content">
            <div class="section-header">
                <h2>Summary Overview</h2>
            </div>
            <div class="summary-grid">
                <div class="summary-card">
                    <h3>Total Employees</h3>
                    <p><?php echo htmlspecialchars($data['summary']['total_employees'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="summary-card">
                    <h3>Active Jobs</h3>
                    <p><?php echo htmlspecialchars($data['summary']['active_jobs'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="summary-card">
                    <h3>Total Projects</h3>
                    <p><?php echo htmlspecialchars($data['summary']['total_projects'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="summary-card">
                    <h3>Total Expenses</h3>
                    <p><?php echo number_format($data['summary']['total_expenses'], 2); ?></p>
                </div>
                <div class="summary-card">
                    <h3>Total Payments</h3>
                    <p><?php echo number_format($data['summary']['total_payments'], 2); ?></p>
                </div>
                <div class="summary-card">
                    <h3>Today's Jobs</h3>
                    <p><?php echo htmlspecialchars($data['summary']['todays_jobs'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="summary-card">
                    <h3>Today's Expenses</h3>
                    <p><?php echo number_format($data['summary']['todays_expenses'], 2); ?></p>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="section-header">
                <h2>System Information</h2>
            </div>
            <div class="system-info-grid">
                <div class="system-info-card">
                    <h3>PHP Version</h3>
                    <p><?php echo htmlspecialchars($data['system_info']['php_version'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="system-info-card">
                    <h3>MySQL Version</h3>
                    <p><?php echo htmlspecialchars($data['system_info']['mysql_version'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="system-info-card">
                    <h3>Server Software</h3>
                    <p><?php echo htmlspecialchars($data['system_info']['server_software'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="system-info-card">
                    <h3>Database Name</h3>
                    <p><?php echo htmlspecialchars($data['system_info']['db_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const container = document.getElementById('container');
            sidebar.classList.toggle('collapsed');
            container.classList.toggle('full-width');
        }

        function updateDateTime() {
            const datetime = document.getElementById('datetime');
            const now = new Date();
            datetime.textContent = now.toLocaleString('en-US', {
                weekday: 'long', month: 'long', day: 'numeric', year: 'numeric',
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();

        // Smooth hover effects for cards
        document.querySelectorAll('.summary-card, .system-info-card').forEach(card => {
            card.addEventListener('mouseenter', () => card.style.transition = 'all 0.3s ease');
            card.addEventListener('mouseleave', () => card.style.transition = 'all 0.3s ease');
        });
    </script>
</body>
</html>