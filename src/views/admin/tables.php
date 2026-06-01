<?php
// Ensure BASE_PATH is defined for relative paths
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}

// Check if $data is available
if (!isset($data) || !is_array($data)) {
    $data = [
        'username' => 'Unknown',
        'dbname' => 'Unknown',
        'operationalCards' => []
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A2Z Engineering Internal Database System - Data Tables Management">
    <title>A2Z Engineering - Data Tables</title>
    <?php require_once __DIR__ . '/../partials/theme.php'; ?>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased overflow-x-hidden min-h-screen">

    <?php 
    $activePage = 'tables';
    $headerTitle = 'Data Tables Registry';
    $headerSubtitle = 'Read, update, and manage all database relation tables';
    $breadcrumb = 'Data Tables';
    require_once __DIR__ . '/../partials/sidebar.php';
    ?>

    <!-- Main Container -->
    <div class="ml-64 transition-all duration-300 min-h-screen flex flex-col justify-between" id="container">
        <div>
            <?php require_once __DIR__ . '/../partials/header.php'; ?>

            <!-- System Messages -->
            <?php if (!empty($data['message'])): ?>
                <div class="px-8 mt-6">
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center space-x-2">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo htmlspecialchars($data['message'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($data['error'])): ?>
                <div class="px-8 mt-6">
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center space-x-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Main Content -->
            <main class="p-8 animate-fadeIn">
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">System Tables Overview</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Select a table schema below to view details, export CSV sheets, insert new logs, or modify active records.</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="text-xs text-slate-500 font-semibold bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                                <i class="fas fa-database text-slate-400"></i> Total Tables: <?php echo count($data['operationalCards']); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (empty($data['operationalCards'])): ?>
                        <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
                            <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                                <i class="fas fa-database text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 mb-1">No Data Tables Available</h3>
                            <p class="text-sm text-slate-500 max-w-sm mx-auto">No registry tables were found under this DBMS instance.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($data['operationalCards'] as $card): ?>
                            <!-- Card item -->
                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-slate-300 transition-all duration-200 group flex flex-col justify-between overflow-hidden">
                                <a href="<?php echo htmlspecialchars($card['link'], ENT_QUOTES, 'UTF-8'); ?>" class="p-6 block flex-1">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                                            <i class="fas <?php echo htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8'); ?> text-lg"></i>
                                        </div>
                                        <span class="text-slate-300 group-hover:text-emerald-600 transition-colors">
                                            <i class="fas fa-arrow-right text-xs"></i>
                                        </span>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-800 tracking-tight mb-1.5"><?php echo htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <p class="text-xs text-slate-500 leading-relaxed"><?php echo htmlspecialchars($card['desc'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </a>
                                <div class="bg-slate-50 border-t border-slate-100 px-6 py-3.5 flex items-center justify-end text-[11px] font-semibold text-slate-550">
                                    <a href="<?php echo htmlspecialchars($card['link'], ENT_QUOTES, 'UTF-8'); ?>" class="text-emerald-650 hover:underline">
                                        Open Sheet <i class="fas fa-chevron-right text-[8px] ml-0.5"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
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
        // Sidebar Toggling for small devices
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const container = document.getElementById('container');
            sidebar.classList.toggle('hidden');
            if (window.innerWidth >= 1024) {
                container.classList.toggle('ml-0');
                container.classList.toggle('ml-64');
            }
        }

        // Clock updating logic
        function updateClock() {
            const now = new Date();
            const dateStr = now.toLocaleDateString('en-US', {
                weekday: 'long',
                month: 'long',
                day: 'numeric'
            });
            const timeStr = now.toLocaleTimeString('en-US', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            const dateElement = document.getElementById('headerDateStr');
            const timeElement = document.getElementById('headerTimeStr');

            if (dateElement) dateElement.textContent = dateStr;
            if (timeElement) timeElement.textContent = timeStr;
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>