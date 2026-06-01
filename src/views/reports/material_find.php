<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/A2Z-DBMS');
}
if (!defined('FULL_BASE_URL')) {
    define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');
}

$error = $error ?? null;
$success = $success ?? null;
$username = $username ?? 'Admin';
$dbname = $dbname ?? '';
$job_id = $job_id ?? 0;
$jobs_list = $jobs_list ?? [];
$materials = $materials ?? [];
$selected_job = $selected_job ?? null;

// Calculate KPI values
$totalBaseCost = 0.0;
$totalProfitMarkup = 0.0;
$totalQuotedPrice = 0.0;
foreach ($materials as $item) {
    $totalBaseCost += floatval($item['total_cost'] ?? 0);
    $totalProfitMarkup += floatval($item['profit_amount'] ?? 0);
    $totalQuotedPrice += floatval($item['final_price'] ?? 0);
}
$avgMargin = $totalBaseCost > 0 ? ($totalProfitMarkup / $totalBaseCost) * 100 : 0.0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering - Material Cost Calculation</title>
    <?php require_once __DIR__ . '/../partials/theme.php'; ?>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .gradient-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased overflow-x-hidden min-h-screen">

    <?php 
    $activePage = 'reports';
    $headerTitle = 'Material Cost Calculation';
    $headerSubtitle = 'Track and calculate material costs across jobs and projects.';
    $breadcrumb = 'Reports / Material Cost';
    require_once __DIR__ . '/../partials/sidebar.php';
    ?>

    <!-- Main Container -->
    <div class="ml-64 transition-all duration-300 min-h-screen flex flex-col justify-between" id="container">
        <div>
            <?php require_once __DIR__ . '/../partials/header.php'; ?>

            <main class="p-8">
                <!-- Action bar -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Live Production Schema</div>
                    </div>
                    <a href="<?php echo htmlspecialchars(BASE_PATH . '/admin/reports', ENT_QUOTES, 'UTF-8'); ?>" class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                        <i class="ri-arrow-left-line"></i> Back to Reports
                    </a>
                </div>

                <!-- Error and Success Messages -->
                <?php if ($error): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center space-x-2 shadow-sm">
                        <i class="ri-error-warning-line text-lg"></i>
                        <span class="text-xs font-medium"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 flex items-center space-x-2 shadow-sm">
                        <i class="ri-checkbox-circle-line text-lg animate-bounce"></i>
                        <span class="text-xs font-semibold"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Selection & Quick Import section -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-6 mb-8">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                        <form method="GET" action="" class="flex flex-col md:flex-row gap-4 items-end flex-grow max-w-3xl">
                            <div class="flex flex-col gap-1.5 w-full md:w-96 relative search-combobox">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Select Project Job for Materials</label>
                                <input type="hidden" name="job_id" value="<?php echo $job_id > 0 ? $job_id : ''; ?>">
                                <div class="relative">
                                    <input type="text" 
                                           placeholder="Search A2Z Job (Customer, Location, ID)..." 
                                           value="<?php 
                                               if ($selected_job) {
                                                   echo htmlspecialchars('[' . ($selected_job['company_reference'] ?? 'N/A') . '] ' . ($selected_job['customer_reference'] ?? '') . ' - Job ID: ' . $selected_job['job_id'], ENT_QUOTES, 'UTF-8');
                                               }
                                           ?>"
                                           class="w-full p-3 pr-10 border border-slate-250 rounded-xl text-xs transition-all focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-white font-medium text-slate-700 cursor-pointer shadow-sm"
                                           autocomplete="off">
                                    <div class="absolute right-3 top-3 text-slate-400 pointer-events-none">
                                        <i class="ri-arrow-down-s-line text-base search-arrow-icon"></i>
                                    </div>
                                </div>
                                
                                <!-- Suggestions wrapper -->
                                <div class="jobs-suggestions-wrapper absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-64 overflow-y-auto hidden">
                                    <div class="p-2.5 border-b border-slate-100 bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">A2Z Engineering Jobs Only</div>
                                    <div class="divide-y divide-slate-150">
                                        <?php foreach ($jobs_list as $job): ?>
                                            <div class="job-item p-3 hover:bg-indigo-50/50 cursor-pointer text-xs transition-colors flex flex-col gap-0.5" 
                                                 data-id="<?php echo intval($job['job_id']); ?>" 
                                                 data-search="<?php echo strtolower(htmlspecialchars(($job['company_reference'] ?? '') . ' ' . ($job['customer_reference'] ?? '') . ' ' . $job['job_id'] . ' ' . ($job['location'] ?? ''), ENT_QUOTES, 'UTF-8')); ?>"
                                                 data-display="[<?php echo htmlspecialchars($job['company_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>] <?php echo htmlspecialchars($job['customer_reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?> - Job ID: <?php echo intval($job['job_id']); ?>">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-slate-800">[<?php echo htmlspecialchars($job['company_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>] <?php echo htmlspecialchars($job['customer_reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                    <span class="text-[9px] bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold px-2 py-0.5 rounded-full">ID: <?php echo intval($job['job_id']); ?></span>
                                                </div>
                                                <div class="text-[10px] text-slate-400 flex items-center gap-1">
                                                    <i class="ri-map-pin-line text-[11px] text-slate-355"></i> <?php echo htmlspecialchars($job['location'] ?? 'No Location', ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="no-jobs-found-msg p-4 text-center text-slate-400 text-xs hidden">
                                        <i class="ri-error-warning-line text-lg block mb-1"></i> No matching jobs found
                                    </div>
                                </div>
                            </div>
                        </form>

                        <?php if ($job_id > 0): ?>
                            <div class="flex items-center gap-3">
                                <form method="POST" action="<?php echo BASE_PATH; ?>/reports/material_find?job_id=<?php echo $job_id; ?>&action=prepopulate" onsubmit="return confirm('WARNING: Pre-populating this package will clear any existing material items for this job. Proceed?');">
                                    <input type="hidden" name="action" value="prepopulate">
                                    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                                    <button type="submit" class="bg-indigo-50 border border-indigo-150 hover:bg-indigo-100 text-indigo-705 px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                                        <i class="ri-magic-line"></i> Pre-populate 5kW Hybrid Package
                                    </button>
                                </form>
                                <form method="POST" action="<?php echo BASE_PATH; ?>/reports/material_find?job_id=<?php echo $job_id; ?>&action=clear" onsubmit="return confirm('Are you sure you want to clear all materials from this job? This cannot be undone.');">
                                    <input type="hidden" name="action" value="clear">
                                    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                                    <button type="submit" class="bg-red-50 border border-red-155 hover:bg-red-100 text-red-650 px-4 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                                        <i class="ri-delete-bin-4-line"></i> Clear All Items
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($job_id <= 0): ?>
                    <!-- Empty State View -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-16 text-center max-w-2xl mx-auto my-6">
                        <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-400">
                            <i class="ri-calculator-line text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Select a Job to Calculate Costs</h3>
                        <p class="text-xs text-slate-400 mb-4 leading-relaxed max-w-md mx-auto">
                            Choose an engineering site project or customer job below or from the dropdown above to manage materials, profit markups, and access the Excel/CSV bulk import feature.
                        </p>
                        
                        <div class="mb-8 max-w-sm mx-auto text-left relative search-combobox">
                            <form method="GET" action="">
                                <input type="hidden" name="job_id" value="">
                                <div class="relative">
                                    <input type="text" 
                                           placeholder="Search A2Z Job (Customer, Location, ID)..." 
                                           class="w-full p-3.5 pr-10 border border-indigo-200 rounded-xl text-xs transition-all focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none bg-indigo-50/10 font-medium text-slate-700 cursor-pointer shadow-sm"
                                           autocomplete="off">
                                    <div class="absolute right-3 top-3.5 text-slate-400 pointer-events-none">
                                        <i class="ri-arrow-down-s-line text-base search-arrow-icon"></i>
                                    </div>
                                </div>
                                
                                <!-- Suggestions wrapper -->
                                <div class="jobs-suggestions-wrapper absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-xl shadow-xl z-55 max-h-60 overflow-y-auto hidden">
                                    <div class="p-2.5 border-b border-slate-100 bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">A2Z Engineering Jobs Only</div>
                                    <div class="divide-y divide-slate-150">
                                        <?php foreach ($jobs_list as $job): ?>
                                            <div class="job-item p-3 hover:bg-indigo-50/50 cursor-pointer text-xs transition-colors flex flex-col gap-0.5" 
                                                 data-id="<?php echo intval($job['job_id']); ?>" 
                                                 data-search="<?php echo strtolower(htmlspecialchars(($job['company_reference'] ?? '') . ' ' . ($job['customer_reference'] ?? '') . ' ' . $job['job_id'] . ' ' . ($job['location'] ?? ''), ENT_QUOTES, 'UTF-8')); ?>"
                                                 data-display="[<?php echo htmlspecialchars($job['company_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>] <?php echo htmlspecialchars($job['customer_reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?> - Job ID: <?php echo intval($job['job_id']); ?>">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-slate-800">[<?php echo htmlspecialchars($job['company_reference'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>] <?php echo htmlspecialchars($job['customer_reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                    <span class="text-[9px] bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold px-2 py-0.5 rounded-full">ID: <?php echo intval($job['job_id']); ?></span>
                                                </div>
                                                <div class="text-[10px] text-slate-450 flex items-center gap-1">
                                                    <i class="ri-map-pin-line text-[11px] text-slate-355"></i> <?php echo htmlspecialchars($job['location'] ?? 'No Location', ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="no-jobs-found-msg p-4 text-center text-slate-400 text-xs hidden">
                                        <i class="ri-error-warning-line text-lg block mb-1"></i> No matching jobs found
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="bg-slate-50/50 border border-slate-150 rounded-xl p-6 text-left space-y-3 max-w-sm mx-auto">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Features included:</h4>
                            <ul class="text-[11px] text-slate-500 space-y-2">
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-emerald-500"></i> Add, edit, or delete customized material items
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-emerald-500"></i> Automatic calculation of Base Cost and Profit Markup
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-emerald-500"></i> Load templates (e.g. 5kW Hybrid Package)
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="ri-checkbox-circle-fill text-emerald-500"></i> Seamless integration with Overall Profit Calculations
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Job Dashboard view -->
                    
                    <!-- KPI Summary Row -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm relative overflow-hidden">
                            <div class="absolute right-4 top-4 text-slate-100"><i class="ri-coins-line text-4xl"></i></div>
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Base Cost (Company Expense)</h4>
                            <p class="text-xl font-extrabold text-slate-900">LKR <?= number_format($totalBaseCost, 2) ?></p>
                            <small class="text-[10px] text-slate-500">Cost paid directly by A2Z</small>
                        </div>
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm relative overflow-hidden">
                            <div class="absolute right-4 top-4 text-slate-100"><i class="ri-percent-line text-4xl"></i></div>
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Average Profit Margin</h4>
                            <p class="text-xl font-extrabold text-indigo-600"><?= number_format($avgMargin, 1) ?>%</p>
                            <small class="text-[10px] text-slate-500">Markup applied to materials</small>
                        </div>
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm relative overflow-hidden">
                            <div class="absolute right-4 top-4 text-slate-100"><i class="ri-pulse-line text-4xl"></i></div>
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Profit Markup Amount</h4>
                            <p class="text-xl font-extrabold text-emerald-600">LKR <?= number_format($totalProfitMarkup, 2) ?></p>
                            <small class="text-[10px] text-slate-500">Net profit generated from items</small>
                        </div>
                        <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm relative overflow-hidden">
                            <div class="absolute right-4 top-4 text-slate-100"><i class="ri-bill-line text-4xl"></i></div>
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Final Price (Client Quote)</h4>
                            <p class="text-xl font-extrabold text-slate-950">LKR <?= number_format($totalQuotedPrice, 2) ?></p>
                            <small class="text-[10px] text-slate-500">Final price listed in quotation</small>
                        </div>
                    </div>

                    <!-- Double Column Layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                        
                        <!-- Left Column: Forms & Import -->
                        <div class="lg:col-span-4 space-y-6">
                            
                            <!-- Manual Add Item Form -->
                            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                                <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Add Material Item</h3>
                                    <span class="badge bg-indigo-50 border border-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full">Automatic Math</span>
                                </div>
                                <form method="POST" action="<?php echo BASE_PATH; ?>/reports/material_find?job_id=<?php echo $job_id; ?>&action=add" class="p-6 space-y-4">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Material Name / Description</label>
                                        <input type="text" name="material_name" placeholder="e.g. Solis 5kW Hybrid Inverter" required
                                               class="p-3 border border-slate-200 rounded-lg text-xs transition-all focus:border-indigo-500 focus:outline-none">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Quantity</label>
                                            <input type="number" step="any" name="quantity" id="form-qty" placeholder="1" required value="1"
                                                   class="p-3 border border-slate-200 rounded-lg text-xs transition-all focus:border-indigo-500 focus:outline-none">
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Profit Margin %</label>
                                            <input type="number" step="any" name="profit_margin" id="form-margin" placeholder="15" required value="15"
                                                   class="p-3 border border-slate-200 rounded-lg text-xs transition-all focus:border-indigo-500 focus:outline-none">
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-1.5">
                                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Unit Cost Price (LKR)</label>
                                        <input type="number" step="any" name="unit_price" id="form-price" placeholder="380000" required
                                               class="p-3 border border-slate-200 rounded-lg text-xs transition-all focus:border-indigo-500 focus:outline-none">
                                    </div>

                                    <!-- Dynamic calculations card -->
                                    <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 space-y-2 mt-2">
                                        <div class="flex justify-between text-[11px] text-slate-500 font-medium">
                                            <span>Total Base Cost:</span>
                                            <span id="preview-base">LKR 0.00</span>
                                        </div>
                                        <div class="flex justify-between text-[11px] text-slate-500 font-medium">
                                            <span>Markup Amount:</span>
                                            <span id="preview-markup">LKR 0.00</span>
                                        </div>
                                        <div class="border-t border-indigo-100/80 pt-2 flex justify-between text-xs text-slate-800 font-bold">
                                            <span>Final Quoted Price:</span>
                                            <span id="preview-quoted">LKR 0.00</span>
                                        </div>
                                    </div>

                                    <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5">
                                        <i class="ri-add-line"></i> Add to Job Material List
                                    </button>
                                </form>
                            </div>

                            <!-- Excel / CSV Import Card -->
                            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                                <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Excel / CSV Import</h3>
                                    <a href="<?php echo BASE_PATH; ?>/reports/material_find?job_id=<?php echo $job_id; ?>&action=download_template" 
                                       class="text-[10px] font-bold text-indigo-650 hover:underline flex items-center gap-1">
                                        <i class="ri-download-2-line"></i> Get Template
                                    </a>
                                </div>
                                <div class="p-6 space-y-4">
                                    <p class="text-[11px] text-slate-500 leading-relaxed">
                                        Upload an Excel worksheet or CSV to bulk import material lists. Data maps directly into the <strong class="text-slate-700">job_materials</strong> database table to recalculate site-wide costs.
                                    </p>
                                    
                                    <form method="POST" action="<?php echo BASE_PATH; ?>/reports/material_find?job_id=<?php echo $job_id; ?>&action=import" enctype="multipart/form-data" class="space-y-4">
                                        <input type="hidden" name="action" value="import">
                                        <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                                        <div class="flex flex-col gap-1.5">
                                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Choose spreadsheet file (.xlsx, .xls, .csv)</label>
                                            <div class="relative border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-lg p-4 transition-all bg-slate-50/50 flex flex-col items-center justify-center text-center cursor-pointer group">
                                                <input type="file" name="excel_file" accept=".xlsx, .xls, .csv" required
                                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                       onchange="document.getElementById('file-name-display').textContent = this.files[0] ? this.files[0].name : 'No file selected'">
                                                <i class="ri-file-excel-2-line text-2xl text-slate-400 group-hover:text-indigo-500 mb-1 transition-colors"></i>
                                                <span id="file-name-display" class="text-xs font-bold text-slate-650 group-hover:text-slate-800 transition-colors">Select Excel or CSV File</span>
                                                <span class="text-[9px] text-slate-400 mt-0.5">Maximum size: 5 MB</span>
                                            </div>
                                        </div>

                                        <div class="bg-slate-50 border border-slate-150 rounded-xl p-3 text-left space-y-1.5">
                                            <h4 class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Required Template Columns:</h4>
                                            <div class="grid grid-cols-2 gap-1.5 text-[10px] text-slate-650">
                                                <div class="flex items-center gap-1 font-medium"><i class="ri-checkbox-circle-line text-emerald-500"></i> Material Name</div>
                                                <div class="flex items-center gap-1 font-medium"><i class="ri-checkbox-circle-line text-emerald-500"></i> Quantity</div>
                                                <div class="flex items-center gap-1 font-medium"><i class="ri-checkbox-circle-line text-emerald-500"></i> Unit Price (LKR)</div>
                                                <div class="flex items-center gap-1 font-medium"><i class="ri-checkbox-circle-line text-emerald-500"></i> Profit Margin (%)</div>
                                            </div>
                                        </div>

                                        <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-1.5">
                                            <i class="ri-upload-cloud-2-line"></i> Upload & Import Data
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Materials List Table -->
                        <div class="lg:col-span-8 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Materials List for Selection</h3>
                                <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-0.5 rounded-full"><?= count($materials) ?> items</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs text-left border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-bold uppercase tracking-wider">
                                            <th class="p-4">Material Details</th>
                                            <th class="p-4 text-center">Qty</th>
                                            <th class="p-4 text-right">Unit Price</th>
                                            <th class="p-4 text-right">Base Cost</th>
                                            <th class="p-4 text-center">Margin %</th>
                                            <th class="p-4 text-right">Quoted Price</th>
                                            <th class="p-4 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <?php if (empty($materials)): ?>
                                            <tr>
                                                <td colspan="7" class="p-12 text-center text-slate-400">
                                                    <i class="ri-inbox-line text-3xl block mb-2 text-slate-300"></i>
                                                    No materials registered yet. Add items using the form or click "Pre-populate 5kW Hybrid Package".
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($materials as $item): ?>
                                                <tr class="hover:bg-slate-50/50 transition-colors">
                                                    <td class="p-4 font-semibold text-slate-800">
                                                        <?php echo htmlspecialchars($item['material_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                    </td>
                                                    <td class="p-4 text-center font-medium text-slate-600">
                                                        <?php echo floatval($item['quantity']); ?>
                                                    </td>
                                                    <td class="p-4 text-right text-slate-600 font-medium">
                                                        LKR <?php echo number_format(floatval($item['unit_price']), 2); ?>
                                                    </td>
                                                    <td class="p-4 text-right text-slate-800 font-semibold">
                                                        LKR <?php echo number_format(floatval($item['total_cost']), 2); ?>
                                                    </td>
                                                    <td class="p-4 text-center font-bold text-indigo-600">
                                                        <?php echo floatval($item['profit_margin']); ?>%
                                                    </td>
                                                    <td class="p-4 text-right text-slate-900 font-bold bg-slate-50/30">
                                                        LKR <?php echo number_format(floatval($item['final_price']), 2); ?>
                                                    </td>
                                                    <td class="p-4 text-center">
                                                         <form method="POST" action="<?php echo BASE_PATH; ?>/reports/material_find?job_id=<?php echo $job_id; ?>&action=delete" onsubmit="return confirm('Are you sure you want to delete this material item?');" class="inline-block">
                                                             <input type="hidden" name="action" value="delete">
                                                             <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                                                             <input type="hidden" name="item_id" value="<?php echo intval($item['id']); ?>">
                                                             <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200" title="Delete Material">
                                                                 <i class="ri-delete-bin-line text-sm"></i>
                                                             </button>
                                                         </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
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

        // Live calculation preview in form
        const inputQty = document.getElementById('form-qty');
        const inputMargin = document.getElementById('form-margin');
        const inputPrice = document.getElementById('form-price');

        const previewBase = document.getElementById('preview-base');
        const previewMarkup = document.getElementById('preview-markup');
        const previewQuoted = document.getElementById('preview-quoted');

        function formatLKR(val) {
            return 'LKR ' + parseFloat(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function runPreview() {
            const qty = parseFloat(inputQty.value) || 0;
            const margin = parseFloat(inputMargin.value) || 0;
            const price = parseFloat(inputPrice.value) || 0;

            const baseCost = qty * price;
            const markup = baseCost * (margin / 100);
            const quoted = baseCost + markup;

            previewBase.textContent = formatLKR(baseCost);
            previewMarkup.textContent = formatLKR(markup);
            previewQuoted.textContent = formatLKR(quoted);
        }

        if (inputQty && inputMargin && inputPrice) {
            [inputQty, inputMargin, inputPrice].forEach(el => {
                el.addEventListener('input', runPreview);
            });
            runPreview();
        }

        // Initialize all Searchable Comboboxes on the page
        document.querySelectorAll('.search-combobox').forEach(combobox => {
            const input = combobox.querySelector('input[type="text"]');
            const hidden = combobox.querySelector('input[type="hidden"]');
            const list = combobox.querySelector('.jobs-suggestions-wrapper');
            const items = combobox.querySelectorAll('.job-item');
            const noFound = combobox.querySelector('.no-jobs-found-msg');
            const arrow = combobox.querySelector('.search-arrow-icon');
            
            // Toggle dropdown list visibility
            input.addEventListener('focus', () => {
                list.classList.remove('hidden');
                arrow.classList.remove('ri-arrow-down-s-line');
                arrow.classList.add('ri-arrow-up-s-line');
            });
            
            // Close dropdown list when clicking outside
            document.addEventListener('click', (e) => {
                if (!combobox.contains(e.target)) {
                    list.classList.add('hidden');
                    if (arrow) {
                        arrow.classList.remove('ri-arrow-up-s-line');
                        arrow.classList.add('ri-arrow-down-s-line');
                    }
                }
            });
            
            // Search filter
            input.addEventListener('input', () => {
                list.classList.remove('hidden');
                const query = input.value.toLowerCase().trim();
                let matches = 0;
                
                items.forEach(item => {
                    const searchText = item.getAttribute('data-search');
                    if (searchText.includes(query)) {
                        item.classList.remove('hidden');
                        matches++;
                    } else {
                        item.classList.add('hidden');
                    }
                });
                
                if (matches === 0) {
                    noFound.classList.remove('hidden');
                } else {
                    noFound.classList.add('hidden');
                }
            });
            
            // Select item
            items.forEach(item => {
                item.addEventListener('click', () => {
                    const id = item.getAttribute('data-id');
                    const display = item.getAttribute('data-display');
                    
                    hidden.value = id;
                    input.value = display;
                    list.classList.add('hidden');
                    
                    // Trigger form submit
                    const form = combobox.closest('form');
                    if (form) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
