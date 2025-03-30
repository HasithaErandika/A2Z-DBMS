<?php
require_once 'src/core/Model.php';

class TableManager extends Model {
    private $allowedTables = [
        'Attendance', 'Employee', 'Employee_Bank_Details', 'Projects', 'Jobs',
        'Operational_Expenses', 'Invoice_Data', 'Employee_Payments', 'Salary_Increments',
        'Material', 'Material_List_Per_Site'
    ];

    public function getAllowedTables() {
        return $this->allowedTables;
    }

    public function getColumns($table) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        $stmt = $this->db->query("SHOW COLUMNS FROM " . $table);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getRecords($table) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        $stmt = $this->db->query("SELECT * FROM " . $table);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($table, $data) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        $keys = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare("INSERT INTO $table ($keys) VALUES ($placeholders)");
        $stmt->execute(array_values($data));
    }

    public function update($table, $data, $idColumn, $id) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        $setClause = implode(',', array_map(fn($col) => "$col = ?", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE $table SET $setClause WHERE $idColumn = ?");
        $values = array_values($data);
        $values[] = $id;
        $stmt->execute($values);
    }

    public function delete($table, $idColumn, $id) {
        if (!in_array($table, $this->allowedTables)) {
            throw new Exception("Invalid table: $table");
        }
        $stmt = $this->db->prepare("DELETE FROM $table WHERE $idColumn = ?");
        $stmt->execute([$id]);
    }
}