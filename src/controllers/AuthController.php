<?php
// src/controllers/AuthController.php

require_once 'src/core/Controller.php';
require_once 'src/core/Database.php';

class AuthController extends Controller {
    public function login($request = null, $response = null) {
        if (!$request) $request = new App\Helpers\Request();
        if (!$response) $response = new App\Helpers\Response();

        if (isset($_SESSION['db_username']) && isset($_SESSION['db_password'])) {
            $response->redirect(BASE_PATH . "/admin");
            return;
        }

        if ($request->isPost()) {
            $dbUsername = trim($request->post('db_username') ?? '');
            $dbPassword = trim($request->post('db_password') ?? '');

            error_log("Login attempt - DB Username: $dbUsername");

            if (empty($dbUsername)) {
                $error = "Please enter the database username.";
            } elseif (empty($dbPassword)) {
                $error = "Please enter the database password.";
            } else {
                try {
                    Database::resetInstance(); // Clear any existing connection
                    // Connect using the application's default database configuration
                    $db = Database::getInstance();
                    $connection = $db->getConnection();

                    // Query the users table for the user
                    $stmt = $connection->prepare("SELECT * FROM users WHERE username = ?");
                    $stmt->execute([$dbUsername]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user && ($dbPassword === $user['password'] || password_verify($dbPassword, $user['password']))) {
                        // Store user session info
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['user_type'] = $user['user_type'];
                        
                        // Set the DB credentials for the system connection in session
                        $config = require 'src/config/database.php';
                        $_SESSION['db_username'] = $config['user'] ?? 'root';
                        $_SESSION['db_password'] = Database::encryptPassword($config['pass'] ?? 'Login@123456');

                        error_log("Login successful for user: $dbUsername");
                        $response->redirect(BASE_PATH . "/admin");
                        exit;
                    } else {
                        $error = "Invalid username or password.";
                        error_log("Login failed: Invalid credentials for user $dbUsername");
                    }
                } catch (Exception $e) {
                    $error = "Database connection failed. Please check system config.";
                    error_log("DB Connection error during login for user $dbUsername: " . $e->getMessage());
                }
            }
        }

        $data = [
            'dbname' => Database::getDatabaseName(),
            'error' => $error ?? null
        ];
        $this->render('auth/login', $data);
    }
    
    public function logout($request = null, $response = null) {
        if (!$response) $response = new App\Helpers\Response();
        Database::resetInstance(); // Clear database connection
        
        // Unset session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        $response->redirect(BASE_PATH . "/login");
    }
}