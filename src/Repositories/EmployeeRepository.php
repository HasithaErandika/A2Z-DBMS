<?php
// src/repositories/EmployeeRepository.php

namespace App\Repositories;

use PDO;
use Exception;
use PDOException;

class EmployeeRepository extends BaseRepository {
    protected $table = 'employees';
    protected $primaryKey = 'emp_id';

    public function getActiveEmployees() {
        try {
            $stmt = $this->db->query("SELECT emp_id, emp_name FROM employees ORDER BY emp_name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getActiveEmployees: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalEmployees() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM employees");
            return intval($stmt->fetchColumn());
        } catch (PDOException $e) {
            error_log("Error in getTotalEmployees: " . $e->getMessage());
            return 0;
        }
    }

    public function fetchEmployeeName($empId) {
        if (empty($empId)) return 'No Employee';
        try {
            $stmt = $this->db->prepare("SELECT emp_name FROM employees WHERE emp_id = ?");
            $stmt->execute([$empId]);
            $empName = $stmt->fetchColumn();
            return $empName !== false ? htmlspecialchars($empName) : 'Unknown Employee';
        } catch (PDOException $e) {
            error_log("Error in fetchEmployeeName: " . $e->getMessage());
            return 'Error';
        }
    }

    public function getEmployeeOptions() {
        try {
            $stmt = $this->db->query("SELECT emp_id, emp_name FROM employees ORDER BY emp_name ASC");
            $options = ['<option value="">Select Employee</option>'];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $options[] = sprintf(
                    '<option value="%s">%s</option>',
                    htmlspecialchars($row['emp_id']),
                    htmlspecialchars($row['emp_name'])
                );
            }
            return implode("\n", $options);
        } catch (PDOException $e) {
            error_log("Error in getEmployeeOptions: " . $e->getMessage());
            return '<option value="">Error loading employees</option>';
        }
    }
}
