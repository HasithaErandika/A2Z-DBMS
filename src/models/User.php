<?php
require_once 'src/core/Model.php';

class User extends Model {
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("SQL prepare failed: " . $this->db->errorInfo()[2]);
            return false;
        }
        $success = $stmt->execute([$username, $password]);
        if (!$success) {
            error_log("SQL execute failed: " . $stmt->errorInfo()[2]);
            return false;
        }
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Fetched user: " . print_r($user, true));
        return $user ?: false;
    }

    public function create($username, $password, $userType) {
        $sql = "INSERT INTO users (username, password, user_type) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$username, $password, $userType]);
    }

    public function getById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $username, $userType) {
        $sql = "UPDATE users SET username = ?, user_type = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$username, $userType, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}