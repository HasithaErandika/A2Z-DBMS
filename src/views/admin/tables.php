<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - Tables</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1E3A8A;        /* Deep blue */
            --secondary: #64748B;      /* Slate gray */
            --background: #F8FAFC;     /* Light gray-white */
            --card-bg: #FFFFFF;        /* White */
            --text-dark: #0F172A;      /* Near black */
            --text-muted: #94A3B8;     /* Light gray */
            --gradient: linear-gradient(135deg, #1E3A8A, #3B82F6); /* Blue gradient */
            --shadow: rgba(30, 58, 138, 0.1); /* Derived from primary */
            --shadow-hover: rgba(30, 58, 138, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--background);
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            background: var(--card-bg);
            padding: 24px;
            transition: all 0.3s ease;
            box-shadow: 2px 0 15px var(--shadow);
        }

        .sidebar.collapsed {
            width: 80px;
            padding: 24px 12px;
        }

        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .sidebar-header h2 {
            display: none;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 24px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--text-muted);
        }

        .sidebar-logo {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            overflow: hidden;
        }

        .sidebar-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: var(--gradient);
            color: var(--card-bg);
            box-shadow: 0 2px 8px var(--shadow);
        }

        .container {
            margin-left: 260px;
            padding: 32px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .container.full-width {
            margin-left: 80px;
        }

        .dashboard-header {
            background: var(--gradient);
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 15px var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
            color: var(--card-bg);
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-title span#datetime {
            font-size: 0.9rem;
            font-weight: 400;
            margin-left: 16px;
            opacity: 0.9;
        }

        .toggle-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: var(--card-bg);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .toggle-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .user-info {
            background: rgba(255, 255, 255, 0.15);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(2px);
        }

        .main-content {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 15px var(--shadow);
            margin-bottom: 32px;
            transition: all 0.2s ease;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--text-muted);
        }

        .section-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .search-bar {
            padding: 12px 16px;
            border: 1px solid var(--text-muted);
            border-radius: 8px;
            width: 280px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            background: var(--card-bg);
            box-shadow: inset 0 1px 3px var(--shadow);
        }

        .search-bar:focus {
            width: 320px;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.2);
            outline: none;
        }

        /* Enhanced Card Grid */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 12px var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient);
            transition: width 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px var(--shadow-hover);
        }

        .card:hover::before {
            width: 8px;
        }

        .card a {
            text-decoration: none;
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            width: 100%;
            z-index: 1;
        }

        .card i {
            font-size: 2rem;
            color: var(--card-bg);
            padding: 12px;
            background: var(--gradient);
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .card:hover i {
            transform: scale(1.1);
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.4;
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 80px;
                padding: 24px 12px;
            }
            .sidebar .sidebar-text,
            .sidebar .sidebar-header h2 {
                display: none;
            }
            .container {
                margin-left: 80px;
            }
        }

        @media (max-width: 768px) {
            .card-grid {
                grid-template-columns: 1fr;
            }
            .search-bar {
                width: 100%;
            }
            .search-bar:focus {
                width: 100%;
            }
            .dashboard-header {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }
            .header-title span#datetime {
                margin-left: 0;
            }
        }
    </style>
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
            <li><a href="<?php echo BASE_PATH; ?>/admin/records"><i class="fas fa-file-alt"></i> <span class="sidebar-text">Records</span></a></li>
            <li><a href="<?php echo BASE_PATH; ?>/logout"><i class="fas fa-sign-out-alt"></i> <span class="sidebar-text">Logout</span></a></li>
        </ul>
    </div>

    <div class="container" id="container">
        <div class="dashboard-header">
            <div class="header-title">
                <button class="toggle-btn" onclick="toggleSidebar()">
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

        <div class="main-content">
            <div class="section-header">
                <h2>Operational Data</h2>
                <input type="text" class="search-bar" placeholder="Search tables..." onkeyup="filterCards(this, 'operational')">
            </div>
            <div class="card-grid" id="operational-grid">
                <?php foreach ($data['operationalCards'] as $card): ?>
                    <div class="card">
                        <a href="<?php echo BASE_PATH . $card['link']; ?>">
                            <i class="fas <?php echo $card['icon']; ?>"></i>
                            <div class="card-title"><?php echo $card['title']; ?></div>
                            <div class="card-desc"><?php echo $card['desc']; ?></div>
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
        }

        function filterCards(input, gridId) {
            const searchTerm = input.value.toLowerCase();
            const grid = document.getElementById(`${gridId}-grid`);
            const cards = grid.getElementsByClassName('card');
            Array.from(cards).forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const desc = card.querySelector('.card-desc').textContent.toLowerCase();
                card.style.display = (title.includes(searchTerm) || desc.includes(searchTerm)) ? 'block' : 'none';
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
    </script>
</body>
</html>