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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-poppins bg-gradient-to-br from-gray-50 to-gray-100 text-gray-800 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <img class="h-12 w-auto" src="<?php echo BASE_PATH; ?>/src/assets/images/longLogoB.png" alt="A2Z Engineering Logo">
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-300">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/support', ENT_QUOTES, 'UTF-8'); ?>" class="text-gray-700 hover:text-blue-600 font-medium transition-colors duration-300">
                        <i class="fas fa-headset mr-2"></i>Support
                    </a>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white shadow-lg">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    <i class="fas fa-home mr-2"></i>Home
                </a>
                <a href="<?php echo htmlspecialchars(BASE_PATH . '/support', ENT_QUOTES, 'UTF-8'); ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    <i class="fas fa-headset mr-2"></i>Support
                </a>
            </div>
        </div>
    </nav>

    <!-- Login Container -->
    <div class="flex-grow flex items-center justify-center p-4">
        <div class="w-full max-w-6xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="md:flex">
                    <!-- Left Panel - Branding -->
                    <div class="md:w-1/2 bg-gradient-to-br from-blue-600 to-indigo-700 p-12 text-white">
                        <div class="flex flex-col h-full justify-center">
                            <div class="text-center mb-10">
                                <img class="h-24 w-auto mx-auto mb-6" src="<?php echo BASE_PATH; ?>/src/assets/images/logo.png" alt="A2Z Engineering Logo">
                                <h1 class="text-3xl font-bold mb-4">A2Z Engineering</h1>
                                <p class="text-blue-100 text-lg">Internal Database Management System</p>
                            </div>
                            
                            <div class="space-y-6">
                                 <div class="md:w-1/2 flex justify-center">
                    <div class="relative">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl w-80 h-80 md:w-96 md:h-96 flex items-center justify-center shadow-2xl">
                            <div class="bg-white bg-opacity-20 rounded-full w-64 h-64 md:w-80 md:h-80 flex items-center justify-center">
                                <div class="bg-white bg-opacity-30 rounded-full w-48 h-48 md:w-64 md:h-64 flex items-center justify-center">
                                    <i class="fas fa-database text-white text-6xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-4 -right-4 bg-yellow-400 rounded-full w-24 h-24 flex items-center justify-center shadow-lg">
                            <i class="fas fa-users text-white text-3xl"></i>
                        </div>
                        <div class="absolute -bottom-4 -left-4 bg-green-500 rounded-full w-24 h-24 flex items-center justify-center shadow-lg">
                            <i class="fas fa-chart-line text-white text-3xl"></i>
                        </div>
                    </div>
                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Panel - Login Form -->
                    <div class="md:w-1/2 p-12">
                        <div class="max-w-sm mx-auto">
                            <div class="text-center mb-10">
                                <h2 class="text-3xl font-bold text-gray-900 mb-3">Welcome Back</h2>
                                <p class="text-gray-600">Access your company database and manage internal operations</p>
                            </div>

                            <?php if (isset($error)): ?>
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-circle text-red-500"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-red-700">
                                                <strong>Login Failed:</strong> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <form action="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" method="POST" class="space-y-6">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                                
                                <div>
                                    <label for="db_username" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-user mr-2 text-blue-600"></i>Database Username
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <input type="text" 
                                               id="db_username" 
                                               name="db_username" 
                                               required 
                                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                               placeholder="Enter your username"
                                               autocomplete="username">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="db_password" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-lock mr-2 text-blue-600"></i>Database Password
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <input type="password" 
                                               id="db_password" 
                                               name="db_password" 
                                               required 
                                               class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                               placeholder="Enter your password"
                                               autocomplete="current-password">
                                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center password-toggle">
                                            <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                                    </div>
                                    <div class="text-sm">
                                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Forgot Password?</a>
                                    </div>
                                </div>
                                
                                <div>
                                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-700 hover:to-blue-900 transition-all duration-300 shadow-md hover:shadow-lg">
                                        <span class="btn-content flex items-center justify-center">
                                            <i class="fas fa-sign-in-alt mr-2"></i>
                                            <span>Sign In</span>
                                        </span>
                                    </button>
                                </div>
                            </form>
                            
                            <div class="mt-8 text-center">
                                <a href="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>" class="inline-flex items-center text-blue-600 font-medium hover:text-blue-800 transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    <span>Return to Home</span>
                                </a>
                                <div class="mt-4 text-sm text-gray-600">
                                    New employee? <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Contact HR for access</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                
            
            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 mb-4 md:mb-0">&copy; 2024 A2Z Engineering. Internal Database System - Company Use Only.</p>
                    <p class="text-gray-400">Version 2.1.0 | Last Updated: <?php echo date('M Y'); ?></p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Password visibility toggle
        document.querySelector('.password-toggle').addEventListener('click', function() {
            const passwordInput = document.querySelector('#db_password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form submission with loading state
        const form = document.querySelector('form');
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<span class="flex items-center justify-center"><i class="fas fa-spinner fa-spin mr-2"></i>Signing In...</span>';
            submitBtn.disabled = true;
            
            // In a real implementation, you would remove this timeout
            // and let the form submission complete naturally
            setTimeout(() => {
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
            }, 2000);
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