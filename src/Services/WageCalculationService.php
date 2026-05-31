<?php
// src/services/WageCalculationService.php

namespace App\Services;

class WageCalculationService {
    public function calculateDailyWage($days, $rate, $advanceTotal, $paidAmountArray) {
        $earned = $days * floatval($rate);
        $paid = array_sum($paidAmountArray ?? [0]);
        $netPayable = $earned - $advanceTotal - $paid;
        return [
            'earned' => $earned,
            'advance_total' => $advanceTotal,
            'paid_total' => $paid,
            'net_payable' => $netPayable
        ];
    }

    public function calculateFixedSalary($basic, $advanceTotal, $paidAmountArray) {
        $basic = floatval($basic);
        $etf = $basic * 0.03;
        $epfEmp = $basic * 0.08;
        $epfComp = $basic * 0.12;
        $payable = $basic; // Gross payable base is the basic salary
        $paid = array_sum($paidAmountArray ?? [0]);
        $netPayable = $basic - $epfEmp - $advanceTotal - $paid;

        return [
            'basic' => $basic,
            'etf' => $etf,
            'epf_employee' => $epfEmp,
            'epf_employer' => $epfComp,
            'payable' => $payable,
            'advance_total' => $advanceTotal,
            'paid_total' => $paid,
            'net_payable' => $netPayable
        ];
    }
}
