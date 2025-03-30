<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - Tables</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #005f99;
            --secondary: #e6ecf0;
            --accent: #007acc;
            --background: #f7f9fc;
            --card-bg: #ffffff;
            --text-dark: #2d3748;
            --text-muted: #718096;
            --shadow: rgba(0, 0, 0, 0.05);
            --success: #38a169;
            --danger: #e53e3e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: var(--background);
            color: var(--text-dark);
            line-height: 1.5;
            overflow-x: hidden;
        }

        .datetime-header {
            background: var(--secondary);
            padding: 10px;
            text-align: center;
            font-size: 1rem;
            color: var(--text-muted);
            border-bottom: 1px solid var(--shadow);
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            height: 100vh;
            background: var(--card-bg);
            box-shadow: 2px 0 10px var(--shadow);
            padding: 20px;
            transition: width 0.3s ease;
        }

        .sidebar.collapsed {
            width: 70px;
            padding: 10px;
        }

        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .sidebar-header h3 {
            display: none;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--secondary);
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.2s ease;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: var(--accent);
            color: white;
        }

        .container {
            margin-left: 240px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .container.full-width {
            margin-left: 70px;
        }

        .dashboard-header {
            background: var(--primary);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            margin-bottom: 20px;
        }

        .header-title {
            font-size: 1.4rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 5px;
            transition: opacity 0.2s;
        }

        .toggle-btn:hover {
            opacity: 0.8;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .main-content {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px var(--shadow);
            margin-bottom: 20px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--secondary);
        }

        .section-header h2 {
            font-size: 1.4rem;
            font-weight: 500;
        }

        .search-bar {
            padding: 10px 15px;
            border: 1px solid var(--secondary);
            border-radius: 6px;
            width: 250px;
            font-size: 0.9rem;
            transition: width 0.3s ease, border-color 0.2s;
        }

        .search-bar:focus {
            width: 300px;
            border-color: var(--accent);
            outline: none;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px var(--shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px var(--shadow);
        }

        .card a {
            text-decoration: none;
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .card i {
            font-size: 1.8rem;
            color: var(--accent);
            padding: 10px;
            background: var(--secondary);
            border-radius: 50%;
        }

        .card-title {
            font-weight: 500;
            font-size: 1.1rem;
        }

        .card-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-align: center;
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 70px;
                padding: 10px;
            }
            .sidebar .sidebar-text,
            .sidebar .sidebar-header h3 {
                display: none;
            }
            .container {
                margin-left: 70px;
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
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="datetime-header" id="datetime">
        <span><?php echo date('l, F j, Y - H:i:s'); ?></span>
    </div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">A2Z</div>
            <h3>A2Z Engineering</h3>
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
                Operational Data Tables
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