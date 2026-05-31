<?php
// src/repositories/BaseRepository.php

namespace App\Repositories;

use Database;
use PDO;
use Exception;

abstract class BaseRepository {
    protected $db;
    protected $table;
    protected $primaryKey;

    public function __construct() {
        if (!isset($_SESSION['db_username']) || !isset($_SESSION['db_password'])) {
            throw new Exception("Session credentials missing for Database connection.");
        }
        $this->db = Database::getInstance($_SESSION['db_username'], $_SESSION['db_password'])->getConnection();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    public function update($id, array $data) {
        $fields = array_keys($data);
        $setClauses = array_map(fn($f) => "{$f} = ?", $fields);
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        
        $values = array_values($data);
        $values[] = $id;
        
        return $stmt->execute($values);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
}
