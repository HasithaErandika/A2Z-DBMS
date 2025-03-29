<?php
require_once __DIR__ . '/Database.php';

class Model {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    protected function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            echo "Query error: " . $e->getMessage();
            return false;
        }
    }
} 