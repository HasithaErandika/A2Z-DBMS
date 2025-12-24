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
    <div class="container mx-auto p-6 min-h-screen bg-[url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120"%3E%3Cg fill="%231E3A8A" fill-opacity="0.03"%3E%3Cpath d="M60 20 L80 60 L60 100 L40 60 Z"/%3E%3C/g%3E%3C/svg%3E')] bg-[length:240px]">
        <!-- Header -->
        <div class="header bg-gradient-to-br from-blue-900 to-blue-500 p-6 rounded-xl shadow-lg mb-10 text-white relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between z-10 relative">
                <div>
                    <h1 class="text-3xl font-bold z-10">A2Z Maintenance Report</h1>
                    <p class="text-blue-100 mt-2">Track maintenance schedules and job details</p>
                </div>
                <div class="header-controls flex gap-3 z-10 mt-4 md:mt-0">
                    <button class="btn bg-white text-blue-900 px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:bg-blue-50 hover:translate-y-[-2px] hover:shadow-xl" onclick="window.print()"><i class="ri-printer-line"></i> Print</button>
                    <button class="btn bg-white text-blue-900 px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:bg-blue-50 hover:translate-y-[-2px] hover:shadow-xl" onclick="window.location.href='<?php echo htmlspecialchars(FULL_BASE_URL . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>'"><i class="ri-arrow-left-line"></i> Go Back</button>
                    <button class="btn bg-white text-blue-900 px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:bg-blue-50 hover:translate-y-[-2px] hover:shadow-xl" onclick="downloadCSV()"><i class="ri-download-line"></i> Download CSV</button>
                </div>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message bg-red-500 text-white p-4 rounded-xl mb-5 text-center"><?php echo htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
        <?php else: ?>

            <div class="filter-card bg-white p-6 rounded-xl shadow-lg mb-10 transition-all duration-300 hover:shadow-2xl">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Report Filters</h2>
                <form method="GET" class="filter-form grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-4 items-end" id="filterForm" action="<?php echo htmlspecialchars(FULL_BASE_URL . '/reports/maintenance_report', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Job ID</label>
                        <input type="text" name="job_id" value="<?php echo htmlspecialchars($filters['job_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none focus:shadow-[0_0_0_3px_rgba(30,58,138,0.1)]">
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Customer (search)</label>
                        <input list="customerList" name="customer_name" value="<?php echo htmlspecialchars($filters['customer_reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none">
                        <datalist id="customerList">
                            <?php foreach ($customer_refs as $ref): ?>
                                <option value="<?php echo htmlspecialchars($ref ?? '', ENT_QUOTES, 'UTF-8'); ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Year</label>
                        <select name="year" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none">
                            <option value="">All</option>
                            <?php $currentYear = (int)date('Y'); for ($y = $currentYear; $y >= $currentYear - 5; $y--): ?>
                                <option value="<?php echo $y; ?>" <?php echo (isset($_GET['year']) && (int)$_GET['year'] === $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="filter-item flex flex-col gap-2">
                        <label class="text-sm font-medium text-gray-500">Month</label>
                        <select name="month" class="p-2.5 border border-gray-200 rounded-lg text-base transition-all duration-300 focus:border-blue-900 focus:outline-none">
                            <option value="">All</option>
                            <?php for ($m = 1; $m <= 12; $m++): $mn = strftime('%B', mktime(0,0,0,$m,10)); ?>
                                <option value="<?php echo $m; ?>" <?php echo (isset($_GET['month']) && (int)$_GET['month'] === $m) ? 'selected' : ''; ?>><?php echo $mn; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn bg-gradient-to-br from-blue-900 to-blue-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-blue-900/30"><i class="ri-search-line"></i> Filter</button>
                    <button type="button" class="btn bg-gradient-to-br from-amber-600 to-amber-500 text-white px-5 py-2.5 rounded-lg font-medium cursor-pointer transition-all duration-300 flex items-center gap-2 text-sm hover:translate-y-[-2px] hover:shadow-xl hover:shadow-amber-900/30" onclick="location.href='<?php echo htmlspecialchars(FULL_BASE_URL . '/reports/maintenance_report', ENT_QUOTES, 'UTF-8'); ?>'"><i class="ri-refresh-line"></i> Reset</button>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="summary-card bg-white p-6 rounded-xl shadow-lg mb-10 transition-all duration-300 hover:shadow-2xl">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Maintenance Summary</h2>
                <div class="summary-grid grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-6">
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-blue-500">
                        <h3 class="text-base text-gray-500 mb-2">Total Jobs</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars(count($jobs), ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-green-500">
                        <h3 class="text-base text-gray-500 mb-2">Completed Jobs</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($completed_jobs_count, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-yellow-500">
                        <h3 class="text-base text-gray-500 mb-2">Scheduled Maintenance</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($scheduled_maintenance_count, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="summary-item bg-white p-4 rounded-xl shadow-md text-center transition-all duration-300 hover:translate-y-[-4px] hover:shadow-lg border-l-4 border-red-500">
                        <h3 class="text-base text-gray-500 mb-2">Due Maintenance</h3>
                        <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($due_maintenance_count, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Jobs Table -->
            <div class="jobs-table bg-white p-6 rounded-xl shadow-lg transition-all duration-300 hover:shadow-2xl">
                <h2 class="text-xl font-semibold bg-gradient-to-br from-blue-900 to-blue-500 bg-clip-text text-transparent mb-5">Job Details with Maintenance Cycles</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full table-auto border-collapse">
                        <thead>
                            <tr>
                                <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Job Details</th>
                                <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Installation Date</th>
                                <th class="p-3 bg-gradient-to-br from-blue-900 to-blue-500 text-white font-semibold sticky top-0 z-10">Maintenance Cycles</th>
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
                                $jobDetails .= "<li>Customer: " . htmlspecialchars($row['customer_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8') . "</li>";
                                $jobDetails .= "</ul>";
                                
                                // Build maintenance cycles: prefer persisted `maintenance_schedule` entries when available
                                $maintenance_cycles = [];
                                $msEntries = $maintenance_by_job[$row['job_id']] ?? [];
                                $today = new DateTime();
                                if (!empty($msEntries)) {
                                    foreach ($msEntries as $ms) {
                                        $scheduled = $ms['scheduled_date'] ?? null;
                                        $actual = $ms['actual_date'] ?? null;
                                        $status = $ms['status'] ?? null;

                                        // If stored status is 'scheduled' but the scheduled_date is past, mark as 'overdue' for display
                                        if ($status === 'scheduled' && $scheduled) {
                                            try {
                                                $sd = new DateTime($scheduled);
                                                if ($sd < $today) {
                                                    $status = 'overdue';
                                                }
                                            } catch (Exception $e) {
                                                // ignore parse errors
                                            }
                                        }

                                        if (!$status) {
                                            if ($scheduled) {
                                                try {
                                                    $sd = new DateTime($scheduled);
                                                    $status = $sd > $today ? 'scheduled' : 'overdue';
                                                } catch (Exception $e) {
                                                    $status = 'scheduled';
                                                }
                                            } else {
                                                $status = 'scheduled';
                                            }
                                        }

                                        $maintenance_cycles[] = [
                                            'cycle' => (int)($ms['cycle_number'] ?? 0),
                                            'date' => $scheduled ?? ($actual ?? 'N/A'),
                                            'actual_date' => $actual ?? null,
                                            'status' => $status
                                        ];
                                    }
                                    // If fewer than 4 cycles are present, attempt to compute remaining from date_completed
                                    if (count($maintenance_cycles) < 4 && !empty($row['date_completed']) && $row['date_completed'] !== '0000-00-00') {
                                        $install_date = new DateTime($row['date_completed']);
                                        $existingCycles = array_column($maintenance_cycles, 'cycle');
                                        for ($i = 1; $i <= 4; $i++) {
                                            if (in_array($i, $existingCycles)) continue;
                                            $cd = clone $install_date;
                                            $cd->add(new DateInterval('P' . (6 * $i) . 'M'));
                                            $maintenance_cycles[] = ['cycle' => $i, 'date' => $cd->format('Y-m-d'), 'status' => $cd > $today ? 'scheduled' : 'overdue'];
                                        }
                                        usort($maintenance_cycles, function($a,$b){return $a['cycle'] <=> $b['cycle'];});
                                    }
                                } else {
                                    // Fallback: compute 4 cycles of 6 months each from date_completed
                                    $installation_date = $row['date_completed'];
                                            if ($installation_date && $installation_date !== '0000-00-00') {
                                        $install_date = new DateTime($installation_date);
                                        for ($i = 1; $i <= 4; $i++) {
                                            $cycle_date = clone $install_date;
                                            $cycle_date->add(new DateInterval('P' . (6 * $i) . 'M'));
                                                    $maintenance_cycles[] = [
                                                        'cycle' => $i,
                                                        'date' => $cycle_date->format('Y-m-d'),
                                                        'status' => $cycle_date > $today ? 'scheduled' : 'overdue'
                                                    ];
                                        }
                                    }
                                }
                                
                                $status_badge = '';
                                switch ($row['completion_status']) {
                                    case 'Completed':
                                        $status_badge = '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Completed</span>';
                                        break;
                                    case 'Ongoing':
                                        $status_badge = '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Ongoing</span>';
                                        break;
                                    case 'Started':
                                        $status_badge = '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Started</span>';
                                        break;
                                    case 'Not Started':
                                        $status_badge = '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Not Started</span>';
                                        break;
                                    case 'Cancelled':
                                        $status_badge = '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Cancelled</span>';
                                        break;
                                    default:
                                        $status_badge = '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Unknown</span>';
                                }
                                
                                $maintenance_details = '<ul class="list-disc pl-5">';
                                foreach ($maintenance_cycles as $cycle) {
                                    $status_class = $cycle['status'] === 'overdue' ? 'text-red-600' : 'text-gray-600';
                                    
                                    $maintenance_details .= '<li class="' . $status_class . '">';
                                    $maintenance_details .= 'Cycle ' . $cycle['cycle'] . ': ' . $cycle['date'] . ' (' . ucfirst($cycle['status']) . ')';
                                    if (!empty($cycle['actual_date'])) {
                                        $maintenance_details .= ' - Completed: ' . $cycle['actual_date'];
                                    }
                                    $maintenance_details .= '</li>';
                                }
                                $maintenance_details .= '</ul>';
                                ?>
                                <tr>
                                    <td class="p-3 border-b border-gray-200 align-top">
                                        <div class="collapsible cursor-pointer flex items-center gap-2 font-medium">
                                            <span class="total"><?php echo htmlspecialchars($row['customer_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                                            <i class="ri-arrow-down-s-line icon transition-transform duration-300"></i>
                                        </div>
                                        <div class="details hidden p-3 bg-blue-50/50 rounded-lg mt-2"><?php echo $jobDetails; ?></div>
                                    </td>
                                    <td class="p-3 border-b border-gray-200 align-top"><?php echo $installation_date !== '0000-00-00' ? htmlspecialchars($installation_date, ENT_QUOTES, 'UTF-8') : 'Not Set'; ?></td>
                                    <td class="p-3 border-b border-gray-200 align-top">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <h4 class="font-semibold mb-2">Cycles 1 & 2</h4>
                                                <?php for ($ci = 0; $ci < 2; $ci++):
                                                    $c = $maintenance_cycles[$ci] ?? null;
                                                    if (!$c) continue;
                                                ?>
                                                    <div class="mb-2">
                                                        <div class="flex items-center justify-between">
                                                            <div><?php echo 'Cycle ' . htmlspecialchars($c['cycle']); ?> — <?php echo htmlspecialchars($c['date']); ?><?php if (!empty($c['actual_date'])) { echo ' <span class="ml-2 text-sm text-gray-500">(Completed: ' . htmlspecialchars($c['actual_date']) . ')</span>'; } ?></div>
                                                            <div class="flex items-center gap-2" role="group" aria-label="Cycle actions">
                                                                <?php $curStatus = $c['status'] ?? ''; ?>
                                                                <button class="<?php echo ($curStatus==='scheduled') ? 'btn-status px-2 py-1 bg-blue-100 rounded ring-2 ring-blue-500' : 'btn-status px-2 py-1 bg-blue-50 rounded'; ?>" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $c['cycle']; ?>" data-date="<?php echo $c['date']; ?>" data-status="scheduled" title="Mark as Scheduled"><i class="ri-calendar-line"></i></button>
                                                                <button class="<?php echo ($curStatus==='completed') ? 'btn-status px-2 py-1 bg-green-100 rounded ring-2 ring-green-500' : 'btn-status px-2 py-1 bg-green-50 rounded'; ?>" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $c['cycle']; ?>" data-date="<?php echo $c['date']; ?>" data-status="completed" title="Mark as Completed"><i class="ri-check-line"></i></button>
                                                                <button class="<?php echo ($curStatus==='overdue') ? 'btn-status px-2 py-1 bg-yellow-100 rounded ring-2 ring-yellow-500' : 'btn-status px-2 py-1 bg-yellow-50 rounded'; ?>" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $c['cycle']; ?>" data-date="<?php echo $c['date']; ?>" data-status="overdue" title="Mark as Overdue"><i class="ri-alert-line"></i></button>
                                                                <button class="<?php echo ($curStatus==='cancelled') ? 'btn-status px-2 py-1 bg-red-100 rounded ring-2 ring-red-500' : 'btn-status px-2 py-1 bg-red-50 rounded'; ?>" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $c['cycle']; ?>" data-date="<?php echo $c['date']; ?>" data-status="cancelled" title="Mark as Cancelled"><i class="ri-close-line"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endfor; ?>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold mb-2">Cycles 3 & 4</h4>
                                                <?php for ($ci = 2; $ci < 4; $ci++):
                                                    $c = $maintenance_cycles[$ci] ?? null;
                                                    if (!$c) continue;
                                                ?>
                                                    <div class="mb-2">
                                                        <div class="flex items-center justify-between">
                                                            <div><?php echo 'Cycle ' . htmlspecialchars($c['cycle']); ?> — <?php echo htmlspecialchars($c['date']); ?><?php if (!empty($c['actual_date'])) { echo ' <span class="ml-2 text-sm text-gray-500">(Completed: ' . htmlspecialchars($c['actual_date']) . ')</span>'; } ?></div>
                                                            <div class="flex items-center gap-2" role="group" aria-label="Cycle actions">
                                                                <?php $curStatus = $c['status'] ?? ''; ?>
                                                                <button class="<?php echo ($curStatus==='scheduled') ? 'btn-status px-2 py-1 bg-blue-100 rounded ring-2 ring-blue-500' : 'btn-status px-2 py-1 bg-blue-50 rounded'; ?>" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $c['cycle']; ?>" data-date="<?php echo $c['date']; ?>" data-status="scheduled" title="Mark as Scheduled"><i class="ri-calendar-line"></i></button>
                                                                <button class="<?php echo ($curStatus==='completed') ? 'btn-status px-2 py-1 bg-green-100 rounded ring-2 ring-green-500' : 'btn-status px-2 py-1 bg-green-50 rounded'; ?>" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $c['cycle']; ?>" data-date="<?php echo $c['date']; ?>" data-status="completed" title="Mark as Completed"><i class="ri-check-line"></i></button>
                                                                <button class="<?php echo ($curStatus==='overdue') ? 'btn-status px-2 py-1 bg-yellow-100 rounded ring-2 ring-yellow-500' : 'btn-status px-2 py-1 bg-yellow-50 rounded'; ?>" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $c['cycle']; ?>" data-date="<?php echo $c['date']; ?>" data-status="overdue" title="Mark as Overdue"><i class="ri-alert-line"></i></button>
                                                                <button class="<?php echo ($curStatus==='cancelled') ? 'btn-status px-2 py-1 bg-red-100 rounded ring-2 ring-red-500' : 'btn-status px-2 py-1 bg-red-50 rounded'; ?>" data-job="<?php echo $row['job_id']; ?>" data-cycle="<?php echo $c['cycle']; ?>" data-date="<?php echo $c['date']; ?>" data-status="cancelled" title="Mark as Cancelled"><i class="ri-close-line"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
            
            // Download CSV function
            window.downloadCSV = function() {
                // Create a temporary form to submit the data for CSV download
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = '<?php echo htmlspecialchars(FULL_BASE_URL . '/reports/maintenance_report?download_csv=1', ENT_QUOTES, 'UTF-8'); ?>';
                
                // Add current filters as hidden inputs
                <?php foreach ($_GET as $key => $value): ?>
                    if ('<?php echo $key; ?>' !== 'download_csv') {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = '<?php echo $key; ?>';
                        input.value = '<?php echo addslashes($value); ?>';
                        form.appendChild(input);
                    }
                <?php endforeach; ?>
                
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            };
        });

        // Handle maintenance status buttons
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('btn-status')) {
                var btn = e.target;
                var jobId = btn.getAttribute('data-job');
                var cycle = btn.getAttribute('data-cycle');
                var date = btn.getAttribute('data-date');
                var status = btn.getAttribute('data-status');
                var desc = prompt('Optional description for this change:', '');
                var form = new FormData();
                form.append('action', 'set_maintenance_status');
                form.append('job_id', jobId);
                form.append('cycle_number', cycle);
                form.append('scheduled_date', date);
                form.append('status', status);
                form.append('description', desc || '');

                fetch('<?php echo BASE_PATH; ?>/admin/manageTable/maintenance_schedule', {
                    method: 'POST',
                    body: form
                }).then(r => r.json()).then(function(json) {
                    if (json.success) {
                        alert('Saved: ' + (json.action || 'ok'));
                        location.reload();
                    } else {
                        alert('Error: ' + (json.error || 'Unknown'));
                    }
                }).catch(function(err){
                    console.error(err);
                    alert('Request failed');
                });
            }
        });
    </script>
</body>
</html>