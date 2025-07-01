<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/A2Z-DBMS');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A2Z Engineering Operational Data Tables for Solar, AC, and Electrical Power Management">
    <title>A2Z Engineering - Tables</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/src/assets/css/tables.css">
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="<?php echo BASE_PATH; ?>/assets/images/logo.png" alt="A2Z Engineering Logo">
            </div>
            <h2>A2Z Engineering</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?php echo BASE_PATH; ?>/admin/dashboard"><i class="fas fa-tachometer-alt"></i> <span class="sidebar-text">Dashboard</span></a></li>
            <li><a href="<?php echo BASE_PATH; ?>/admin/tables" class="active"><i class="fas fa-table"></i> <span class="sidebar-text">Tables</span></a></li>
            <li><a href="<?php echo BASE_PATH; ?>/admin/reports"><i class="fas fa-file-alt"></i> <span class="sidebar-text">Reports</span></a></li>
            <li><a href="<?php echo BASE_PATH; ?>/logout"><i class="fas fa-sign-out-alt"></i> <span class="sidebar-text">Logout</span></a></li>
        </ul>
    </div>

    <div class="container" id="container">
        <div class="dashboard-header">
            <div class="header-title">
                <button class="toggle-btn" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                A2Z Engineering Operational Data Tables
                <span id="datetime"><?php echo date('l, F j, Y - H:i:s'); ?></span>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($data['username']); ?> | DB: <?php echo htmlspecialchars($data['dbname']); ?></span>
            </div>
        </div>

        <?php if (!empty($data['message'])): ?>
            <div class="message" style="color: green; padding: 10px; text-align: center;">
                <?php echo htmlspecialchars($data['message']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($data['error'])): ?>
            <div class="error" style="color: red; padding: 10px; text-align: center;">
                <?php echo htmlspecialchars($data['error']); ?>
            </div>
        <?php endif; ?>

        <div class="main-content">
            <div class="section-header">
                <h2>Operational Data</h2>
                <input type="text" class="search-bar" placeholder="Search tables..." onkeyup="filterCards(this, 'operational')" aria-label="Search operational data tables">
            </div>
            <div class="card-grid" id="operational-grid">
                <?php foreach ($data['operationalCards'] as $card): ?>
                    <div class="card">
                        <a href="<?php echo htmlspecialchars(BASE_PATH . $card['link'], ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="fas <?php echo htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                            <div class="card-title"><?php echo htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="card-desc"><?php echo htmlspecialchars($card['desc'], ENT_QUOTES, 'UTF-8'); ?></div>
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
            sidebar.classList.toggle('collapsed');
            container.classList.toggle('full-width');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }

        function filterCards(input, gridId) {
            const searchTerm = input.value.toLowerCase().trim();
            const grid = document.getElementById(`${gridId}-grid`);
            const cards = grid.getElementsByClassName('card');
            Array.from(cards).forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const desc = card.querySelector('.card-desc').textContent.toLowerCase();
                card.style.display = (title.includes(searchTerm) || desc.includes(searchTerm)) ? '' : 'none';
            });
        }

        function updateDateTime() {
            const datetime = document.getElementById('datetime');
            if (datetime) {
                const now = new Date();
                datetime.textContent = now.toLocaleString('en-US', {
                    weekday: 'long', month: 'long', day: 'numeric', year: 'numeric',
                    hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
                });
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
            document.querySelectorAll('.card').forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transition = 'all 0.3s ease';
                    card.style.transform = 'translateY(-5px)';
                    card.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transition = 'all 0.3s ease';
                    card.style.transform = 'translateY(0)';
                    card.style.boxShadow = '0 2px 10px rgba(0,0,0,0.05)';
                });
            });
        });
    </script>
</body>
</html>