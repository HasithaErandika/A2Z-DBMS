<?php
// Ensure BASE_PATH is defined for relative paths (though we'll use FULL_BASE_URL)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}

// Define FULL_BASE_URL for absolute links
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
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
    <meta name="description" content="A2Z Engineering Report Records for Solar, AC, and Electrical Power Management">
    <title>A2Z Engineering - Reports</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(FULL_BASE_URL . '/src/assets/css/records.css', ENT_QUOTES, 'UTF-8'); ?>">
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
            <li><a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/dashboard', ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-tachometer-alt"></i> <span class="sidebar-text">Dashboard</span></a></li>
            <li><a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/tables', ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-table"></i> <span class="sidebar-text">Tables</span></a></li>
            <li><a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>" class="active"><i class="fas fa-file-alt"></i> <span class="sidebar-text">Reports</span></a></li>
            <li><a href="<?php echo htmlspecialchars(FULL_BASE_URL . '/logout', ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-sign-out-alt"></i> <span class="sidebar-text">Logout</span></a></li>
        </ul>
    </div>

    <div class="container" id="container">
        <div class="dashboard-header">
            <div class="header-title">
                <button class="toggle-btn" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                A2Z Engineering Report Records
                <span id="datetime"><?php echo date('l, F j, Y - H:i:s'); ?></span>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8'); ?> | DB: <?php echo htmlspecialchars($data['dbname'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>

        <div class="main-content">
            <div class="section-header">
                <h2>Reports</h2>
                <input type="text" class="search-bar" placeholder="Search reports..." onkeyup="filterCards(this, 'reports')" aria-label="Search reports">
            </div>
            <div class="card-grid" id="reports-grid">
                <?php if (empty($data['reportCards'])): ?>
                    <p>No reports available.</p>
                <?php else: ?>
                    <?php foreach ($data['reportCards'] as $card): ?>
                        <div class="card">
                            <a href="<?php echo htmlspecialchars($card['link'], ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="fas <?php echo htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                                <div class="card-title"><?php echo htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="card-desc"><?php echo htmlspecialchars($card['desc'], ENT_QUOTES, 'UTF-8'); ?></div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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

        function filterCards(input, gridId) {
            const searchTerm = input.value.toLowerCase();
            const grid = document.getElementById(`${gridId}-grid`);
            const cards = grid.getElementsByClassName('card');
            Array.from(cards).forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const desc = card.querySelector('.card-desc').textContent.toLowerCase();
                card.style.display = (title.includes(searchTerm) || desc.includes(searchTerm)) ? 'flex' : 'none';
            });
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

        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', () => card.style.transition = 'all 0.3s ease');
            card.addEventListener('mouseleave', () => card.style.transition = 'all 0.3s ease');
        });
    </script>
</body>
</html>