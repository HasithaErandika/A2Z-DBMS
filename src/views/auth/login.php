<?php
// Define BASE_PATH dynamically if not set
if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to A2Z Engineering DBMS for Solar, AC, and Electrical Power Management">
    <title>Login - A2Z Engineering DBMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/src/assets/css/login.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg" role="navigation" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand" href="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>">
                <i class="fas fa-solar-panel icon-solar" aria-hidden="true"></i> A2Z Engineering
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlspecialchars(BASE_PATH . '/support', ENT_QUOTES, 'UTF-8'); ?>">Support</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="login-container">
        <div class="login-box" role="main" aria-label="Login Section">
            <div class="login-header">
                <h1>A2Z Engineering</h1>
                <p>Powering Solar, AC, and Electrical Solutions</p>
            </div>

            <?php if (!isset($_POST['user_type'])): ?>
                <form action="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" method="POST" aria-label="Select User Type">
                    <button type="submit" name="user_type" value="user" class="btn btn-select-user">
                        <i class="fas fa-user" aria-hidden="true"></i> Login as User
                    </button>
                    <button type="submit" name="user_type" value="admin" class="btn btn-select-admin">
                        <i class="fas fa-user-shield" aria-hidden="true"></i> Login as Admin
                    </button>
                </form>
            <?php else: ?>
                <div class="role-display <?php echo htmlspecialchars($_POST['user_type'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo ucfirst(htmlspecialchars($_POST['user_type'], ENT_QUOTES, 'UTF-8')); ?> Login
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert" role="alert">
                        <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" method="POST" aria-label="Login Form">
                    <input type="hidden" name="user_type" value="<?php echo htmlspecialchars($_POST['user_type'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="mb-4">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required aria-required="true" placeholder="Enter username">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required aria-required="true" placeholder="Enter password">
                    </div>
                    <button type="submit" class="btn <?php echo $_POST['user_type'] === 'admin' ? 'btn-login-admin' : 'btn-login-user'; ?>">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Login
                    </button>
                </form>
                <a href="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" class="back-link">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to Role Selection
                </a>
            <?php endif; ?>

            <a href="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>" class="back-link">
                <i class="fas fa-home" aria-hidden="true"></i> Return to Home
            </a>
        </div>
    </div>

    <footer role="contentinfo">
        <div class="container">
            <p class="mb-0">© <?php echo date('Y'); ?> A2Z Engineering | <a href="<?php echo htmlspecialchars(BASE_PATH . '/privacy', ENT_QUOTES, 'UTF-8'); ?>">Privacy Policy</a> | <a href="<?php echo htmlspecialchars(BASE_PATH . '/terms', ENT_QUOTES, 'UTF-8'); ?>">Terms of Service</a></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script>
        // Button press animation
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (e.target.tagName !== 'BUTTON') return;
                this.style.transform = 'translateY(2px)';
                setTimeout(() => this.style.transform = 'translateY(0)', 150);
            });
        });

        // Real-time form validation feedback
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.toggle('is-invalid', !this.value.trim());
            });
        });

        // Smooth scroll for back links
        document.querySelectorAll('.back-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.href;
            });
        });
    </script>
</body>
</html>

<?php
// Example CSRF token function (implement this in your backend)
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
?>