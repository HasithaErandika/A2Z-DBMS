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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-poppins bg-gray-50 text-gray-900 leading-relaxed overflow-x-hidden">
    <div class="container mx-auto p-6 min-h-screen bg-[url('data:image/svg+xml,%3Csvg xmlns=\"http://www.w3.org/2000/svg\" width=\"120\" height=\"120\" viewBox=\"0 0 120 120\"%3E%3Cg fill=\"%231E3A8A\" fill-opacity=\"0.03\"%3E%3Cpath d=\"M60 20 L80 60 L60 100 L40 60 Z\"/%3E%3C/g%3E%3C/svg%3E')] bg-[length:240px]">
        <div class="header bg-gradient-to-br from-blue-900 to-blue-500 p-8 rounded-xl shadow-2xl flex items-center justify-between mb-10 text-white relative overflow-hidden hover:after:opacity-20 after:content-[''] after:absolute after:top-[-50%] after:left-[-50%] after:w-[200%] after:h-[200%] after:bg-white/10 after:rotate-30 after:transition-all after:duration-500 after:opacity-0 after:z-0">
            <h1 class="text-3xl font-semibold z-10">Maintenance Report</h1>
            <div class="header-controls flex gap-3 z-10">
                <button class="btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30" onclick="window.print()"><i class="ri-printer-line"></i> Print</button>
                <button class="btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30" onclick="window.location.href='<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>'"><i class="ri-arrow-left-line"></i> Go Back</button>
                <button class="btn bg-transparent text-blue-900 border-2 border-blue-900 px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:bg-blue-900 hover:text-white hover:translate-y-[-2px]" onclick="downloadCSV()"><i class="ri-download-line"></i> Download CSV</button>
            </div>
        </div>
        <?php if (isset($error)): ?>
            <div class="error-message bg-red-500 text-white p-4 rounded-xl mb-5 text-center"><?php echo htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>
            <div class="filter-card bg-white p-6 rounded-xl shadow-lg mb-10 transition-all duration-300 hover:shadow-2xl">
                <form method="GET" class="filter-form grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-4 items-end" id="filterForm" action="<?php echo htmlspecialchars(FULL_BASE_URL . '/reports/maintenance_report', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Job ID</label>
                        <input type="text" name="job_id" value="<?php echo htmlspecialchars($filters['job_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Customer</label>
                        <select name="customer_name" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                            <option value="">All</option>
                            <?php foreach ($customer_refs as $ref): ?>
                                <option value="<?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php echo $filters['customer_reference'] === $ref ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Year</label>
                        <select name="year" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                            <option value="">Any Year</option>
                            <?php $currentYear = (int)date('Y'); for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php echo (isset($_GET['year']) && (int)$_GET['year'] === $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Month</label>
                        <select name="month" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                            <option value="">Any Month</option>
                            <?php for ($m = 1; $m <= 12; $m++): $mn = strftime('%B', mktime(0,0,0,$m,10)); ?>
                            <option value="<?php echo $m; ?>" <?php echo (isset($_GET['month']) && (int)$_GET['month'] === $m) ? 'selected' : ''; ?>><?php echo $mn; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30"><i class="ri-filter-line"></i> Filter</button>
                </form>
            </div>
            <div class="summary-card bg-white p-6 rounded-xl shadow-lg mb-10 transition-all duration-300 hover:shadow-2xl">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Filtered Summary</h2>
                <div class="summary-grid grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-6">
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-amber-500">
                        <h3 class="text-base text-gray-500 mb-2">Total Jobs</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars(count($jobs), ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="summary-item paid bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-green-500">
                        <h3 class="text-base text-gray-500 mb-2">Completed</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($completed_jobs_count, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="summary-item unpaid bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-red-500">
                        <h3 class="text-base text-gray-500 mb-2">Scheduled</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($scheduled_maintenance_count, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-amber-500">
                        <h3 class="text-base text-gray-500 mb-2">Action Required</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($due_maintenance_count, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
            </div>
            <div class="charts-container flex flex-row gap-6 mb-10 flex-wrap">
                <div class="chart-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl flex-1 min-w-[300px]">
                    <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Maintenance Status Overview</h2>
                    <div class="chart-container max-w-full h-[300px]">
                        <canvas id="maintenanceStatusChart"></canvas>
                    </div>
                </div>
                <div class="chart-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl flex-1 min-w-[300px]">
                    <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Job Status Distribution</h2>
                    <div class="chart-container max-w-full h-[300px]">
                        <canvas id="jobStatusChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="table-card bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl overflow-x-auto">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Detailed Maintenance Schedule</h2>
                <table class="table w-full border-collapse text-base">
                    <thead>
                        <tr>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Job Details</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Installation</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Cycle 1 (6M)</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Cycle 2 (12M)</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Cycle 3 (18M)</th>
                            <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Cycle 4 (24M)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $row): ?>
                        <?php
                        $jobDetails = "<ul class='list-disc pl-5'>";
                        $jobDetails .= "<li>Job ID: " . htmlspecialchars($row['job_id'] ?? '', ENT_QUOTES, 'UTF-8') . "</li>";
                        $jobDetails .= "<li>Location: " . htmlspecialchars($row['location'] ?? '', ENT_QUOTES, 'UTF-8') . "</li>";
                        $jobDetails .= "<li>Company Ref: " . htmlspecialchars($row['company_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</li>";
                        $jobDetails .= "<li>Engineer: " . htmlspecialchars($row['engineer'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</li>";
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
                        <tr>
                            <td class="p-3 border-b border-gray-200 align-top">
                                <div class="collapsible cursor-pointer flex items-center gap-2 font-medium">
                                    <span class="total"><?php echo htmlspecialchars($row['customer_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <i class="ri-arrow-down-s-line icon transition-transform duration-300"></i>
                                </div>
                                <div class="details hidden p-3 bg-blue-50/50 rounded-lg mt-2"><?php echo $jobDetails; ?></div>
                            </td>
                            <td class="p-3 border-b border-gray-200 align-top">
                                <?php echo $installation_date && $installation_date !== '0000-00-00' ? htmlspecialchars($installation_date, ENT_QUOTES, 'UTF-8') : '<span class="text-slate-300 italic">Pending</span>'; ?>
                            </td>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                            <?php
                            $cycle = array_reduce($maintenance_cycles, function($carry, $item) use ($i) {
                                return ($item['cycle_number'] == $i) ? $item : $carry;
                            }, null) ?? ['scheduled_date' => 'N/A', 'actual_date' => null, 'status' => 'not set', 'description' => ''];
                            
                            $status = $cycle['status'];
                            $date = $cycle['scheduled_date'];
                            $actual = $cycle['actual_date'] ? '<div class="text-[10px] text-emerald-600 mt-1"><i class="ri-check-double-line"></i> ' . $cycle['actual_date'] . '</div>' : '';
                            $desc = $cycle['description'] ? '<div class="text-[10px] text-slate-400 mt-1 italic truncate max-w-[120px]" title="'.htmlspecialchars($cycle['description']).'">' . htmlspecialchars($cycle['description']) . '</div>' : '';
                            
                            // Status badge classes
                            $badgeClass = "inline-block px-3 py-1 rounded-full text-xs font-semibold mb-2 ";
                            if($status === 'scheduled') $badgeClass .= "bg-blue-100 text-blue-700";
                            elseif($status === 'completed') $badgeClass .= "bg-emerald-100 text-emerald-700";
                            elseif($status === 'overdue') $badgeClass .= "bg-amber-100 text-amber-700";
                            elseif($status === 'cancelled') $badgeClass .= "bg-red-50 text-red-500 line-through decoration-red-500";
                            else $badgeClass .= "bg-slate-100 text-slate-400";
                            ?>
                            <td class="p-3 border-b border-gray-200 align-top text-center">
                                <div class="flex flex-col items-center h-full justify-between">
                                    <div>
                                        <span class="<?php echo $badgeClass; ?>"><?php echo ucfirst($status); ?></span>
                                        <div class="font-medium text-slate-700"><?php echo $date; ?></div>
                                        <?php echo $actual . $desc; ?>
                                    </div>
                                    
                                    <div class="flex gap-1 mt-3 <?php echo ($status === 'completed' || $status === 'cancelled') ? 'hidden' : 'opacity-100'; ?> transition-opacity bg-white shadow-sm p-1 rounded-full border border-slate-100">
                                        <button class="w-8 h-8 rounded-full flex items-center justify-center bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors btn-status" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $i; ?>" data-date="<?php echo $date; ?>" data-status="scheduled" title="Reschedule"><i class="ri-calendar-line pointer-events-none"></i></button>
                                        
                                        <button class="w-8 h-8 rounded-full flex items-center justify-center bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-colors btn-status" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $i; ?>" data-date="<?php echo $date; ?>" data-status="completed" title="Complete"><i class="ri-check-line pointer-events-none"></i></button>
                                        
                                        <button class="w-8 h-8 rounded-full flex items-center justify-center bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition-colors btn-status" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $i; ?>" data-date="<?php echo $date; ?>" data-status="overdue" title="Mark Overdue"><i class="ri-alert-line pointer-events-none"></i></button>
                                        
                                        <button class="w-8 h-8 rounded-full flex items-center justify-center bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors btn-status" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $i; ?>" data-date="<?php echo $date; ?>" data-status="cancelled" title="Cancel"><i class="ri-close-line pointer-events-none"></i></button>
                                    </div>
                                </div>
                            </td>
                            <?php endfor; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Row-level collapsible
            document.querySelectorAll('.collapsible').forEach(item => {
                item.addEventListener('click', () => {
                    const details = item.nextElementSibling;
                    item.classList.toggle('active');
                    details.classList.toggle('hidden');
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
                            'rgba(16, 185, 129, 0.6)', 
                            'rgba(30, 144, 255, 0.6)', 
                            'rgba(255, 107, 0, 0.6)'
                        ],
                        borderColor: [
                            'rgba(16, 185, 129, 1)', 
                            'rgba(30, 144, 255, 1)', 
                            'rgba(255, 107, 0, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true } },
                    plugins: { 
                        legend: { display: false }, 
                        title: { display: true, text: 'Maintenance Status Distribution' } 
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
                            'rgba(16, 185, 129, 0.6)', 
                            'rgba(30, 144, 255, 0.6)', 
                            'rgba(255, 107, 0, 0.6)'
                        ],
                        borderColor: [
                            'rgba(16, 185, 129, 1)', 
                            'rgba(30, 144, 255, 1)', 
                            'rgba(255, 107, 0, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: { 
                        title: { display: true, text: 'Job Status Distribution' } 
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
                            // Quick visual feedback before reload
                            btn.innerHTML = '<i class="ri-loader-4-line animate-spin"></i>';
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