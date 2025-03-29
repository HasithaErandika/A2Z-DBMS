<?php
class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $user = $this->db->fetch($sql, [$username]);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
        return false;
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $sql = "SELECT id, username, role, created_at FROM users WHERE id = ?";
        return $this->db->fetch($sql, [$_SESSION['user_id']]);
    }

    public function createUser($username, $password, $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        
        $data = [
            'username' => $username,
            'password' => $hashedPassword,
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('users', $data);
    }

    public function updateUser($userId, $data) {
        $allowedFields = ['username', 'role'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));
        
        if (isset($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        }

        return $this->db->update('users', $updateData, 'id = ?', [$userId]);
    }

    public function deleteUser($userId) {
        return $this->db->delete('users', 'id = ?', [$userId]);
    }

    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $user = $this->getCurrentUser();
        if ($user['role'] === 'admin') {
            return true;
        }

        // Add more permission checks based on roles
        return false;
    }
} 