<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/A2Z-DBMS');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - Site Cost Calculation</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1E3A8A; /* Deep blue */
            --secondary: #F59E0B; /* Amber for solar */
            --accent: #10B981; /* Green for renewable */
            --background: #F1F5F9; /* Light slate */
            --card-bg: #FFFFFF;
            --text-dark: #0F172A;
            --text-muted: #64748B;
            --shadow: rgba(0, 0, 0, 0.15);
            --gradient: linear-gradient(135deg, #1E3A8A, #3B82F6);
            --transition: all 0.3s ease;
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

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: var(--card-bg);
            padding: 24px;
            transition: var(--transition);
            box-shadow: 4px 0 20px var(--shadow);
            z-index: 1000;
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
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--text-muted);
        }

        .sidebar-logo {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            overflow: hidden;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            background: var(--gradient);
            -webkit-background-clip: text;
            color: transparent;
        }

        .sidebar-menu {
            list-style: none;
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
            font-weight: 500;
            transition: var(--transition);
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: var(--primary);
            color: var(--card-bg);
            box-shadow: 0 4px 12px var(--shadow);
        }

        .sidebar-menu a i {
            font-size: 1.2rem;
        }

        .container {
            margin-left: 280px;
            padding: 40px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            background: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120"%3E%3Cg fill="%231E3A8A" fill-opacity="0.05"%3E%3Cpath d="M60 20 L80 60 L60 100 L40 60 Z"/%3E%3C/g%3E%3C/svg%3E');
            background-size: 240px;
        }

        .container.full-width {
            margin-left: 80px;
        }

        .dashboard-header {
            background: var(--gradient);
            padding: 24px 32px;
            border-radius: 12px;
            box-shadow: 0 6px 20px var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
            color: var(--card-bg);
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
            transition: all 0.5s ease;
            opacity: 0;
            z-index: 0;
        }

        .dashboard-header:hover::after {
            opacity: 0.2;
            top: -20%;
            left: -20%;
        }

        .header-title {
            font-size: 1.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1;
        }

        .header-title span#datetime {
            font-size: 1rem;
            font-weight: 400;
            opacity: 0.9;
        }

        .toggle-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: var(--card-bg);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 10px;
            border-radius: 8px;
            transition: var(--transition);
        }

        .toggle-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .user-info {
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(5px);
            z-index: 1;
        }

        .main-content {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 6px 20px var(--shadow);
            margin-bottom: 40px;
            transition: var(--transition);
        }

        .main-content:hover {
            box-shadow: 0 8px 24px var(--shadow);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 2px solid rgba(30, 58, 138, 0.1);
        }

        .section-header h2 {
            font-size: 1.75rem;
            font-weight: 600;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .search-bar {
            padding: 12px 20px;
            border: 1px solid var(--text-muted);
            border-radius: 8px;
            width: 300px;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
            background: var(--card-bg);
            box-shadow: inset 0 1px 3px var(--shadow);
        }

        .search-bar:focus {
            width: 340px;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.15);
            outline: none;
        }

        .cost-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px var(--shadow);
        }

        .cost-table th {
            background: var(--primary);
            color: var(--card-bg);
            padding: 16px 20px;
            font-weight: 600;
            text-align: left;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cost-table td {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(30, 58, 138, 0.1);
            font-size: 0.95rem;
            color: var(--text-dark);
        }

        .cost-table tr:last-child td {
            border-bottom: none;
        }

        .cost-table tr:hover {
            background: rgba(30, 58, 138, 0.05);
            transition: var(--transition);
        }

        .cost-table td:nth-child(3),
        .cost-table td:nth-child(4) {
            color: var(--secondary);
            font-weight: 500;
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
            .search-bar {
                width: 100%;
            }
            .search-bar:focus {
                width: 100%;
            }
            .dashboard-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
                padding: 20px;
            }
            .header-title span#datetime {
                font-size: 0.9rem;
            }
            .container {
                padding: 20px;
            }
            .cost-table th,
            .cost-table td {
                font-size: 0.85rem;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="<?php echo BASE_PATH; ?>/src/assets/images/logo.png" alt="A2Z Engineering Logo">
            </div>
            <h2>A2Z Engineering</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?php echo BASE_PATH; ?>/admin/dashboard"><i class="fas fa-tachometer-alt"></i> <span class="sidebar-text">Dashboard</span></a></li>
            <li><a href="<?php echo BASE_PATH; ?>/admin/tables"><i class="fas fa-table"></i> <span class="sidebar-text">Tables</span></a></li>
            <li><a href="<?php echo BASE_PATH; ?>/admin/records" class="active"><i class="fas fa-file-alt"></i> <span class="sidebar-text">Records</span></a></li>
            <li><a href="<?php echo BASE_PATH; ?>/logout"><i class="fas fa-sign-out-alt"></i> <span class="sidebar-text">Logout</span></a></li>
        </ul>
    </div>

    <div class="container" id="container">
        <div class="dashboard-header">
            <div class="header-title">
                <button class="toggle-btn" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                Site Cost Calculation
                <span id="datetime"><?php echo date('l, F j, Y - H:i:s'); ?></span>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($data['username']); ?> | DB: <?php echo htmlspecialchars($data['dbname']); ?></span>
            </div>
        </div>

        <div class="main-content">
            <div class="section-header">
                <h2>Cost Breakdown by Site</h2>
                <input type="text" class="search-bar" placeholder="Search sites..." onkeyup="filterTable(this)" aria-label="Search sites">
            </div>
            <table class="cost-table">
    <thead>
        <tr>
            <th>Site Name</th>
            <th>Total Jobs</th>
            <th>Total Cost</th>
            <th>Avg Cost/Job</th>
        </tr>
    </thead>
    <tbody id="cost-table-body">
        <?php foreach ($data['siteCosts'] as $site): ?>
            <tr>
                <td><?php echo htmlspecialchars($site['site_name'] ?? 'Unknown'); ?></td>
                <td><?php echo htmlspecialchars($site['total_jobs']); ?></td>
                <td>$<?php echo number_format($site['total_cost'], 2); ?></td>
                <td>$<?php echo number_format($site['avg_cost_per_job'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const container = document.getElementById('container');
            sidebar.classList.toggle('collapsed');
            container.classList.toggle('full-width');
        }

        function filterTable(input) {
            const searchTerm = input.value.toLowerCase();
            const tbody = document.getElementById('cost-table-body');
            const rows = tbody.getElementsByTagName('tr');
            Array.from(rows).forEach(row => {
                const siteName = row.cells[0].textContent.toLowerCase();
                row.style.display = siteName.includes(searchTerm) ? '' : 'none';
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