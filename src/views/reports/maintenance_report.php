<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - Maintenance Report</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php require_once __DIR__ . '/../partials/theme.php'; ?>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased overflow-x-hidden min-h-screen">

    <?php 
    $activePage = 'reports';
    $headerTitle = 'Maintenance Schedule Cycles';
    $headerSubtitle = 'Periodic tracking and schedule status of system maintenance contracts.';
    $breadcrumb = 'Reports / Maintenance';
    require_once __DIR__ . '/../partials/sidebar.php';
    ?>

    <!-- Main Container -->
    <div class="ml-64 transition-all duration-300 min-h-screen flex flex-col justify-between" id="container">
        <div>
            <?php require_once __DIR__ . '/../partials/header.php'; ?>

            <main class="p-8 animate-fadeIn">
                <!-- Action Controls -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="text-xs text-slate-500 font-semibold">Report Generated on: <?php echo date('d M Y'); ?></div>
                    <div class="flex items-center gap-2">
                        <a href="<?php echo htmlspecialchars(BASE_PATH . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-arrow-left"></i> Back to Reports
                        </a>
                        <button onclick="window.print()" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button onclick="downloadCSV()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </button>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center space-x-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php else: ?>

                    <!-- Filters Card -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-8">
                        <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Report Filters</h3>
                        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 items-end" id="filterForm" action="<?php echo htmlspecialchars(BASE_PATH . '/reports/maintenance_report', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Job ID</label>
                                <input type="text" name="job_id" value="<?php echo htmlspecialchars($filters['job_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Customer</label>
                                <select name="customer_name" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                                    <option value="">All</option>
                                    <?php foreach ($customer_refs as $ref): ?>
                                        <option value="<?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php echo $filters['customer_reference'] === $ref ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Year</label>
                                <select name="year" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                                    <option value="">Any Year</option>
                                    <?php $currentYear = (int)date('Y'); for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                                    <option value="<?php echo $y; ?>" <?php echo (isset($_GET['year']) && (int)$_GET['year'] === $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1.5">
                                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Month</label>
                                <select name="month" class="p-2.5 border border-slate-200 rounded-lg text-xs transition-all focus:border-emerald-500 focus:outline-none bg-white">
                                    <option value="">Any Month</option>
                                    <?php for ($m = 1; $m <= 12; $m++): $mn = date('F', mktime(0,0,0,$m,10)); ?>
                                    <option value="<?php echo $m; ?>" <?php echo (isset($_GET['month']) && (int)$_GET['month'] === $m) ? 'selected' : ''; ?>><?php echo $mn; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]">
                                <i class="fas fa-filter"></i> Apply Filter
                            </button>
                        </form>
                    </div>

                    <!-- Filtered Summary Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total System Jobs</h4>
                            <p class="text-xl font-extrabold text-slate-900"><?php echo htmlspecialchars(count($jobs), ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Completed Cycles</h4>
                            <p class="text-xl font-extrabold text-emerald-600"><?php echo htmlspecialchars($completed_jobs_count, ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Scheduled Cycles</h4>
                            <p class="text-xl font-extrabold text-blue-600"><?php echo htmlspecialchars($scheduled_maintenance_count, ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Overdue Cycles (Action Required)</h4>
                            <p class="text-xl font-extrabold text-amber-600"><?php echo htmlspecialchars($due_maintenance_count, ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </div>

                    <!-- Charts Container -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Maintenance Status Overview</h3>
                            <div class="h-[250px] flex items-center justify-center">
                                <canvas id="maintenanceStatusChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                            <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Status Distribution Percentage</h3>
                            <div class="h-[250px] flex items-center justify-center">
                                <canvas id="jobStatusChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Maintenance Schedule Table -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-8 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 bg-slate-50">
                            <h3 class="text-sm font-bold text-slate-900">Detailed Maintenance Schedule</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse text-xs text-left">
                                <thead>
                                    <tr class="bg-slate-100 border-b border-slate-200 text-slate-700">
                                        <th class="p-4 font-bold">Job Details</th>
                                        <th class="p-4 font-bold">Installation Date</th>
                                        <th class="p-4 font-bold text-center">Cycle 1 (6M)</th>
                                        <th class="p-4 font-bold text-center">Cycle 2 (12M)</th>
                                        <th class="p-4 font-bold text-center">Cycle 3 (18M)</th>
                                        <th class="p-4 font-bold text-center">Cycle 4 (24M)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($jobs as $row): ?>
                                    <?php
                                    $jobDetails = "<ul class='list-disc pl-5 space-y-1 text-slate-500'>";
                                    $jobDetails .= "<li><strong>Job ID:</strong> " . htmlspecialchars($row['job_id'] ?? '', ENT_QUOTES, 'UTF-8') . "</li>";
                                    $jobDetails .= "<li><strong>Location:</strong> " . htmlspecialchars($row['location'] ?? '', ENT_QUOTES, 'UTF-8') . "</li>";
                                    $jobDetails .= "<li><strong>Company Ref:</strong> " . htmlspecialchars($row['company_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</li>";
                                    $jobDetails .= "<li><strong>Engineer:</strong> " . htmlspecialchars($row['engineer'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</li>";
                                    $jobDetails .= "</ul>";

                                    $installation_date = $row['date_completed'] ?? null;
                                    $maintenance_cycles = $maintenance_by_job[$row['job_id']] ?? [];
                                    $today = new DateTime();
                                    if (empty($maintenance_cycles)) {
                                        if ($installation_date && $installation_date !== '0000-00-00') {
                                            $install_date = new DateTime($installation_date);
                                            for ($i = 1; $i <= 4; $i++) {
                                                $cycle_date = clone $install_date;
                                                $cycle_date->add(new DateInterval('P' . (6 * $i) . 'M'));
                                                $status = $cycle_date > $today ? 'scheduled' : 'overdue';
                                                $maintenance_cycles[] = ['cycle_number' => $i, 'scheduled_date' => $cycle_date->format('Y-m-d'), 'actual_date' => null, 'status' => $status, 'description' => ''];
                                            }
                                        } else {
                                            for ($i = 1; $i <= 4; $i++) {
                                                $maintenance_cycles[] = ['cycle_number' => $i, 'scheduled_date' => 'N/A', 'actual_date' => null, 'status' => 'not set', 'description' => ''];
                                            }
                                        }
                                    } else {
                                        foreach ($maintenance_cycles as &$ms) {
                                            $scheduledDateStr = $ms['scheduled_date'] ?? null;
                                            if ($scheduledDateStr && $scheduledDateStr !== '0000-00-00') {
                                                try {
                                                    $scheduledDate = new DateTime($scheduledDateStr);
                                                    if ($ms['status'] === 'scheduled' && $scheduledDate < $today) {
                                                        $ms['status'] = 'overdue';
                                                    }
                                                } catch (Exception $e) {}
                                            }
                                        }
                                        unset($ms);
                                    }
                                    ?>
                                    <tr class="hover:bg-slate-50/55 transition-colors">
                                        <td class="p-4 align-top">
                                            <div class="collapsible cursor-pointer flex items-center gap-2 font-medium select-none text-slate-900">
                                                <span><?php echo htmlspecialchars($row['customer_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                                                <i class="fas fa-chevron-down text-[9px] text-slate-400 transition-transform duration-300"></i>
                                            </div>
                                            <div class="details hidden p-4 bg-slate-50/50 border border-slate-200/60 rounded-xl mt-3"><?php echo $jobDetails; ?></div>
                                        </td>
                                        <td class="p-4 align-top text-slate-650">
                                            <?php echo $installation_date && $installation_date !== '0000-00-00' ? htmlspecialchars($installation_date, ENT_QUOTES, 'UTF-8') : '<span class="text-slate-350 italic">Pending</span>'; ?>
                                        </td>
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                        <?php
                                        $cycle = array_reduce($maintenance_cycles, function($carry, $item) use ($i) {
                                            return ($item['cycle_number'] == $i) ? $item : $carry;
                                        }, null) ?? ['scheduled_date' => 'N/A', 'actual_date' => null, 'status' => 'not set', 'description' => ''];
                                        
                                        $status = $cycle['status'];
                                        $date = $cycle['scheduled_date'];
                                        $actual = $cycle['actual_date'] ? '<div class="text-[10px] text-emerald-600 mt-1"><i class="ri-check-double-line"></i> Completed: ' . $cycle['actual_date'] . '</div>' : '';
                                        $desc = $cycle['description'] ? '<div class="text-[10px] text-slate-400 mt-1 italic truncate max-w-[120px]" title="'.htmlspecialchars($cycle['description']).'">' . htmlspecialchars($cycle['description']) . '</div>' : '';
                                        
                                        // Status badge classes
                                        $badgeClass = "inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase mb-2 ";
                                        if($status === 'scheduled') $badgeClass .= "bg-blue-50 border border-blue-100 text-blue-600";
                                        elseif($status === 'completed') $badgeClass .= "bg-emerald-50 border border-emerald-100 text-emerald-600";
                                        elseif($status === 'overdue') $badgeClass .= "bg-amber-50 border border-amber-100 text-amber-600";
                                        elseif($status === 'cancelled') $badgeClass .= "bg-rose-50 border border-rose-100 text-rose-500 line-through decoration-rose-400";
                                        else $badgeClass .= "bg-slate-55 border border-slate-100 text-slate-400";
                                        ?>
                                        <td class="p-4 align-top text-center border-l border-slate-100">
                                            <div class="flex flex-col items-center h-full justify-between gap-3">
                                                <div>
                                                    <span class="<?php echo $badgeClass; ?>"><?php echo ucfirst($status); ?></span>
                                                    <div class="font-bold text-slate-800"><?php echo $date; ?></div>
                                                    <?php echo $actual . $desc; ?>
                                                </div>
                                                
                                                <div class="flex gap-1 <?php echo ($status === 'completed' || $status === 'cancelled') ? 'hidden' : 'opacity-100'; ?> bg-white shadow-sm p-1 rounded-full border border-slate-200">
                                                    <button class="w-6.5 h-6.5 rounded-full flex items-center justify-center bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors btn-status" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $i; ?>" data-date="<?php echo $date; ?>" data-status="scheduled" title="Reschedule"><i class="ri-calendar-line text-[11px] pointer-events-none"></i></button>
                                                    
                                                    <button class="w-6.5 h-6.5 rounded-full flex items-center justify-center bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-colors btn-status" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $i; ?>" data-date="<?php echo $date; ?>" data-status="completed" title="Complete"><i class="ri-check-line text-[11px] pointer-events-none"></i></button>
                                                    
                                                    <button class="w-6.5 h-6.5 rounded-full flex items-center justify-center bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition-colors btn-status" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $i; ?>" data-date="<?php echo $date; ?>" data-status="overdue" title="Mark Overdue"><i class="ri-alert-line text-[11px] pointer-events-none"></i></button>
                                                    
                                                    <button class="w-6.5 h-6.5 rounded-full flex items-center justify-center bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors btn-status" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $i; ?>" data-date="<?php echo $date; ?>" data-status="cancelled" title="Cancel"><i class="ri-close-line text-[11px] pointer-events-none"></i></button>
                                                </div>
                                            </div>
                                        </td>
                                        <?php endfor; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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

        document.addEventListener('DOMContentLoaded', () => {
            // Row-level collapsible
            document.querySelectorAll('.collapsible').forEach(item => {
                item.addEventListener('click', () => {
                    const details = item.nextElementSibling;
                    const icon = item.querySelector('.fa-chevron-down, .fa-chevron-up');
                    details.classList.toggle('hidden');
                    if (icon) {
                        if (details.classList.contains('hidden')) {
                            icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                        } else {
                            icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                        }
                    }
                });
            });
            
            // Maintenance Status Chart
            const maintenanceCtx = document.getElementById('maintenanceStatusChart').getContext('2d');
            new Chart(maintenanceCtx, {
                type: 'bar',
                data: {
                    labels: ['Completed', 'Scheduled', 'Action Required'],
                    datasets: [{
                        label: 'Maintenance Status',
                        data: [
                            <?php echo $completed_jobs_count; ?>,
                            <?php echo $scheduled_maintenance_count; ?>,
                            <?php echo $due_maintenance_count; ?>
                        ],
                        backgroundColor: [
                            '#059669', 
                            '#3b82f6', 
                            '#f59e0b'
                        ],
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, grid: { borderDash: [4, 4] } } },
                    plugins: { 
                        legend: { display: false }
                    }
                }
            });
            
            // Job Status Chart
            const jobStatusCtx = document.getElementById('jobStatusChart').getContext('2d');
            new Chart(jobStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Scheduled', 'Action Required'],
                    datasets: [{
                        label: 'Job Status',
                        data: [
                            <?php echo $completed_jobs_count; ?>,
                            <?php echo $scheduled_maintenance_count; ?>,
                            <?php echo $due_maintenance_count; ?>
                        ],
                        backgroundColor: [
                            '#059669', 
                            '#3b82f6', 
                            '#f59e0b'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
                    }
                }
            });
            
            // Status update functionality
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-status') || e.target.closest('.btn-status')) {
                    const btn = e.target.classList.contains('btn-status') ? e.target : e.target.closest('.btn-status');
                    const jobId = btn.getAttribute('data-job');
                    const cycle = btn.getAttribute('data-cycle');
                    const date = btn.getAttribute('data-date');
                    const status = btn.getAttribute('data-status');
                    const desc = prompt('Add an optional note (e.g., "Battery Replaced"):', '');
                    
                    const form = new FormData();
                    form.append('action', 'set_maintenance_status');
                    form.append('job_id', jobId);
                    form.append('cycle_number', cycle);
                    form.append('scheduled_date', date);
                    form.append('status', status);
                    form.append('description', desc || '');
 
                    fetch('<?php echo BASE_PATH; ?>/admin/manageTable/maintenance_schedule', {
                        method: 'POST',
                        body: form
                    }).then(r => r.json()).then(json => {
                        if (json.success) {
                            btn.innerHTML = '<i class="ri-loader-4-line animate-spin text-[11px]"></i>';
                            setTimeout(() => location.reload(), 500);
                        } else {
                            alert('Error: ' + (json.error || 'Unknown'));
                        }
                    }).catch(err => {
                        console.error(err);
                        alert('Request failed');
                    });
                }
            });
        });
        
        function downloadCSV() {
            const form = document.getElementById('filterForm');
            const url = new URL(form.action);
            url.search = new URLSearchParams(new FormData(form)).toString();
            url.searchParams.set('download_csv', '1');
            window.location.href = url.toString();
        }
    </script>
</body>
</html>