<?php
// src/repositories/ExpenseRepository.php

namespace App\Repositories;

use PDO;
use Exception;
use PDOException;

class ExpenseRepository extends BaseRepository {
    protected $table = 'operational_expenses';
    protected $primaryKey = 'expense_id';

    public function getTotalExpenses() {
        try {
            $stmt = $this->db->query("SELECT SUM(expense_amount) FROM operational_expenses");
            return floatval($stmt->fetchColumn() ?: 0);
        } catch (PDOException $e) {
            error_log("Error in getTotalExpenses: " . $e->getMessage());
            return 0.0;
        }
    }

    public function getTodaysExpenses() {
        try {
            $today = date('Y-m-d');
            $stmt = $this->db->prepare("SELECT SUM(expense_amount) FROM operational_expenses WHERE DATE(expensed_date) = ?");
            $stmt->execute([$today]);
            return floatval($stmt->fetchColumn() ?: 0);
        } catch (PDOException $e) {
            error_log("Error in getTodaysExpenses: " . $e->getMessage());
            return 0.0;
        }
    }

    public function getMonthlyExpenses() {
        try {
            $month = date('Y-m');
            $stmt = $this->db->prepare("SELECT SUM(expense_amount) FROM operational_expenses WHERE expensed_date LIKE ?");
            $stmt->execute(["$month%"]);
            return floatval($stmt->fetchColumn() ?: 0);
        } catch (PDOException $e) {
            error_log("Error in getMonthlyExpenses: " . $e->getMessage());
            return 0.0;
        }
    }

    public function getTotalPayments() {
        try {
            $stmt = $this->db->query("SELECT SUM(paid_amount) FROM employee_payments");
            return floatval($stmt->fetchColumn() ?: 0);
        } catch (PDOException $e) {
            error_log("Error in getTotalPayments: " . $e->getMessage());
            return 0.0;
        }
    }
}
