<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - A2Z Engineering DBMS</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #2c3e50;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
            --border-radius: 8px;
            --box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            line-height: 1.6;
        }

        .admin-sidebar {
            min-height: 100vh;
            background: var(--primary-color);
            color: white;
            padding-top: 60px;
        }

        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.8rem 1rem;
            transition: var(--transition);
        }

        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
        }

        .admin-sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }

        .admin-content {
            padding-top: 60px;
        }

        .admin-header {
            background: white;
            box-shadow: var(--box-shadow);
            padding: 1rem;
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1000;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            margin-bottom: 1.5rem;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Admin Dashboard</h4>
                <div class="dropdown">
                    <button class="btn btn-link text-dark dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo BASE_PATH; ?>/admin/profile">
                            <i class="fas fa-user me-2"></i>Profile
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_PATH; ?>/admin/settings">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?php echo BASE_PATH; ?>/logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar">
                <div class="d-flex flex-column">
                    <a class="navbar-brand text-white mb-4 px-3" href="<?php echo BASE_PATH; ?>/admin">
                        <i class="fas fa-database me-2"></i>A2Z DBMS
                    </a>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="<?php echo BASE_PATH; ?>/admin">
                                <i class="fas fa-tachometer-alt"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_PATH; ?>/admin/databases">
                                <i class="fas fa-database"></i>Databases
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_PATH; ?>/admin/tables">
                                <i class="fas fa-table"></i>Tables
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_PATH; ?>/admin/users">
                                <i class="fas fa-users"></i>Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_PATH; ?>/admin/sql">
                                <i class="fas fa-code"></i>SQL Console
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 admin-content">
                <div class="container-fluid py-4">
                    <!-- Welcome Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h2 class="card-title">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                                    <p class="text-muted">Here's what's happening with your database system today.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-subtitle mb-2 text-muted">Databases</h6>
                                            <h3 class="card-title mb-0">0</h3>
                                        </div>
                                        <div class="stat-icon">
                                            <i class="fas fa-database fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                    <a href="<?php echo BASE_PATH; ?>/admin/databases" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-subtitle mb-2 text-muted">Tables</h6>
                                            <h3 class="card-title mb-0">0</h3>
                                        </div>
                                        <div class="stat-icon">
                                            <i class="fas fa-table fa-2x text-success"></i>
                                        </div>
                                    </div>
                                    <a href="<?php echo BASE_PATH; ?>/admin/tables" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-subtitle mb-2 text-muted">Users</h6>
                                            <h3 class="card-title mb-0">0</h3>
                                        </div>
                                        <div class="stat-icon">
                                            <i class="fas fa-users fa-2x text-info"></i>
                                        </div>
                                    </div>
                                    <a href="<?php echo BASE_PATH; ?>/admin/users" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-subtitle mb-2 text-muted">Queries</h6>
                                            <h3 class="card-title mb-0">0</h3>
                                        </div>
                                        <div class="stat-icon">
                                            <i class="fas fa-code fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                    <a href="<?php echo BASE_PATH; ?>/admin/sql" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Recent Activity</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-history fa-3x mb-3"></i>
                                        <p>No recent activity to display</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="<?php echo BASE_PATH; ?>/admin/databases/create" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Create New Database
                                        </a>
                                        <a href="<?php echo BASE_PATH; ?>/admin/users/create" class="btn btn-success">
                                            <i class="fas fa-user-plus me-2"></i>Add New User
                                        </a>
                                        <a href="<?php echo BASE_PATH; ?>/admin/sql" class="btn btn-info text-white">
                                            <i class="fas fa-terminal me-2"></i>SQL Console
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">System Status</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="status-icon bg-success me-3">
                                                    <i class="fas fa-check text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Server Status</h6>
                                                    <small class="text-muted">Online</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="status-icon bg-info me-3">
                                                    <i class="fas fa-database text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Database Version</h6>
                                                    <small class="text-muted">MySQL 8.0</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="status-icon bg-warning me-3">
                                                    <i class="fas fa-code text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">PHP Version</h6>
                                                    <small class="text-muted">8.2.12</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="status-icon bg-primary me-3">
                                                    <i class="fas fa-tachometer-alt text-white"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Server Load</h6>
                                                    <small class="text-muted">Low</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 