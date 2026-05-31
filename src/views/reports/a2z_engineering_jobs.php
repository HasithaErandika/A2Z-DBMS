<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
}

$error = $error ?? null;
$username = $username ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - A2Z Engineering Jobs</title>
    <?php require_once __DIR__ . '/../partials/theme.php'; ?>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased overflow-x-hidden min-h-screen">

    <?php 
    $activePage = 'reports';
    $headerTitle = 'A2Z Engineering Jobs';
    $headerSubtitle = 'A comprehensive overview of all A2Z Engineering jobs and their status.';
    $breadcrumb = 'Reports / A2Z Jobs';
    require_once __DIR__ . '/../partials/sidebar.php';
    ?>

    <!-- Main Container -->
    <div class="ml-64 transition-all duration-300 min-h-screen flex flex-col justify-between" id="container">
        <div>
            <?php require_once __DIR__ . '/../partials/header.php'; ?>

            <main class="p-8">
                <!-- Action bar -->
                <div class="flex items-center justify-between mb-6">
                    <div class="text-xs text-slate-500 font-medium">Status: Under Development</div>
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                        <i class="fas fa-arrow-left"></i> Back to Reports
                    </a>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center space-x-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php else: ?>

                <!-- Feature card -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-12 text-center max-w-2xl mx-auto">
                    <div class="w-16 h-16 bg-emerald-50 border border-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-600">
                        <i class="fas fa-briefcase text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">A2Z Engineering Jobs Overview</h3>
                    <p class="text-sm text-slate-500 mb-6 leading-relaxed">
                        This analytical module is currently being finalized. Once compiled, it will aggregate key performance indicators (KPIs) for all operational site jobs.
                    </p>
                    
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-6 text-left space-y-3.5 max-w-md mx-auto">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Planned Integration Features:</h4>
                        <ul class="text-xs text-slate-650 space-y-2.5">
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-emerald-500"></i> Job status dashboard with interactive KPIs
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-emerald-500"></i> Project-wise job clustering and group filters
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-emerald-500"></i> Engineer work logs and performance indexing
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-emerald-500"></i> Job lifecycle milestone tracking
                            </li>
                        </ul>
                    </div>
                </div>

                <?php endif; ?>
            </main>
        </div>

        <!-- Corporate Footer -->
        <footer class="border-t border-slate-200 bg-white py-6 px-8 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center space-x-2">
                <img class="h-6 w-auto opacity-70" src="<?php echo BASE_PATH; ?>/src/assets/images/logo.png" alt="A2Z Logo">
                <span>&copy; <?php echo date('Y'); ?> A2Z Engineering. Internal DBMS Portal.</span>
            </div>
            <div class="flex space-x-6">
                <a href="https://a2zengineering.lk" target="_blank" rel="noopener noreferrer" class="hover:text-slate-900 transition-colors">Corporate Site</a>
                <span class="text-slate-300">|</span>
                <span>DBMS Stable v2.2.0</span>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const container = document.getElementById('container');
            sidebar.classList.toggle('hidden');
            if (window.innerWidth >= 1024) {
                container.classList.toggle('ml-0');
                container.classList.toggle('ml-64');
            }
        }
    </script>
</body>
</html>
