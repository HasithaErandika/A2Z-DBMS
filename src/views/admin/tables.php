<?php
// Ensure BASE_PATH is defined for relative paths
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}

// Define FULL_BASE_URL for absolute links
if (!defined('FULL_BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('FULL_BASE_URL', $protocol . $host . $path);
}

// Validate $data to prevent undefined index errors
if (!isset($data) || !is_array($data)) {
    $data = [
        'username' => 'Unknown',
        'dbname' => 'Unknown',
        'operationalCards' => [],
        'message' => '',
        'error' => '',
    ];
    error_log("Warning: \$data not provided to tables.php. Using fallback values.");
} else {
    // Ensure required keys exist
    $data = array_merge([
        'username' => 'Unknown',
        'dbname' => 'Unknown',
        'operationalCards' => [],
        'message' => '',
        'error' => '',
    ], $data);
}

// Note: Assuming authentication is handled by a controller
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A2Z Engineering Operational Data Tables for Solar, AC, and Electrical Power Management">
    <meta name="author" content="A2Z Engineering">
    <title>A2Z Engineering - Tables</title>
    <link rel="icon" type="image/png" href="<?php echo htmlspecialchars(FULL_BASE_URL . '/src/assets/images/favicon.png', ENT_QUOTES, 'UTF-8'); ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(FULL_BASE_URL . '/src/assets/css/tables.css', ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body>
    <div class="sidebar" id="sidebar" aria-label="Navigation Sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="<?php echo htmlspecialchars(FULL_BASE_URL . '/src/assets/images/LongLogoB.png', ENT_QUOTES, 'UTF-8'); ?>" 
                     alt="A2Z Engineering Logo" 
                     onerror="this.src='<?php echo htmlspecialchars(FULL_BASE_URL . '/src/assets/images/fallback-logo.png', ENT_QUOTES, 'UTF-8'); ?>'">
            </div>
            <h2>A2Z DBMS</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/dashboard', ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-tachometer-alt"></i> <span class="sidebar-text">Dashboard</span></a></li>
            <li><a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/tables', ENT_QUOTES, 'UTF-8'); ?>" class="active" aria-current="page"><i class="fas fa-table"></i> <span class="sidebar-text">Tables</span></a></li>
            <li><a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-file-alt"></i> <span class="sidebar-text">Reports</span></a></li>
            <li><a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/logout', ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-sign-out-alt"></i> <span class="sidebar-text">Logout</span></a></li>
        </ul>
    </div>

    <div class="container" id="container">
        <div class="dashboard-header">
            <div class="header-title">
                <button class="toggle-btn" onclick="toggleSidebar()" aria-label="Toggle Navigation Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                A2Z Engineering Operational Data Tables
                <span id="datetime"><?php echo htmlspecialchars(date('l, F j, Y - H:i:s'), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8'); ?> | DB: <?php echo htmlspecialchars($data['dbname'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>

        <?php if (!empty($data['message'])): ?>
            <div class="message"><?php echo htmlspecialchars($data['message'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <?php if (!empty($data['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="main-content">
            <div class="section-header">
                <h2>Operational Data</h2>
                <input type="text" class="search-bar" placeholder="Search tables..." onkeyup="filterCards(this, 'operational')" aria-label="Search operational data tables">
            </div>
            <div class="card-grid" id="operational-grid">
                <?php foreach ($data['operationalCards'] as $card): ?>
                    <div class="card" tabindex="0">
                        <a href="<?php echo htmlspecialchars(FULL_BASE_URL . $card['link'], ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="fas <?php echo htmlspecialchars($card['icon'] ?? 'fa-table', ENT_QUOTES, 'UTF-8'); ?>"></i>
                            <div class="card-title"><?php echo htmlspecialchars($card['title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="card-desc"><?php echo htmlspecialchars($card['desc'] ?? 'No description', ENT_QUOTES, 'UTF-8'); ?></div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const container = document.getElementById('container');
            if (sidebar && container) {
                sidebar.classList.toggle('collapsed');
                container.classList.toggle('full-width');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            }
        }

        function filterCards(input, gridId) {
            const searchTerm = input.value.toLowerCase().trim();
            const grid = document.getElementById(`${gridId}-grid`);
            if (!grid) return;
            const cards = grid.getElementsByClassName('card');
            Array.from(cards).forEach(card => {
                const title = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
                const desc = card.querySelector('.card-desc')?.textContent.toLowerCase() || '';
                card.style.display = (title.includes(searchTerm) || desc.includes(searchTerm)) ? '' : 'none';
            });
        }

        function updateDateTime() {
            const datetime = document.getElementById('datetime');
            if (datetime) {
                const now = new Date();
                datetime.textContent = now.toLocaleString('en-US', {
                    weekday: 'long',
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }).replace(/, (\d{1,2}:\d{2}:\d{2})$/, ' - $1');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('container').classList.add('full-width');
            }
            setInterval(updateDateTime, 1000);
            updateDateTime();
        });
    </script>
</body>
</html>