<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - DBMS Admin Dashboard</title>
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

        /* Enhanced Summary Grid */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .summary-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: none;
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient);
            transition: width 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px var(--shadow-hover);
        }

        .summary-card:hover::before {
            width: 8px;
        }

        .summary-card h3 {
            font-size: 1rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-card p {
            font-size: 2rem;
            font-weight: 700;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        /* Enhanced System Info Grid */
        .system-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
        }

        .system-info-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: none;
        }

        .system-info-card::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: var(--gradient);
            opacity: 0;
            transform: rotate(30deg);
            transition: all 0.5s ease;
            z-index: 0;
        }

        .system-info-card:hover::after {
            opacity: 0.05;
            top: -20%;
            left: -20%;
        }

        .system-info-card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px var(--shadow-hover);
        }

        .system-info-card h3 {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 1;
        }

        .system-info-card p {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--secondary);
            margin: 0;
            z-index: 1;
            word-break: break-word;
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
            .summary-grid, .system-info-grid {
                grid-template-columns: 1fr;
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
                <img src="src/assets/images/logo.png" alt="A2Z Engineering Logo">
            </div>
            <h2>A2Z Engineering</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?php echo BASE_PATH; ?>/admin/dashboard" class="active"><i class="fas fa-tachometer-alt"></i> <span class="sidebar-text">Dashboard</span></a></li>
            <li><a href="<?php echo BASE_PATH; ?>/admin/tables"><i class="fas fa-table"></i> <span class="sidebar-text">Tables</span></a></li>
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
                A2Z Engineering DBMS Dashboard
                <span id="datetime"><?php echo date('l, F j, Y - H:i:s'); ?></span>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($data['username']); ?> | DB: <?php echo htmlspecialchars($data['dbname']); ?></span>
            </div>
        </div>

        <div class="main-content">
            <div class="section-header">
                <h2>Summary Overview</h2>
            </div>
            <div class="summary-grid">
                <div class="summary-card">
                    <h3>Total Employees</h3>
                    <p><?php echo htmlspecialchars($data['summary']['total_employees']); ?></p>
                </div>
                <div class="summary-card">
                    <h3>Active Jobs</h3>
                    <p><?php echo htmlspecialchars($data['summary']['active_jobs']); ?></p>
                </div>
                <div class="summary-card">
                    <h3>Total Projects</h3>
                    <p><?php echo htmlspecialchars($data['summary']['total_projects']); ?></p>
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
                    <p><?php echo htmlspecialchars($data['summary']['todays_jobs']); ?></p>
                </div>
                <div class="summary-card">
                    <h3>Today's Expenses</h3>
                    <p><?php echo number_format($data['summary']['todays_expenses'], 2); ?></p>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="section-header">
                <h2>Database Management System</h2>
            </div>
            <div class="system-info-grid">
                <div class="system-info-card">
                    <h3>PHP Version</h3>
                    <p><?php echo htmlspecialchars($data['system_info']['php_version']); ?></p>
                </div>
                <div class="system-info-card">
                    <h3>MySQL Version</h3>
                    <p><?php echo htmlspecialchars($data['system_info']['mysql_version']); ?></p>
                </div>
                <div class="system-info-card">
                    <h3>Server Software</h3>
                    <p><?php echo htmlspecialchars($data['system_info']['server_software']); ?></p>
                </div>
                <div class="system-info-card">
                    <h3>Database Name</h3>
                    <p><?php echo htmlspecialchars($data['system_info']['db_name']); ?></p>
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
    </script>
</body>
</html>