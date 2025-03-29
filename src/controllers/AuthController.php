<?php
require_once __DIR__ . '/../classes/User.php';

class AuthController {
    private $user;

    public function __construct() {
        $this->user = new User();
    }

    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Username and password are required'
            ];
        }

        if ($this->user->login($username, $password)) {
            return [
                'success' => true,
                'message' => 'Login successful',
                'redirect' => 'tables.php'
            ];
        }

        return [
            'success' => false,
            'message' => 'Invalid username or password'
        ];
    }

    public function logout() {
        if ($this->user->logout()) {
            return [
                'success' => true,
                'message' => 'Logged out successfully',
                'redirect' => 'index.php'
            ];
        }

        return [
            'success' => false,
            'message' => 'Logout failed'
        ];
    }

    public function checkAuth() {
        if (!$this->user->isLoggedIn()) {
            header('Location: index.php');
            exit;
        }
    }

    public function requirePermission($permission) {
        if (!$this->user->hasPermission($permission)) {
            header('Location: error.php?code=403');
            exit;
        }
    }
} 