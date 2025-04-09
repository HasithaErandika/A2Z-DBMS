<?php
require_once 'src/core/Controller.php';
require_once 'src/core/Database.php';

class AuthController extends Controller {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['db_username']) && isset($_POST['db_password'])) {
                $dbUsername = trim($_POST['db_username'] ?? '');
                $dbPassword = trim($_POST['db_password'] ?? '');

                error_log("Login attempt - DB Username: $dbUsername");

                if (empty($dbUsername)) {
                    $error = "Please enter the database username.";
                } elseif (empty($dbPassword)) {
                    $error = "Please enter the database password.";
                } else {
                    try {
                        Database::resetInstance(); // Clear any existing connection
                        $db = Database::getInstance($dbUsername, $dbPassword);

                        // Store both username and password in session
                        $_SESSION['db_username'] = $dbUsername;
                        $_SESSION['db_password'] = $dbPassword; // Note: Consider security implications
                        error_log("Login successful - Session set: " . print_r($_SESSION, true));
                        header("Location: " . BASE_PATH . "/admin"); // Single dashboard
                        exit;
                    } catch (Exception $e) {
                        $error = "Database connection failed: " . $e->getMessage();
                        error_log("DB Connection error: " . $e->getMessage());
                    }
                }
            } else {
                $error = "Database username and password are required.";
            }
        }

        $data = [
            'dbname' => 'suramalr_a2zOperationalDB',
            'error' => $error ?? null
        ];
        $this->render('auth/login', $data);
    }
    
    public function logout() {
        Database::resetInstance(); // Clear database connection
        session_destroy();
        header("Location: " . BASE_PATH . "/login");
        exit;
    }
}