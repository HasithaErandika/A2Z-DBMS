<?php
// src/views/home/support.php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}
$isLoggedIn = isset($_SESSION['username']);
$dashboardUrl = BASE_PATH . '/admin';
$loginUrl = BASE_PATH . '/login';
$logoutUrl = BASE_PATH . '/logout';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - A2Z Engineering DBMS</title>
    <!-- Plus Jakarta Sans Font -->
    <?php require_once __DIR__ . '/../partials/theme.php'; ?>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-850 min-h-screen flex flex-col justify-between selection:bg-brand-green selection:text-white">

    <!-- Top Navigation Bar -->
    <nav class="border-b border-slate-200 bg-white/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <!-- Brand Logo -->
            <a href="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>" class="flex items-center space-x-3 group">
                <img class="h-10 w-auto transition-transform duration-300 group-hover:scale-105" src="<?php echo BASE_PATH; ?>/src/assets/images/logo.png" alt="A2Z Logo">
                <span class="font-bold text-lg tracking-wider text-slate-900">A2Z <span class="text-brand-green">DBMS</span></span>
            </a>

            <!-- Desktop Menu -->
            <div class="flex items-center space-x-8">
                <a href="https://a2zengineering.lk/" target="_blank" rel="noopener noreferrer" class="text-slate-600 hover:text-slate-900 font-medium transition-colors flex items-center gap-2">
                    <i class="fas fa-globe text-sm"></i> A2Z Website
                </a>
                <a href="<?php echo htmlspecialchars(BASE_PATH . '/support', ENT_QUOTES, 'UTF-8'); ?>" class="text-slate-900 font-medium flex items-center gap-2 border-b-2 border-brand-green pb-1 px-1">
                    <i class="fas fa-headset text-sm text-brand-green"></i> Support
                </a>
                
                <?php if ($isLoggedIn): ?>
                    <a href="<?php echo htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>" class="bg-brand-green hover:bg-brand-greenHover text-white px-5 py-2 rounded-lg font-medium transition-all duration-300 flex items-center gap-2 text-sm shadow-lg">
                        <i class="fas fa-chart-pie"></i> Console
                    </a>
                <?php else: ?>
                    <a href="<?php echo htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8'); ?>" class="bg-brand-green hover:bg-brand-greenHover text-white px-5 py-2 rounded-lg font-medium transition-all duration-300 flex items-center gap-2 text-sm shadow-lg">
                        <i class="fas fa-sign-in-alt text-sm"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Support Content -->
    <main class="flex-grow bg-white flex flex-col justify-center items-center py-16 px-6">
        <div class="max-w-4xl w-full space-y-12">
            <!-- Header text -->
            <div class="text-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-brand-green/10 border border-brand-green/20 flex items-center justify-center mx-auto text-brand-green text-2xl">
                    <i class="fas fa-headset"></i>
                </div>
                <h1 class="text-4xl font-extrabold text-slate-900">Help & System Support</h1>
                <p class="text-slate-655 max-w-xl mx-auto text-sm">
                    Access guidelines, system answers, and submit requests directly to the DBMS IT Administration team.
                </p>
            </div>

            <!-- Grid Layout: FAQ & Ticket -->
            <div class="grid md:grid-cols-12 gap-8">
                <!-- FAQs: 5 cols -->
                <div class="md:col-span-5 space-y-6">
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-question-circle text-brand-green text-lg"></i> System FAQ
                    </h2>

                    <div class="space-y-4">
                        <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl">
                            <h3 class="font-semibold text-slate-800 text-sm mb-1.5">How do I request credentials?</h3>
                            <p class="text-slate-600 text-xs leading-relaxed">
                                Please contact the operations or HR administrator desk. Credentials can only be assigned to registered engineers and administrative staff.
                            </p>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl">
                            <h3 class="font-semibold text-slate-800 text-sm mb-1.5">Forgot my password?</h3>
                            <p class="text-slate-600 text-xs leading-relaxed">
                                Passwords can be reset instantly by the IT team. Open a support ticket on the right panel or email admin@a2zengineering.lk directly.
                            </p>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl">
                            <h3 class="font-semibold text-slate-800 text-sm mb-1.5">Are session limits enforced?</h3>
                            <p class="text-slate-600 text-xs leading-relaxed">
                                Yes. For security compliance, database sessions will automatically expire after 1 hour of inactivity.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Ticket Form: 7 cols -->
                <div class="md:col-span-7 bg-white border border-slate-200 p-8 rounded-2xl shadow-xl space-y-6">
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-envelope-open-text text-brand-green text-lg"></i> Submit ticket
                    </h2>

                    <form class="space-y-4" onsubmit="event.preventDefault(); alert('Support ticket sent successfully! IT admin desk will contact you via email.');">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Your Name</label>
                                <input type="text" required class="block w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green" placeholder="e.g. Ruwan Silva">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Email Address</label>
                                <input type="email" required class="block w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green" placeholder="e.g. ruwan@a2zengineering.lk">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Subject</label>
                            <input type="text" required class="block w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green" placeholder="e.g. Password Reset / Maintenance View Bug">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Message Detail</label>
                            <textarea rows="4" required class="block w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green" placeholder="Outline the issue or bug details here..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-brand-green hover:bg-brand-greenHover text-white py-3 rounded-lg font-bold text-xs uppercase tracking-wider transition-all duration-300 shadow-lg shadow-brand-green/10">
                            <i class="fas fa-paper-plane mr-2"></i> Submit Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Corporate Footer -->
    <footer class="border-t border-slate-200 bg-white py-8 px-6 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center space-x-2">
                <img class="h-6 w-auto opacity-70" src="<?php echo BASE_PATH; ?>/src/assets/images/logo.png" alt="A2Z Logo">
                <span>&copy; <?php echo date('Y'); ?> A2Z Engineering. All rights reserved. Internal System.</span>
            </div>
            <div class="flex space-x-6">
                <a href="<?php echo htmlspecialchars(BASE_PATH . '/login', ENT_QUOTES, 'UTF-8'); ?>" class="hover:text-slate-900 transition-colors">Portal Login</a>
                <a href="https://a2zengineering.lk" target="_blank" rel="noopener noreferrer" class="hover:text-slate-900 transition-colors">Corporate Site</a>
                <span class="text-slate-400">|</span>
                <span>DBMS Version 2.2.0 (Stable)</span>
            </div>
        </div>
    </footer>

</body>
</html>
