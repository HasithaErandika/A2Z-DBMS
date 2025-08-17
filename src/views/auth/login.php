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
    <!-- Enhanced Background Elements -->
    <div id="background-animation"></div>
    <div id="floating-elements"></div>
    
    <!-- Enhanced Navigation -->
    <nav class="navbar navbar-expand-lg" role="navigation" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand" href="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="brand-icon">
                    <i class="fas fa-solar-panel icon-solar" aria-hidden="true"></i>
                </div>
                <span class="brand-text">A2Z Engineering</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlspecialchars(BASE_PATH . '/support', ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="fas fa-headset me-1"></i>Support
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Enhanced Login Container -->
    <div class="login-container">
        <div class="login-content">
            <!-- Left Panel - Branding -->
            <div class="login-branding">
                <div class="branding-content">
                    <div class="brand-logo">
                        <div class="logo-circle">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                    <h1>A2Z Engineering</h1>
                    <p class="brand-tagline">Internal Database Management System</p>
                    <div class="brand-features">
                        <div class="feature-item">
                            <i class="fas fa-users"></i>
                            <span>Employee Portal</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-project-diagram"></i>
                            <span>Project Management</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance Analytics</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secure Access</span>
                        </div>
                    </div>
                    <div class="brand-stats">
                        <div class="stat-item">
                            <span class="stat-number">500+</span>
                            <span class="stat-label">Employees</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">50+</span>
                            <span class="stat-label">Active Projects</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">99.9%</span>
                            <span class="stat-label">System Uptime</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Login Form -->
            <div class="login-form-panel">
                <div class="login-box" role="main" aria-label="Login Section">
                    <div class="login-header">
                        <h2>Welcome Back</h2>
                        <p>Access your company database and manage internal operations</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <div class="alert-icon">
                                <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                            </div>
                            <div class="alert-content">
                                <strong>Login Failed</strong>
                                <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" method="POST" aria-label="Login Form" class="login-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                        
                        <div class="form-group">
                            <label for="db_username" class="form-label">
                                <i class="fas fa-user"></i>
                                Database Username
                            </label>
                            <div class="input-wrapper">
                                <input type="text" 
                                       class="form-control" 
                                       id="db_username" 
                                       name="db_username" 
                                       required 
                                       aria-required="true" 
                                       placeholder="Enter your username"
                                       autocomplete="username">
                                <div class="input-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="db_password" class="form-label">
                                <i class="fas fa-lock"></i>
                                Database Password
                            </label>
                            <div class="input-wrapper">
                                <input type="password" 
                                       class="form-control" 
                                       id="db_password" 
                                       name="db_password" 
                                       required 
                                       aria-required="true" 
                                       placeholder="Enter your password"
                                       autocomplete="current-password">
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-options">
                            <label class="checkbox-wrapper">
                                <input type="checkbox" id="remember" name="remember">
                                <span class="checkmark"></span>
                                Remember me
                            </label>
                            <a href="#" class="forgot-password">Forgot Password?</a>
                        </div>

                        <button type="submit" class="btn btn-login-user">
                            <span class="btn-content">
                                <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                                <span>Sign In</span>
                            </span>
                            <div class="btn-loader">
                                <div class="spinner"></div>
                            </div>
                        </button>
                    </form>

                    <div class="login-divider">
                        <span>or continue with</span>
                    </div>

                    <div class="alternative-login">
                        <button type="button" class="btn btn-google">
                            <i class="fab fa-google"></i>
                            Company Google Account
                        </button>
                        <button type="button" class="btn btn-microsoft">
                            <i class="fab fa-microsoft"></i>
                            Company Microsoft Account
                        </button>
                    </div>

                    <div class="login-footer">
                        <a href="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>" class="back-link">
                            <i class="fas fa-arrow-left" aria-hidden="true"></i>
                            <span>Return to Home</span>
                        </a>
                        <div class="signup-prompt">
                            New employee? <a href="#" class="signup-link">Contact HR for access</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Footer -->
    <footer role="contentinfo" class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0">&copy; <?php echo date('Y'); ?> A2Z Engineering | Advanced DBMS Solutions</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="footer-links">
                            <a href="<?php echo htmlspecialchars(BASE_PATH . '/privacy', ENT_QUOTES, 'UTF-8'); ?>">Privacy Policy</a>
                            <span class="separator">|</span>
                            <a href="<?php echo htmlspecialchars(BASE_PATH . '/terms', ENT_QUOTES, 'UTF-8'); ?>">Terms of Service</a>
                            <span class="separator">|</span>
                            <a href="<?php echo htmlspecialchars(BASE_PATH . '/support', ENT_QUOTES, 'UTF-8'); ?>">Support</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script>
        // Enhanced form interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            const passwordToggle = document.querySelector('.password-toggle');
            const passwordInput = document.querySelector('#db_password');
            
            if (passwordToggle && passwordInput) {
                passwordToggle.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }

            // Enhanced form validation
            const form = document.querySelector('.login-form');
            const inputs = form.querySelectorAll('.form-control');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.parentElement.classList.remove('focused');
                    }
                });
                
                input.addEventListener('input', function() {
                    this.classList.toggle('is-valid', this.value.trim().length > 0);
                    this.classList.toggle('is-invalid', this.value.trim().length === 0);
                });
            });

            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('.btn-login-user');
                submitBtn.classList.add('loading');
                
                // Simulate loading (remove in production)
                setTimeout(() => {
                    submitBtn.classList.remove('loading');
                }, 2000);
            });

            // Enhanced button interactions
            document.querySelectorAll('.btn').forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px) scale(1.02)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
                
                btn.addEventListener('mousedown', function() {
                    this.style.transform = 'translateY(1px) scale(0.98)';
                });
                
                btn.addEventListener('mouseup', function() {
                    this.style.transform = 'translateY(-2px) scale(1.02)';
                });
            });
        });

        // Background animation
        function createBackgroundElement() {
            const element = document.createElement('div');
            element.className = 'bg-element';
            element.style.cssText = `
                position: absolute;
                width: ${Math.random() * 100 + 50}px;
                height: ${Math.random() * 100 + 50}px;
                background: linear-gradient(45deg, rgba(30, 58, 138, 0.1), rgba(245, 158, 11, 0.1));
                border-radius: 50%;
                animation: float ${Math.random() * 20 + 15}s linear infinite;
                left: ${Math.random() * 100}vw;
                top: ${Math.random() * 100}vh;
                z-index: -1;
            `;
            document.getElementById('background-animation').appendChild(element);
            setTimeout(() => element.remove(), 25000);
        }

        // Floating elements
        function createFloatingElement() {
            const element = document.createElement('div');
            const icons = ['fa-solar-panel', 'fa-bolt', 'fa-database', 'fa-chart-line'];
            const randomIcon = icons[Math.floor(Math.random() * icons.length)];
            
            element.innerHTML = `<i class="fas ${randomIcon}"></i>`;
            element.className = 'floating-element';
            element.style.cssText = `
                position: absolute;
                font-size: ${Math.random() * 2 + 1}rem;
                color: rgba(30, 58, 138, 0.1);
                animation: floatIcon ${Math.random() * 15 + 10}s linear infinite;
                left: ${Math.random() * 100}vw;
                top: ${Math.random() * 100}vh;
                z-index: -1;
            `;
            document.getElementById('floating-elements').appendChild(element);
            setTimeout(() => element.remove(), 20000);
        }

        setInterval(createBackgroundElement, 3000);
        setInterval(createFloatingElement, 4000);

        // Smooth scroll for navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
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