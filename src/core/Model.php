<?php
// src/core/Model.php
require_once __DIR__ . '/Database.php';

class Model {
    protected $db;

    public function __construct() {
        try {
            $this->db = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            error_log("Failed to initialize database in Model: " . $e->getMessage());
            throw new Exception("Database connection error: " . $e->getMessage());
        }
    }

    protected function query($sql, $params = []) {
        try {
            if (!$this->db) {
                throw new Exception("Database connection is not initialized.");
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("General query error: " . $e->getMessage());
            return false;
        }
    }
}