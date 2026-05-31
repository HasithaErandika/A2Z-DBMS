<?php
// src/helpers/Formatter.php

namespace App\Helpers;

class Formatter {
    public static function getBooleanIcon($value) {
        $isTrue = ($value === 'Yes' || $value === true || $value === 1 || $value === '1');
        return $isTrue 
            ? '<i class="fas fa-check" style="color: #10B981;"></i>' 
            : '<i class="fas fa-times" style="color: #EF4444;"></i>';
    }

    public static function getPresenceDisplay($value) {
        $val = (float)$value;
        if ($val === 1.0) return '<span style="color: #10B981;">Full Day</span>';
        if ($val === 0.5) return '<span style="color: #F59E0B;">Half Day</span>';
        if ($val === 0.0) return '<span style="color: #EF4444;">Not Attended</span>';
        return htmlspecialchars((string)$value);
    }

    public static function getTransactionTypeDisplay($value) {
        if ($value === 'In') {
            return '<span style="color: #10B981;">Received</span>';
        } elseif ($value === 'Out') {
            return '<span style="color: #EF4444;">Disbursed</span>';
        }
        return htmlspecialchars((string)$value);
    }

    public static function getCompletionStatus($value) {
        $val = (float)$value;
        switch ($val) {
            case 0.0: return '<span style="color: #EF4444;">Not Started</span>';
            case 0.1: return '<span style="color: #D1D5DB;">Cancelled</span>';
            case 0.2: return '<span style="color: #3B82F6;">Started</span>';
            case 0.3: return '<span style="color: #6D28D9;">Postponed</span>';
            case 0.5: return '<span style="color: #F59E0B;">Ongoing</span>';
            case 1.0: return '<span style="color: #10B981;">Completed</span>';
            default: return htmlspecialchars((string)$value);
        }
    }
}
