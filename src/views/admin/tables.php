<?php
// Ensure BASE_PATH is defined for relative paths
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}

// Define FULL_BASE_URL for absolute links
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'http://localhost/A2Z-DBMS/');
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
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/src/assets/css/tables.css">
</head>
<body>
    <!-- Enhanced Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-database"></i>
                <div class="logo-glow"></div>
            </div>
            <div>
                <h2 class="sidebar-title">A2Z Engineering</h2>
                <p class="sidebar-subtitle">Internal Database System</p>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="sidebar-menu">
                <li class="menu-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/dashboard" class="menu-link">
                        <i class="fas fa-tachometer-alt menu-icon"></i>
                        <span class="menu-text">Dashboard</span>
                        <span class="menu-indicator"></span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="<?php echo BASE_PATH; ?>/admin/tables" class="menu-link active">
                        <i class="fas fa-table menu-icon"></i>
                        <span class="menu-text">Data Tables</span>
                        <span class="menu-indicator"></span>
                    </a>
                </li>
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
                <li class="menu-item">
                    <a href="<?php echo BASE_PATH; ?>/logout" class="menu-link logout-link">
                        <i class="fas fa-sign-out-alt menu-icon"></i>
                        <span class="menu-text">Logout</span>
                        <span class="menu-indicator"></span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <div class="system-status">
                <span class="status-indicator"></span>
                <span>System Online</span>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container" id="container">
        <!-- Enhanced Dashboard Header -->
        <div class="dashboard-header">
            <div class="header-left">
                <button class="btn-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="header-title">Data Tables</h1>
                    <p class="header-subtitle">Manage internal company data and records</p>
                </div>
            </div>
            
            <div class="header-right">
                <div class="header-actions">
                    <div class="notification-badge">
                        <i class="fas fa-bell"></i>
                        <span>2</span>
                    </div>
                    <button class="action-btn">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="action-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <span class="user-name"><?php echo htmlspecialchars($data['username'] ?? 'Administrator'); ?></span>
                        <span class="user-role">System Admin</span>
                    </div>
                    <button class="user-menu-toggle">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- DateTime Display -->
        <div class="datetime-display">
            <div class="datetime-card">
                <div class="datetime-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="datetime-content">
                    <div class="current-date" id="current-date"><?php echo date('l, F j, Y'); ?></div>
                    <div class="current-time" id="current-time"><?php echo date('H:i:s'); ?></div>
                </div>
            </div>
        </div>

        <!-- System Messages -->
        <?php if (!empty($data['message'])): ?>
            <div class="message success-message">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($data['message']); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($data['error'])): ?>
            <div class="message error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <span><?php echo htmlspecialchars($data['error']); ?></span>
            </div>
        <?php endif; ?>

        <!-- Main Content -->
        <div class="main-content">
            <div class="section-header">
                <div class="section-title">
                    <i class="fas fa-database"></i>
                    <div>
                        <h2>Data Management</h2>
                        <p>Access and manage internal company data tables</p>
                    </div>
                </div>
                <div class="section-actions">
                    <input type="text" class="search-bar" placeholder="Search tables..." onkeyup="filterCards(this, 'operational')" aria-label="Search operational data tables">
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Table
                    </button>
                </div>
            </div>
            
            <div class="card-grid" id="operational-grid">
                <?php if (empty($data['operationalCards'] ?? [])): ?>
                    <div class="empty-state">
                        <i class="fas fa-table"></i>
                        <h3>No Data Tables Available</h3>
                        <p>Create your first data table to start managing company information</p>
                        <button class="btn btn-primary">Create Table</button>
                    </div>
                <?php else: ?>
                    <?php foreach ($data['operationalCards'] as $card): ?>
                        <div class="card">
                            <a href="<?php echo htmlspecialchars(BASE_PATH . $card['link'], ENT_QUOTES, 'UTF-8'); ?>">
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
        // Enhanced sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const container = document.getElementById('container');
            sidebar.classList.toggle('collapsed');
            container.classList.toggle('full-width');
            
            // Add animation class
            sidebar.classList.add('animating');
            setTimeout(() => sidebar.classList.remove('animating'), 300);
            
            // Save state to localStorage
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }

        // Enhanced card filtering
        function filterCards(input, gridId) {
            const searchTerm = input.value.toLowerCase().trim();
            const grid = document.getElementById(`${gridId}-grid`);
            const cards = grid.getElementsByClassName('card');
            
            Array.from(cards).forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const desc = card.querySelector('.card-desc').textContent.toLowerCase();
                const shouldShow = title.includes(searchTerm) || desc.includes(searchTerm);
                
                card.style.display = shouldShow ? 'flex' : 'none';
                
                if (shouldShow) {
                    card.style.animation = 'slideIn 0.3s ease-out';
                }
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
            // Restore sidebar state
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('container').classList.add('full-width');
            }
            
            // Update date/time every second
            setInterval(updateDateTime, 1000);
            updateDateTime();
            
            // Enhanced card hover effects
            document.querySelectorAll('.card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                    this.style.boxShadow = '0 12px 24px rgba(0, 0, 0, 0.15)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                    this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
                });
                
                // Enhanced click effects
                card.addEventListener('mousedown', function() {
                    this.style.transform = 'translateY(-4px) scale(0.98)';
                });
                
                card.addEventListener('mouseup', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
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
            });

            // Menu item active state management
            document.querySelectorAll('.menu-link').forEach(link => {
                link.addEventListener('click', function() {
                    // Remove active class from all menu items
                    document.querySelectorAll('.menu-link').forEach(l => l.classList.remove('active'));
                    // Add active class to clicked item
                    this.classList.add('active');
                });
            });

            // Action button click effects
            document.querySelectorAll('.action-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });

            // Parallax effect for dashboard header
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const header = document.querySelector('.dashboard-header');
                if (header) {
                    header.style.transform = `translateY(${scrolled * 0.1}px)`;
                }
            });

            // Auto-hide messages after 5 seconds
            setTimeout(() => {
                const messages = document.querySelectorAll('.message');
                messages.forEach(message => {
                    message.style.opacity = '0';
                    setTimeout(() => message.remove(), 300);
                });
            }, 5000);
        });

        // Loading states for buttons
        function setLoadingState(button, isLoading) {
            if (isLoading) {
                button.classList.add('loading');
                button.disabled = true;
            } else {
                button.classList.remove('loading');
                button.disabled = false;
            }
        }
    </script>
</body>
</html>