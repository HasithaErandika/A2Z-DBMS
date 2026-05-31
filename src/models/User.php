<?php
require_once 'src/core/Model.php';

class User extends Model {
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("SQL prepare failed: " . $this->db->errorInfo()[2]);
            return false;
        }
        $success = $stmt->execute([$username]);
        if (!$success) {
            error_log("SQL execute failed: " . $stmt->errorInfo()[2]);
            return false;
        }
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }
        // Verify password using bcrypt hash
        if (password_verify($password, $user['password'])) {
            // Remove password from returned data to prevent leaks
            unset($user['password']);
            return $user;
        }
        return false;
    }

    public function create($username, $password, $userType) {
        // Hash password using bcrypt before storing
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (username, password, user_type) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$username, $hashedPassword, $userType]);
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