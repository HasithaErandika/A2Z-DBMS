<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/A2Z-DBMS');
if (!defined('FULL_BASE_URL')) define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');

$filters               = $filters ?? ['year' => '', 'month' => ''];
$report_title          = $report_title ?? 'Full Company Expense Report (All Time)';
$total_invoices        = floatval($total_invoices ?? 0);
$total_operational     = floatval($total_operational ?? $total_expenses ?? 0);
$total_expenses        = $total_operational;
$total_employee_costs  = floatval($total_employee_costs ?? 0);
$total_epf_employer    = floatval($total_epf_employer ?? 0);
$total_etf             = floatval($total_etf ?? 0);
$total_statutory       = floatval($total_statutory ?? 0);
$total_costs           = floatval($total_costs ?? ($total_operational + $total_employee_costs));
$profit                = floatval($profit ?? 0);
$total_invoices_count  = intval($total_invoices_count ?? 0);
$total_jobs            = intval($total_jobs ?? 0);
$total_job_capacity    = floatval($total_job_capacity ?? 0);
$expenses_by_category  = $expenses_by_category ?? [];
$full_breakdown        = $full_breakdown ?? $expenses_by_category;
$employee_costs_by_type= $employee_costs_by_type ?? [];
$statutory_rows        = $statutory_rows ?? [];
$detailed_rows         = $detailed_rows ?? [];
$error                 = $error ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A2Z Engineering – Expense & Revenue Audit</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css" rel="stylesheet">
    <?php require_once __DIR__ . '/../partials/theme.php'; ?>
    <style>
        .dt-search input,.dt-length select{padding:.35rem .7rem;font-size:.75rem;border:1px solid #e2e8f0;border-radius:.375rem;background:#fff;outline:none}
        .dt-search input:focus{border-color:#10b981}
        .badge{display:inline-flex;align-items:center;padding:.2rem .6rem;border-radius:9999px;font-size:.65rem;font-weight:700}
        .badge-green{background:#dcfce7;color:#166534}
        .badge-red{background:#fee2e2;color:#991b1b}
        .badge-slate{background:#f1f5f9;color:#475569}
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased overflow-x-hidden min-h-screen">
<?php
$activePage    = 'reports';
$headerTitle   = 'Expenses & Revenue Audit';
$headerSubtitle= 'Comprehensive profit analysis with per-transaction auditor ledger and statutory obligations.';
$breadcrumb    = 'Reports / Expenses';
require_once __DIR__ . '/../partials/sidebar.php';
?>
<div class="ml-64 transition-all duration-300 min-h-screen flex flex-col justify-between" id="container">
<div>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<main class="p-8 animate-fadeIn">

<!-- ── Action bar ──────────────────────────────────────────────────── -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div class="text-xs text-slate-500 font-semibold"><?= htmlspecialchars($report_title) ?></div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="<?= htmlspecialchars(BASE_PATH.'/admin/reports', ENT_QUOTES) ?>"
           class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg text-xs font-bold flex items-center gap-1.5 shadow-sm">
            <i class="ri-arrow-left-line"></i> Back
        </a>
        <button onclick="window.print()"
                class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-lg text-xs font-bold flex items-center gap-1.5 shadow-sm">
            <i class="ri-printer-line"></i> Print
        </button>
        <button onclick="downloadCSV()"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-xs font-bold flex items-center gap-1.5 shadow-sm">
            <i class="ri-file-excel-line"></i> Export CSV
        </button>
    </div>
</div>

<?php if ($error): ?>
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
    <i class="ri-error-warning-line"></i> <?= htmlspecialchars($error) ?>
</div>
<?php else: ?>

<!-- ── Filters ───────────────────────────────────────────────────────── -->
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-8">
    <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Report Filters</h3>
    <form method="POST" action="<?= htmlspecialchars(BASE_PATH.'/reports/expenses_report', ENT_QUOTES) ?>"
          class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end" id="filterForm">
        <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Year</label>
            <select name="year" class="p-2.5 border border-slate-200 rounded-lg text-xs focus:border-emerald-500 focus:outline-none bg-white">
                <option value="">All Years</option>
                <?php foreach (range(2022, 2030) as $y): ?>
                <option value="<?= $y ?>" <?= $filters['year'] == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex flex-col gap-1.5">
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Month</label>
            <select name="month" class="p-2.5 border border-slate-200 rounded-lg text-xs focus:border-emerald-500 focus:outline-none bg-white">
                <option value="">All Months</option>
                <?php for ($m=1;$m<=12;$m++): ?>
                <option value="<?= $m ?>" <?= $filters['month']==$m?'selected':'' ?>><?= date('F',mktime(0,0,0,$m,1)) ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-lg text-xs font-bold flex items-center justify-center gap-1.5 h-[38px]">
            <i class="ri-filter-3-line"></i> Apply
        </button>
    </form>
</div>

<!-- ── KPI Summary Cards ───────────────────────────────────────────── -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Revenue</h4>
        <p class="text-xl font-extrabold text-slate-900">LKR <?= number_format($total_invoices,2) ?></p>
        <small class="text-[11px] text-slate-500"><?= $total_invoices_count ?> Invoices</small>
    </div>
    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Costs</h4>
        <p class="text-xl font-extrabold text-slate-900">LKR <?= number_format($total_costs,2) ?></p>
        <small class="text-[11px] text-slate-500">Operational + Labour + Statutory</small>
    </div>
    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Net <?= $profit>=0?'Profit':'Loss' ?></h4>
        <p class="text-xl font-extrabold <?= $profit>=0?'text-emerald-600':'text-red-500' ?>">LKR <?= number_format($profit,2) ?></p>
        <small class="text-[11px] text-slate-500"><?= $total_invoices>0?number_format(($profit/$total_invoices)*100,1):'0.0' ?>% margin</small>
    </div>
    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Active Jobs</h4>
        <p class="text-xl font-extrabold text-slate-900"><?= $total_jobs ?></p>
        <small class="text-[11px] text-slate-500"><?= number_format($total_job_capacity,1) ?> kW capacity</small>
    </div>
</div>

<!-- ── Charts ─────────────────────────────────────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
        <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Revenue vs Total Costs</h3>
        <div class="h-[260px] flex items-center"><canvas id="revenueVsCostChart"></canvas></div>
    </div>
    <div class="bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
        <h3 class="text-xs font-bold text-slate-900 mb-4 uppercase tracking-wider">Full Expense Breakdown (All Categories incl. Labour)</h3>
        <div class="h-[260px] flex items-center"><canvas id="expenseBreakdownChart"></canvas></div>
</div>

<!-- ── Executive Cost Pillars Summary ───────────────────────────── -->
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-8 shadow-sm">
    <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Executive Cost Analysis Breakdown</h3>
        <span class="bg-indigo-50 border border-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full">Auditor Grade</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-100">
        <div class="p-6 text-center">
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Operational Expenses</span>
            <span class="text-xl font-extrabold text-slate-950 block">LKR <?= number_format($total_operational,2) ?></span>
            <small class="text-[10px] text-slate-500 font-semibold"><?= $total_costs>0?number_format(($total_operational/$total_costs)*100,1):'0.0' ?>% of total</small>
        </div>
        <div class="p-6 text-center">
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Site Material Costs</span>
            <span class="text-xl font-extrabold text-slate-950 block">LKR <?= number_format($total_material_cost,2) ?></span>
            <small class="text-[10px] text-slate-500 font-semibold"><?= $total_costs>0?number_format(($total_material_cost/$total_costs)*100,1):'0.0' ?>% of total</small>
        </div>
        <div class="p-6 text-center">
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mb-1">Employee & Labour Costs</span>
            <span class="text-xl font-extrabold text-slate-950 block">LKR <?= number_format($total_employee_costs,2) ?></span>
            <small class="text-[10px] text-slate-500 font-semibold"><?= $total_costs>0?number_format(($total_employee_costs/$total_costs)*100,1):'0.0' ?>% of total</small>
        </div>
    </div>
</div>

<!-- ── Cost Summary Tables side by side ─────────────────────────── -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    <!-- Operational by category -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900">Operational Expenses by Category</h3>
        </div>
        <table class="w-full text-xs text-left">
            <thead><tr class="bg-slate-100 text-slate-700">
                <th class="p-3 font-bold">Category</th>
                <th class="p-3 font-bold text-right">Amount (LKR)</th>
                <th class="p-3 font-bold text-right">% of Total Costs</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            <?php foreach ($expenses_by_category as $cat => $amt):
                $pct = $total_costs > 0 ? ($amt/$total_costs)*100 : 0; ?>
            <tr class="hover:bg-slate-50">
                <td class="p-3 font-medium"><?= htmlspecialchars($cat) ?></td>
                <td class="p-3 text-right"><?= number_format($amt,2) ?></td>
                <td class="p-3 text-right text-slate-500"><?= number_format($pct,1) ?>%</td>
            </tr>
            <?php endforeach; ?>
            <tr class="bg-slate-50 font-bold border-t border-slate-200">
                <td class="p-3">Total Operational</td>
                <td class="p-3 text-right"><?= number_format($total_operational,2) ?></td>
                <td class="p-3 text-right text-slate-600"><?= $total_costs>0?number_format(($total_operational/$total_costs)*100,1):'0.0' ?>%</td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Employee / Labour / Statutory -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-900">Employee, Labour & Statutory Costs</h3>
        </div>
        <table class="w-full text-xs text-left">
            <thead><tr class="bg-slate-100 text-slate-700">
                <th class="p-3 font-bold">Cost Type</th>
                <th class="p-3 font-bold text-right">Amount (LKR)</th>
                <th class="p-3 font-bold text-right">% of Total Costs</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            <?php foreach ($employee_costs_by_type as $type => $amt):
                $pct = $total_costs > 0 ? ($amt/$total_costs)*100 : 0; ?>
            <tr class="hover:bg-slate-50">
                <td class="p-3 font-medium"><?= htmlspecialchars($type) ?></td>
                <td class="p-3 text-right"><?= number_format($amt,2) ?></td>
                <td class="p-3 text-right text-slate-500"><?= number_format($pct,1) ?>%</td>
            </tr>
            <?php endforeach; ?>
            <tr class="bg-slate-50 font-bold border-t border-slate-200">
                <td class="p-3">Total Employee & Statutory</td>
                <td class="p-3 text-right"><?= number_format($total_employee_costs,2) ?></td>
                <td class="p-3 text-right text-slate-600"><?= $total_costs>0?number_format(($total_employee_costs/$total_costs)*100,1):'0.0' ?>%</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ── Statutory Obligations per Employee ───────────────────────── -->
<?php if (!empty($statutory_rows)): ?>
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-8 overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-slate-900">Statutory Obligations per Employee (Fixed-Salary)</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">EPF: Employee 8% deduction / Employer 12% contribution &nbsp;|&nbsp; ETF: Employer 3% contribution</p>
        </div>
        <div class="flex gap-3 text-xs">
            <span class="badge badge-slate">EPF Total: LKR <?= number_format($total_epf_employer,2) ?></span>
            <span class="badge badge-slate">ETF Total: LKR <?= number_format($total_etf,2) ?></span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left" id="statutory-table">
            <thead><tr class="bg-slate-100 text-slate-700">
                <th class="p-3 font-bold">Employee</th>
                <th class="p-3 font-bold text-right">Basic Salary</th>
                <th class="p-3 font-bold text-right">EPF Employee (8%)</th>
                <th class="p-3 font-bold text-right">EPF Employer (12%)</th>
                <th class="p-3 font-bold text-right">ETF Employer (3%)</th>
                <th class="p-3 font-bold text-right">Company Obligation</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            <?php foreach ($statutory_rows as $sr): ?>
            <tr class="hover:bg-slate-50">
                <td class="p-3 font-medium text-slate-900"><?= htmlspecialchars($sr['emp_name']) ?></td>
                <td class="p-3 text-right"><?= number_format($sr['basic_salary'],2) ?></td>
                <td class="p-3 text-right text-blue-600"><?= number_format($sr['epf_employee'],2) ?></td>
                <td class="p-3 text-right text-amber-600"><?= number_format($sr['epf_employer'],2) ?></td>
                <td class="p-3 text-right text-purple-600"><?= number_format($sr['etf'],2) ?></td>
                <td class="p-3 text-right font-bold text-slate-900"><?= number_format($sr['total_statutory'],2) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot><tr class="bg-slate-100 font-bold text-slate-800">
                <td class="p-3">Totals</td>
                <td class="p-3 text-right"><?= number_format(array_sum(array_column($statutory_rows,'basic_salary')),2) ?></td>
                <td class="p-3 text-right text-blue-600"><?= number_format(array_sum(array_column($statutory_rows,'epf_employee')),2) ?></td>
                <td class="p-3 text-right text-amber-600"><?= number_format(array_sum(array_column($statutory_rows,'epf_employer')),2) ?></td>
                <td class="p-3 text-right text-purple-600"><?= number_format(array_sum(array_column($statutory_rows,'etf')),2) ?></td>
                <td class="p-3 text-right"><?= number_format(array_sum(array_column($statutory_rows,'total_statutory')),2) ?></td>
            </tr></tfoot>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- ── Auditor Transaction Ledger ────────────────────────────────── -->
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm mb-8 overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-slate-900">Full Expense Transaction Ledger</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Every operational expense line-item — searchable, sortable, paginated for audit purposes</p>
        </div>
        <span class="badge badge-slate"><?= count($detailed_rows) ?> records</span>
    </div>
    <div class="overflow-x-auto p-4">
        <table id="ledger-table" class="w-full text-xs text-left border-collapse">
            <thead><tr class="bg-slate-100 text-slate-700">
                <th class="p-3 font-bold">ID</th>
                <th class="p-3 font-bold">Date</th>
                <th class="p-3 font-bold">Category</th>
                <th class="p-3 font-bold">Description</th>
                <th class="p-3 font-bold text-right">Amount (LKR)</th>
                <th class="p-3 font-bold">Voucher #</th>
                <th class="p-3 font-bold">Employee</th>
                <th class="p-3 font-bold">Job / Location</th>
                <th class="p-3 font-bold">Company</th>
                <th class="p-3 font-bold text-center">Paid</th>
                <th class="p-3 font-bold">Remarks</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            <?php foreach ($detailed_rows as $dr):
                $isPaid = (int)($dr['paid'] ?? 0); ?>
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="p-3 text-slate-500"><?= $dr['expense_id'] ?></td>
                <td class="p-3 font-medium text-slate-800"><?= htmlspecialchars($dr['expensed_date']) ?></td>
                <td class="p-3"><span class="badge badge-slate"><?= htmlspecialchars($dr['expenses_category']) ?></span></td>
                <td class="p-3 max-w-[200px] truncate text-slate-700" title="<?= htmlspecialchars($dr['description'] ?? '') ?>"><?= htmlspecialchars($dr['description'] ?? '—') ?></td>
                <td class="p-3 text-right font-bold text-slate-900"><?= number_format(floatval($dr['expense_amount']),2) ?></td>
                <td class="p-3 text-slate-600"><?= htmlspecialchars($dr['voucher_number'] ?? '—') ?></td>
                <td class="p-3 text-slate-700"><?= htmlspecialchars($dr['emp_name']) ?></td>
                <td class="p-3 text-slate-600"><?= htmlspecialchars($dr['job_location']) ?></td>
                <td class="p-3 text-slate-600"><?= htmlspecialchars($dr['company_reference']) ?></td>
                <td class="p-3 text-center">
                    <span class="badge <?= $isPaid ? 'badge-green' : 'badge-red' ?>"><?= $isPaid ? 'Paid' : 'Unpaid' ?></span>
                </td>
                <td class="p-3 text-slate-500 max-w-[160px] truncate" title="<?= htmlspecialchars($dr['remarks'] ?? '') ?>"><?= htmlspecialchars($dr['remarks'] ?? '—') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; // end !$error ?>
</main>
</div>

<footer class="border-t border-slate-200 bg-white py-6 px-8 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-4">
    <div class="flex items-center space-x-2">
        <img class="h-6 w-auto opacity-70" src="<?= BASE_PATH ?>/src/assets/images/logo.png" alt="A2Z Logo">
        <span>&copy; <?= date('Y') ?> A2Z Engineering. Internal DBMS Portal.</span>
    </div>
    <div class="flex space-x-6">
        <a href="https://a2zengineering.lk" target="_blank" class="hover:text-slate-900">Corporate Site</a>
        <span class="text-slate-300">|</span><span>DBMS v2.3.0</span>
    </div>
</footer>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
<script>
function toggleSidebar(){const s=document.getElementById('sidebar'),c=document.getElementById('container');s.classList.toggle('hidden');if(window.innerWidth>=1024){c.classList.toggle('ml-0');c.classList.toggle('ml-64');}}

$(document).ready(function(){
    // Ledger DataTable
    $('#ledger-table').DataTable({
        pageLength: 25,
        lengthMenu: [10,25,50,100,200],
        order: [[1,'desc']],
        columnDefs: [{orderable:false, targets:[10]}],
        language:{search:'_INPUT_', searchPlaceholder:'Search transactions...'}
    });

    // Statutory table (no pagination needed usually, but add search)
    if($('#statutory-table tbody tr').length > 10){
        $('#statutory-table').DataTable({pageLength:10, searching:true});
    }

    // Revenue vs Total Costs bar chart
    new Chart(document.getElementById('revenueVsCostChart'), {
        type: 'bar',
        data: {
            labels: ['Revenue', 'Operational Exp.', 'Labour & Statutory', 'Net <?= $profit>=0?"Profit":"Loss" ?>'],
            datasets: [{
                data: [<?= $total_invoices ?>, <?= $total_operational ?>, <?= $total_employee_costs ?>, <?= $profit ?>],
                backgroundColor: ['#3b82f6','#f59e0b','#8b5cf6','<?= $profit>=0?"#059669":"#ef4444" ?>'],
                borderRadius: 6, borderWidth: 0
            }]
        },
        options: {
            responsive:true, maintainAspectRatio:false,
            plugins:{legend:{display:false}, tooltip:{callbacks:{label:c=>'LKR '+c.parsed.y.toLocaleString()}}},
            scales:{y:{beginAtZero:true, grid:{borderDash:[4,4]}, ticks:{callback:v=>'LKR '+v.toLocaleString()}}}
        }
    });

    // Full breakdown doughnut (ALL categories incl. labour + statutory)
    const bdKeys = <?= json_encode(array_keys($full_breakdown)) ?>;
    const bdVals = <?= json_encode(array_values($full_breakdown)) ?>;
    const colors = ['#3b82f6','#8b5cf6','#059669','#f59e0b','#ef4444','#06b6d4','#ec4899','#6366f1','#f97316','#14b8a6','#a855f7','#0ea5e9','#f43f5e','#10b981','#84cc16','#eab308','#d946ef','#64748b','#475569','#0284c7','#0891b2','#0d9488','#16a34a','#ca8a04','#d97706','#dc2626','#db2777','#9333ea','#4f46e5','#2563eb'];
    new Chart(document.getElementById('expenseBreakdownChart'), {
        type: 'doughnut',
        data: {
            labels: bdKeys.length ? bdKeys : ['No Data'],
            datasets: [{
                data: bdVals.length ? bdVals : [1],
                backgroundColor: colors,
                borderColor: '#fff', borderWidth: 3
            }]
        },
        options: {
            responsive:true, maintainAspectRatio:false,
            plugins:{
                legend:{position:'right', labels:{boxWidth:12, font:{size:10}}},
                tooltip:{callbacks:{label:c=>c.label+': LKR '+Number(c.parsed).toLocaleString()}}
            }
        }
    });
});

function downloadCSV(){
    const form = document.getElementById('filterForm');
    const url = new URL(form.action, location.href);
    new FormData(form).forEach((v,k)=>url.searchParams.append(k,v));
    url.searchParams.set('download_csv','1');
    window.location.href = url.toString();
}
</script>
</body>
</html>