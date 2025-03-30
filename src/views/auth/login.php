<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - A2Z Engineering DBMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://a2zengineering.lk/wp-content/uploads/2023/03/Solar_4.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: white;
            font-family: 'Poppins', sans-serif;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        .navbar-brand {
            font-weight: 700;
            color: #2c3e50;
        }
        .nav-link {
            color: #2c3e50;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #3498db;
        }
        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .login-box {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 0 25px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 450px;
            transition: all 0.3s ease;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.9rem;
            border-radius: 10px;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            box-shadow: none;
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .btn-select-user {
            background: linear-gradient(135deg, #2ecc71, #27ae60); /* Green gradient for User */
            border: none;
            padding: 1rem;
            width: 100%;
            margin-top: 1rem;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-select-user:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
        }
        .btn-select-admin {
            background: linear-gradient(135deg, #e74c3c, #c0392b); /* Red gradient for Admin */
            border: none;
            padding: 1rem;
            width: 100%;
            margin-top: 1rem;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-select-admin:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        }
        .btn-login-user {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            border: none;
            padding: 1rem;
            width: 100%;
            margin-top: 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-login-user:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(46, 204, 113, 0.5);
        }
        .btn-login-admin {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            border: none;
            padding: 1rem;
            width: 100%;
            margin-top: 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-login-admin:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(231, 76, 60, 0.5);
        }
        .back-home {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        .back-home:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
            color: white;
        }
        .alert {
            background: rgba(220, 53, 69, 0.3);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: white;
            margin-bottom: 1.5rem;
            border-radius: 10px;
        }
        footer {
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 1.5rem 0;
            text-align: center;
        }
        .user-theme .login-box {
            border: 2px solid rgba(46, 204, 113, 0.4);
            box-shadow: 0 0 20px rgba(46, 204, 113, 0.2);
        }
        .admin-theme .login-box {
            border: 2px solid rgba(231, 76, 60, 0.4);
            box-shadow: 0 0 20px rgba(231, 76, 60, 0.2);
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
        <div class="login-box <?php echo isset($_POST['user_type']) && $_POST['user_type'] === 'admin' ? 'admin-theme' : (isset($_POST['user_type']) ? 'user-theme' : ''); ?>">
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
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_PATH; ?>/login" method="POST">
                    <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($_POST['user_type']); ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary <?php echo $_POST['user_type'] === 'admin' ? 'btn-login-admin' : 'btn-login-user'; ?>">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>
                
                <div class="text-center">
                    <a href="<?php echo BASE_PATH; ?>/login" class="back-home">
                        <i class="fas fa-arrow-left me-2"></i>Back to Selection
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="text-center">
                <a href="<?php echo BASE_PATH; ?>" class="back-home">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
            </div>
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