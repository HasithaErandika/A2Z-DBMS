<?php
// src/services/ExportService.php

namespace App\Services;

class ExportService {
    public function generateWagesCSV($title, $total_wages, $daily, $fixed, $epf, $advance, $count, $avg) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="wages_report_' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, [$title]); fputcsv($out, ['']);
        fputcsv($out, ['Total Wages', number_format($total_wages, 2)]);
        fputcsv($out, ['Daily Wages', number_format($daily, 2)]);
        fputcsv($out, ['Fixed Wages', number_format($fixed, 2)]);
        fputcsv($out, ['EPF Cost', number_format($epf, 2)]);
        fputcsv($out, ['Advance Payments', number_format($advance, 2)]);
        fputcsv($out, ['Employee Count', $count]);
        fputcsv($out, ['Average Wage', number_format($avg, 2)]);
        fclose($out);
        exit;
    }

    public function generateWagesPDF($report_title, $employees) {
        if (class_exists('Dompdf\Dompdf')) {
            $this->renderPDFWithDompdf($report_title, $employees);
        } else {
            $this->renderPDFWithBrowserPrint($report_title, $employees);
        }
    }

    private function renderPDFWithDompdf($title, $employees) {
        $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
        $html = $this->buildSlipHTML($title, $employees);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Salary_Slips_" . date('Y-m-d') . ".pdf", ['Attachment' => true]);
        exit;
    }

    private function renderPDFWithBrowserPrint($title, $employees) {
        echo $this->buildSlipHTML($title, $employees, false);
        echo '<script>window.print(); setTimeout(() => window.close(), 1000);</script>';
        exit;
    }

    private function buildSlipHTML($title, $employees, $forDompdf = true) {
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>
            body{font-family:Arial,sans-serif;margin:15px;font-size:13px;background:#f9f9f9;}
            .slip{width:48%;float:left;margin:1%;border:2px solid #1e40af;padding:20px;background:white;box-sizing:border-box;}
            .header{text-align:center;font-size:18px;font-weight:bold;color:#1e40af;margin-bottom:15px;}
            table{width:100%;border-collapse:collapse;margin:10px 0;}
            th,td{border:1px solid #333;padding:8px;}
            th{background:#1e40af;color:white;text-align:left;}
            .clear{clear:both;height:20px;}
            @page{margin:0.5cm;}
        </style></head><body>';

        $count = 0;
        foreach ($employees as $emp) {
            if ($count % 2 == 0 && $count > 0) $html .= '<div class="clear"></div>';
            if ($count % 2 == 0 && $count > 0 && !$forDompdf) $html .= '<div style="page-break-before:always;"></div>';

            $isFixed = strtoupper($emp['rate_type'] ?? 'DAILY') === 'FIXED';
            $basic   = $isFixed ? floatval($emp['basic_salary'] ?? $emp['rate_amount'] ?? 0) : 0;
            $days    = $emp['attendance_summary']['total_presence'] ?? 0;
            $rate    = $isFixed ? 0 : floatval($emp['rate_amount'] ?? 0);
            $earned  = $isFixed ? $basic : $days * $rate;

            $etf     = $isFixed ? $basic * 0.03 : 0;
            $epfEmp  = $isFixed ? $basic * 0.08 : 0;
            $epfComp = $isFixed ? $basic * 0.12 : 0;
            $payable = $isFixed ? $basic : $earned;
            $paid    = array_sum($emp['paid_amount'] ?? [0,0,0,0]);
            $advance_paid = $emp['advance_details']['paid_amount'] ?? 0;
            $advance_deduction = $emp['advance_details']['deduction_amount'] ?? 0;
            $advance_total = $advance_paid + $advance_deduction;
            
            // Net Payable calculation
            if ($isFixed) {
                $net_payable = $basic - $epfEmp - $advance_total - $paid;
            } else {
                $net_payable = $earned - $advance_total - $paid;
            }
            
            $html .= '<div class="slip">
                <div class="header">A2Z Engineering (Pvt) Ltd<br>Salary Slip – ' . htmlspecialchars($title) . '</div>
                <p><strong>Name:</strong> ' . htmlspecialchars($emp['emp_name']) . ' | <strong>ID:</strong> ' . $emp['emp_id'] . '</p>
                <table>
                    <tr><th>Earnings & Employer Costs</th><th>Amount</th><th>Deductions</th><th>Amount</th></tr>
                    <tr><td>Basic / Daily</td><td>' . number_format($earned,2) . '</td><td>EPF Employee (8%)</td><td>' . number_format($epfEmp,2) . '</td></tr>
                    <tr><td>EPF Company (12%)</td><td>' . number_format($epfComp,2) . '</td><td>Advances/Other</td><td>' . number_format($paid,2) . '</td></tr>
                    <tr><td>ETF Company (3%)</td><td>' . number_format($etf,2) . '</td><td>Advance Total</td><td>' . number_format($advance_total,2) . '</td></tr>
                    <tr><td><strong>Total Earnings</strong></td><td><strong>' . number_format($earned,2) . '</strong></td>
                        <td><strong>Total Ded.</strong></td><td><strong>' . number_format($epfEmp+$paid+$advance_total,2) . '</strong></td></tr>
                    <tr><td colspan="4" style="text-align:center;font-size:16px;background:#e6f2ff;">
                        <strong>Net Take-Home Pay: LKR ' . number_format($net_payable,2) . '</strong>
                    </td></tr>
                </table>
                <small>Generated: ' . date('d M Y H:i') . '</small>
            </div>';
            $count++;
        }
        $html .= '</body></html>';
        return $html;
    }
}
