<?php
$activePage = $activePage ?? 'dashboard';
?>
<!-- Sidebar -->
<div class="sidebar bg-white w-64 h-screen fixed left-0 top-0 border-r border-slate-200 z-50 flex flex-col transition-all duration-300" id="sidebar">
    <!-- Branding Header -->
    <div class="p-6 border-b border-slate-200">
        <a href="https://a2zengineering.lk/" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-3 group">
            <img src="<?php echo BASE_PATH; ?>/src/assets/images/logo.png" alt="A2Z Logo" class="w-10 h-10 object-contain transition-transform duration-300 group-hover:scale-105">
            <div>
                <h2 class="font-bold text-slate-800 text-sm tracking-wide">A2Z Engineering</h2>
                <span class="text-[10px] font-semibold text-emerald-600 tracking-wider uppercase">Internal DBMS</span>
            </div>
        </a>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="flex-1 py-6 overflow-y-auto">
        <div class="px-4 mb-4">
            <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase px-3">Main Portal</span>
        </div>
        <ul class="space-y-1.5 px-3">
            <li>
                <a href="<?php echo BASE_PATH; ?>/admin/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 <?php echo $activePage === 'dashboard' ? 'bg-gradient-to-r from-emerald-600 to-green-500 text-white font-medium shadow-md shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    <i class="fas fa-th-large text-sm <?php echo $activePage === 'dashboard' ? 'text-white' : 'text-slate-450'; ?>"></i>
                    <span class="text-sm">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_PATH; ?>/admin/tables" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 <?php echo $activePage === 'tables' ? 'bg-gradient-to-r from-emerald-600 to-green-500 text-white font-medium shadow-md shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    <i class="fas fa-database text-sm <?php echo $activePage === 'tables' ? 'text-white' : 'text-slate-455'; ?>"></i>
                    <span class="text-sm">Data Tables</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_PATH; ?>/admin/reports" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 <?php echo $activePage === 'reports' ? 'bg-gradient-to-r from-emerald-600 to-green-500 text-white font-medium shadow-md shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    <i class="fas fa-chart-line text-sm <?php echo $activePage === 'reports' ? 'text-white' : 'text-slate-455'; ?>"></i>
                    <span class="text-sm">Reports Portal</span>
                </a>
            </li>
        </ul>

        <div class="px-4 mt-8 mb-4">
            <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase px-3">Support & External</span>
        </div>
        <ul class="space-y-1.5 px-3">
            <li>
                <a href="<?php echo BASE_PATH; ?>/support" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all duration-200">
                    <i class="fas fa-headset text-sm text-slate-400"></i>
                    <span class="text-sm">Help Support</span>
                </a>
            </li>
            <li>
                <a href="https://a2zengineering.lk/" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all duration-200">
                    <i class="fas fa-globe text-sm text-slate-400"></i>
                    <span class="text-sm">Corporate Site</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- User Profile & Logout section at bottom -->
    <div class="p-4 border-t border-slate-200 bg-slate-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">
                    <?php echo strtoupper(substr($data['username'] ?? 'A', 0, 1)); ?>
                </div>
                <div class="truncate max-w-[110px]">
                    <p class="text-xs font-semibold text-slate-800 truncate"><?php echo htmlspecialchars($data['username'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="text-[10px] text-slate-500 truncate"><?php echo htmlspecialchars($data['dbname'] ?? 'operational_db', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
            <a href="<?php echo htmlspecialchars(BASE_PATH . '/logout', ENT_QUOTES, 'UTF-8'); ?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Logout">
                <i class="fas fa-sign-out-alt text-sm"></i>
            </a>
        </div>
    </div>
</div>
