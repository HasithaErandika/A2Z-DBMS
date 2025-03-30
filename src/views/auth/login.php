<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - A2Z Engineering DBMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Roboto', sans-serif;
            color: #333;
        }
        .navbar {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        .navbar-brand {
            font-weight: 700;
            color: #3F51B5;
        }
        .nav-link {
            color: #555;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-link:hover {
            color: #3F51B5;
        }
        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-box {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #3F51B5;
        }
        .login-header p {
            color: #777;
            font-size: 0.9rem;
        }
        .form-control {
            border: 1px solid #ddd;
            padding: 0.75rem;
            border-radius: 4px;
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            border-color: #3F51B5;
            box-shadow: none;
        }
        .btn-select-user {
            background: #26A69A;
            border: none;
            padding: 0.75rem;
            width: 100%;
            margin-top: 1rem;
            border-radius: 4px;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        .btn-select-user:hover {
            background: #1c7d74;
        }
        .btn-select-admin {
            background: #3F51B5;
            border: none;
            padding: 0.75rem;
            width: 100%;
            margin-top: 1rem;
            border-radius: 4px;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        .btn-select-admin:hover {
            background: #2c3e9c;
        }
        .btn-login-user {
            background: #26A69A;
            border: none;
            padding: 0.75rem;
            width: 100%;
            margin-top: 1.5rem;
            border-radius: 4px;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        .btn-login-user:hover {
            background: #1c7d74;
        }
        .btn-login-admin {
            background: #3F51B5;
            border: none;
            padding: 0.75rem;
            width: 100%;
            margin-top: 1.5rem;
            border-radius: 4px;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        .btn-login-admin:hover {
            background: #2c3e9c;
        }
        .alert {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .role-display {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            color: #555;
        }
        .role-display.user {
            color: #26A69A;
        }
        .role-display.admin {
            color: #3F51B5;
        }
        .back-link {
            display: block;
            text-align: center;
            color: #555;
            text-decoration: none;
            margin-top: 1rem;
            transition: color 0.3s ease;
        }
        .back-link:hover {
            color: #3F51B5;
        }
        footer {
            background: #fff;
            color: #777;
            padding: 1rem 0;
            text-align: center;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_PATH; ?>">
                <i class="fas fa-database me-2"></i>A2Z Engineering
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_PATH; ?>">Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>A2Z Engineering</h1>
                <p>Database Management System</p>
            </div>

            <?php if (!isset($_POST['user_type'])): ?>
                <form action="<?php echo BASE_PATH; ?>/login" method="POST">
                    <button type="submit" name="user_type" value="user" class="btn btn-primary btn-select-user">
                        <i class="fas fa-user me-2"></i>Login as User
                    </button>
                    <button type="submit" name="user_type" value="admin" class="btn btn-primary btn-select-admin">
                        <i class="fas fa-user-shield me-2"></i>Login as Admin
                    </button>
                </form>
            <?php else: ?>
                <div class="role-display <?php echo $_POST['user_type']; ?>">
                    Selected Role: <?php echo ucfirst($_POST['user_type']); ?>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_PATH; ?>/login" method="POST">
                    <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($_POST['user_type']); ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary <?php echo $_POST['user_type'] === 'admin' ? 'btn-login-admin' : 'btn-login-user'; ?>">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>
                <a href="<?php echo BASE_PATH; ?>/login" class="back-link">
                    <i class="fas fa-arrow-left me-2"></i>Back to Selection
                </a>
            <?php endif; ?>
            
            <a href="<?php echo BASE_PATH; ?>" class="back-link">
                <i class="fas fa-home me-2"></i>Back to Home
            </a>
        </div>
    </div>

    <footer>
        <div class="container">
            <p class="mb-0">© <?php echo date('Y'); ?> A2Z Engineering. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>