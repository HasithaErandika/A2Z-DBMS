<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff6b00;
            --secondary: #00c4b4;
            --accent: #1e90ff;
            --background: #f5f6fa;
            --card-bg: #ffffff;
            --text-dark: #1a2540;
            --text-muted: #6b7280;
            --shadow: rgba(17, 24, 39, 0.1);
            --dark-bg: #1a2540;
            --dark-card: #2d3b55;
            --dark-text: #e5e7eb;
            --success: #10b981;
            --warning: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background);
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        body.dark-mode {
            background: var(--dark-bg);
            color: var(--dark-text);
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: var(--card-bg);
            box-shadow: 4px 0 20px var(--shadow);
            padding: 25px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 80px;
            padding: 15px;
        }

        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .sidebar-header h3 {
            display: none;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 25px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(107, 114, 128, 0.1);
            transition: all 0.3s;
        }

        .sidebar-logo {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.3rem;
            transition: all 0.3s;
        }

        .sidebar.collapsed .sidebar-logo {
            width: 40px;
            height: 40px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 15px;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .sidebar-menu a:hover {
            background: var(--accent);
            color: white;
            transform: translateX(5px);
        }

        .sidebar-menu a::after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: var(--accent);
            transform: scaleY(0);
            transform-origin: bottom;
            transition: transform 0.3s ease;
        }

        .sidebar-menu a.active::after,
        .sidebar-menu a:hover::after {
            transform: scaleY(1);
        }

        .container {
            margin-left: 260px;
            padding: 30px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .container.full-width {
            margin-left: 80px;
        }

        .container.no-sidebar {
            margin-left: 0;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            padding: 25px 35px;
            border-radius: 16px;
            box-shadow: 0 6px 25px rgba(30, 144, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            margin-bottom: 35px;
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><path fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="20" d="M50,150 Q100,50 150,150"/></svg>') no-repeat center;
            opacity: 0.1;
            pointer-events: none;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .toggle-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 1.4rem;
            padding: 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .toggle-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .user-info {
            background: rgba(255, 255, 255, 0.15);
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(5px);
        }

        .main-content {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 15px var(--shadow);
            margin-bottom: 35px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(107, 114, 128, 0.1);
        }

        .section-header h2 {
            font-size: 1.6rem;
            font-weight: 600;
            position: relative;
        }

        .section-header h2::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--accent);
            border-radius: 2px;
        }

        .search-bar {
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            width: 300px;
            font-size: 0.95rem;
            background: rgba(107, 114, 128, 0.1);
            box-shadow: inset 0 2px 4px var(--shadow);
            transition: all 0.3s ease;
        }

        .search-bar:focus {
            width: 350px;
            background: white;
            box-shadow: 0 4px 15px var(--shadow);
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            padding: 10px 0;
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                rgba(30, 144, 255, 0) 0%,
                rgba(30, 144, 255, 0.1) 50%,
                rgba(30, 144, 255, 0) 100%
            );
            transition: all 0.5s ease;
            pointer-events: none;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px var(--shadow);
        }

        .card:hover::before {
            transform: translate(25%, 25%) rotate(20deg);
        }

        .card a {
            text-decoration: none;
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .card i {
            font-size: 2.2rem;
            color: var(--accent);
            padding: 15px;
            background: rgba(30, 144, 255, 0.1);
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .card:hover i {
            transform: scale(1.15);
            background: rgba(30, 144, 255, 0.2);
        }

        .card-title {
            font-weight: 600;
            font-size: 1.15rem;
            margin: 5px 0;
        }

        .card-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            opacity: 0.9;
            text-align: center;
        }

        .status-dot {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 8px var(--success);
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 80px;
                padding: 15px;
            }
            .sidebar .sidebar-text,
            .sidebar .sidebar-header h3 {
                display: none;
            }
            .container {
                margin-left: 80px;
            }
            .sidebar.active {
                width: 260px;
            }
            .sidebar.active .sidebar-text,
            .sidebar.active .sidebar-header h3 {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .card-grid {
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            }
            .search-bar {
                width: 100%;
            }
            .search-bar:focus {
                width: 100%;
            }
            .dashboard-header {
                padding: 20px;
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            .header-title {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            .main-content {
                padding: 20px;
            }
            .card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">A2Z</div>
            <h3>A2Z Engineering</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?php echo BASE_PATH; ?>/admin" class="active"><i class="fas fa-tachometer-alt"></i> <span class="sidebar-text">Dashboard</span></a></li>
            <li><a href="<?php echo BASE_PATH; ?>/admin/manage_table.php?table=Employee"><i class="fas fa-users"></i> <span class="sidebar-text">Employees</span></a></li>
            <li><a href="<?php echo BASE_PATH; ?>/admin/manage_table.php?table=Projects"><i class="fas fa-solar-panel"></i> <span class="sidebar-text">Projects</span></a></li>
            <li><a href="<?php echo BASE_PATH; ?>/admin/wages_report.php"><i class="fas fa-money-bill"></i> <span class="sidebar-text">Wages Report</span></a></li>
            <li><a href="<?php echo BASE_PATH; ?>/logout"><i class="fas fa-sign-out-alt"></i> <span class="sidebar-text">Logout</span></a></li>
        </ul>
    </div>

    <div class="container" id="container">
        <div class="dashboard-header">
            <div class="header-title">
                <button class="toggle-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                A2Z Engineering Dashboard
            </div>
            <div class="header-controls">
                <button class="toggle-btn" onclick="toggleDarkMode()">
                    <i class="fas fa-moon"></i>
                </button>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo htmlspecialchars($data['username']); ?> | DB: <?php echo htmlspecialchars($data['dbname']); ?></span>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="section-header">
                <h2>Operational Data</h2>
                <input type="text" class="search-bar" placeholder="Search operations..." onkeyup="filterCards(this, 'operational')">
            </div>
            <div class="card-grid" id="operational-grid">
                <?php foreach ($data['operationalCards'] as $card): ?>
                    <div class="card">
                        <a href="<?php echo BASE_PATH . $card['link']; ?>">
                            <i class="fas <?php echo $card['icon']; ?>"></i>
                            <div class="card-title"><?php echo $card['title']; ?></div>
                            <div class="card-desc"><?php echo $card['desc']; ?></div>
                            <div class="status-dot"></div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="main-content">
            <div class="section-header">
                <h2>Reports</h2>
                <input type="text" class="search-bar" placeholder="Search reports..." onkeyup="filterCards(this, 'reports')">
            </div>
            <div class="card-grid" id="reports-grid">
                <?php foreach ($data['reportCards'] as $card): ?>
                    <div class="card">
                        <a href="<?php echo BASE_PATH . $card['link']; ?>">
                            <i class="fas <?php echo $card['icon']; ?>"></i>
                            <div class="card-title"><?php echo $card['title']; ?></div>
                            <div class="card-desc"><?php echo $card['desc']; ?></div>
                            <div class="status-dot"></div>
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
            container.classList.toggle('no-sidebar', !sidebar.classList.contains('collapsed') && window.innerWidth <= 1024);
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
            updateTheme();
        }

        function updateTheme() {
            const isDark = document.body.classList.contains('dark-mode');
            document.querySelectorAll('.card, .sidebar, .main-content, .dashboard-header')
                .forEach(el => el.style.background = isDark ? 'var(--dark-card)' : 'var(--card-bg)');
            document.querySelectorAll('.card a').forEach(el => el.style.color = isDark ? 'var(--dark-text)' : 'var(--text-dark)');
        }

        function filterCards(input, gridId) {
            const searchTerm = input.value.toLowerCase();
            const grid = document.getElementById(`${gridId}-grid`);
            const cards = grid.getElementsByClassName('card');
            Array.from(cards).forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const desc = card.querySelector('.card-desc').textContent.toLowerCase();
                const shouldShow = title.includes(searchTerm) || desc.includes(searchTerm);
                card.style.opacity = shouldShow ? '1' : '0';
                card.style.transform = shouldShow ? 'scale(1)' : 'scale(0.95)';
                card.style.transition = 'all 0.3s ease';
                card.style.pointerEvents = shouldShow ? 'auto' : 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('darkMode') === 'true') {
                document.body.classList.add('dark-mode');
                updateTheme();
            }

            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('click', () => {
                    card.style.transform = 'scale(0.98)';
                    setTimeout(() => card.style.transform = 'scale(1)', 150);
                });
            });

            window.addEventListener('resize', () => {
                const sidebar = document.getElementById('sidebar');
                const container = document.getElementById('container');
                if (window.innerWidth <= 1024 && !sidebar.classList.contains('collapsed')) {
                    container.classList.add('no-sidebar');
                } else {
                    container.classList.remove('no-sidebar');
                }
            });
        });
    </script>
</body>
</html>