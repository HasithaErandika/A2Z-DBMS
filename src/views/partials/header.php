<?php
$headerTitle = $headerTitle ?? 'System Dashboard';
$headerSubtitle = $headerSubtitle ?? 'Internal Database Management Overview';
$breadcrumb = $breadcrumb ?? 'Dashboard';
?>
<!-- Header -->
<header class="bg-white border-b border-slate-200 sticky top-0 z-40 px-8 py-5">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <!-- Title & Breadcrumb -->
        <div class="flex items-center space-x-3">
            <button class="lg:hidden p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none mr-2" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <!-- Breadcrumbs -->
                <div class="flex items-center space-x-1.5 text-xs text-slate-400 mb-1">
                    <span>A2Z Portal</span>
                    <i class="fas fa-chevron-right text-[8px]"></i>
                    <span class="text-slate-650 font-medium"><?php echo htmlspecialchars($breadcrumb); ?></span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <?php echo htmlspecialchars($headerTitle); ?>
                </h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5"><?php echo htmlspecialchars($headerSubtitle); ?></p>
            </div>
        </div>

        <!-- Quick actions, date/time info -->
        <div class="flex items-center gap-4 ml-auto md:ml-0">
            <!-- DateTime Widget -->
            <div class="hidden sm:flex items-center space-x-2.5 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-slate-600">
                <i class="far fa-calendar-alt text-slate-400 text-sm"></i>
                <div class="text-right">
                    <span class="block text-[10px] font-semibold text-slate-450 uppercase tracking-wider leading-none" id="headerDateStr"><?php echo date('l, F j'); ?></span>
                    <span class="block text-xs font-bold text-slate-700 leading-none mt-1" id="headerTimeStr"><?php echo date('H:i:s'); ?></span>
                </div>
            </div>

            <!-- Database connection details badge -->
            <div class="flex items-center space-x-2.5 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-database text-xs"></i>
                </div>
                <div>
                    <span class="block text-[9px] font-semibold text-slate-400 uppercase tracking-wider leading-none">Database</span>
                    <span class="block text-xs font-bold text-slate-700 leading-none mt-1 truncate max-w-[100px]" title="<?php echo htmlspecialchars($data['dbname'] ?? 'operational_db', ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($data['dbname'] ?? 'operational_db', ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</header>
