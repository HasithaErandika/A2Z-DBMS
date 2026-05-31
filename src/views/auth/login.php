<?php
// src/views/auth/login.php
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
    <?php require_once __DIR__ . '/../partials/theme.php'; ?>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between selection:bg-brand-green selection:text-white">

    <!-- Top Navigation -->
    <nav class="border-b border-slate-200 bg-white/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img class="h-10 w-auto" src="<?php echo BASE_PATH; ?>/src/assets/images/logo.png" alt="A2Z Logo">
                <span class="font-bold text-lg tracking-wider text-slate-900">A2Z <span class="text-brand-green">DBMS</span></span>
            </div>
            <div class="hidden md:flex items-center space-x-8">
                <a href="https://a2zengineering.lk/" target="_blank" rel="noopener noreferrer" class="text-slate-600 hover:text-slate-900 font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-globe text-sm"></i> A2Z Website
                </a>
                <a href="<?php echo htmlspecialchars(BASE_PATH . '/support', ENT_QUOTES, 'UTF-8'); ?>" class="text-slate-600 hover:text-slate-900 font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-headset text-sm"></i> Support
                </a>
            </div>
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-slate-600 hover:text-slate-900 focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-slate-200">
            <div class="px-6 py-4 space-y-3">
                <a href="https://a2zengineering.lk/" target="_blank" rel="noopener noreferrer" class="block text-slate-600 hover:text-slate-900 font-medium transition-colors">
                    <i class="fas fa-globe mr-2"></i>A2Z Website
                </a>
                <a href="<?php echo htmlspecialchars(BASE_PATH . '/support', ENT_QUOTES, 'UTF-8'); ?>" class="block text-slate-600 hover:text-slate-900 font-medium transition-colors">
                    <i class="fas fa-headset mr-2"></i>Support
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="flex-grow flex items-center justify-center p-6 bg-slate-50">
        <div class="w-full max-w-5xl bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
            <div class="md:flex">
                <!-- Left Panel: Branding & Welcome Message -->
                <div class="md:w-5/12 bg-gradient-to-br from-brand-green/10 via-slate-50 to-slate-100 p-12 flex flex-col justify-between border-r border-slate-250">
                    <div>
                        <img class="h-20 w-auto mb-8" src="<?php echo BASE_PATH; ?>/src/assets/images/logo.png" alt="A2Z Engineering Logo">
                        <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">
                            Secure <br><span class="text-brand-green">Enterprise</span> Portal
                        </h2>
                        <p class="mt-4 text-slate-600 leading-relaxed text-sm">
                            Welcome to the A2Z Engineering Database Management System. Access company resources, engineering jobs, wages, and operational summaries.
                        </p>
                    </div>
                    
                    <div class="mt-12 space-y-4">
                        <div class="flex items-center space-x-3 text-sm text-slate-700">
                            <span class="w-8 h-8 rounded-lg bg-slate-200 flex items-center justify-center text-brand-green">
                                <i class="fas fa-shield-alt"></i>
                            </span>
                            <span>AES-256 Data Encryption</span>
                        </div>
                        <div class="flex items-center space-x-3 text-sm text-slate-700">
                            <span class="w-8 h-8 rounded-lg bg-slate-200 flex items-center justify-center text-brand-green">
                                <i class="fas fa-database"></i>
                            </span>
                            <span>MySQL Production Cluster</span>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Login Form -->
                <div class="md:w-7/12 p-12 bg-white">
                    <div class="max-w-md mx-auto">
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold text-slate-900 mb-2">Sign In</h3>
                            <p class="text-slate-500 text-sm">Enter your database administrator credentials below.</p>
                        </div>

                        <?php if (isset($error)): ?>
                            <div class="bg-red-50 border border-red-200 p-4 mb-6 rounded-xl flex items-start space-x-3">
                                <i class="fas fa-exclamation-circle text-red-500 mt-1"></i>
                                <div>
                                    <span class="block text-sm font-semibold text-red-800">Authentication Failed</span>
                                    <span class="block text-xs text-red-600 mt-0.5"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" method="POST" class="space-y-6">
                            <div>
                                <label for="db_username" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
                                    Database Username
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           id="db_username" 
                                           name="db_username" 
                                           required 
                                           class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-slate-800 placeholder-slate-400 transition-colors text-sm" 
                                           placeholder="e.g. admin_user"
                                           autocomplete="username">
                                </div>
                            </div>
                            
                            <div>
                                <label for="db_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
                                    Database Password
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           id="db_password" 
                                           name="db_password" 
                                           required 
                                           class="block w-full pl-11 pr-11 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-brand-green text-slate-800 placeholder-slate-400 transition-colors text-sm" 
                                           placeholder="••••••••••••"
                                           autocomplete="current-password">
                                    <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center password-toggle text-slate-400 hover:text-slate-600">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between text-xs">
                                <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-50 border-slate-200 text-brand-green focus:ring-0">
                                    <span>Remember connection</span>
                                </label>
                                <a href="<?php echo htmlspecialchars(BASE_PATH . '/support', ENT_QUOTES, 'UTF-8'); ?>" class="text-brand-green hover:underline">
                                    Forgot password?
                                </a>
                            </div>
                            
                            <button type="submit" class="w-full bg-brand-green hover:bg-brand-greenHover text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 shadow-lg shadow-brand-green/20">
                                <span class="flex items-center justify-center space-x-2">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Authenticate Connection</span>
                                </span>
                            </button>
                        </form>

                        <div class="mt-8 text-center border-t border-slate-100 pt-6">
                            <span class="text-xs text-slate-500">
                                Need access? <a href="<?php echo htmlspecialchars(BASE_PATH . '/support', ENT_QUOTES, 'UTF-8'); ?>" class="text-brand-green hover:underline font-medium">Contact System Administrator</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <p>&copy; <?php echo date('Y'); ?> A2Z Engineering. All rights reserved. Confidential Internal System.</p>
            <p>System Engine v2.5.0</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const menuBtn = document.getElementById('mobile-menu-button');
        const menu = document.getElementById('mobile-menu');
        if (menuBtn && menu) {
            menuBtn.addEventListener('click', () => menu.classList.toggle('hidden'));
        }

        // Password visibility toggle
        const toggleBtn = document.querySelector('.password-toggle');
        const passwordInput = document.querySelector('#db_password');
        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    passwordInput.type = 'text' ? passwordInput.type = 'password' : null;
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
        }

        // Form submission loading indicator
        const loginForm = document.querySelector('form');
        if (loginForm) {
            loginForm.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="flex items-center justify-center space-x-2"><i class="fas fa-spinner fa-spin"></i><span>Authenticating...</span></span>';
                }
            });
        }
    </script>
</body>
</html>