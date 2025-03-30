<?php
require_once 'src/core/Controller.php';
require_once 'src/models/User.php';

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['username']) && isset($_POST['password'])) {
                $username = trim($_POST['username'] ?? '');
                $password = trim($_POST['password'] ?? '');
                $user_type = trim($_POST['user_type'] ?? '');

                error_log("Login attempt - Username: $username, Password: $password, User Type: $user_type");

                if (empty($username) || empty($password)) {
                    $error = "Please enter both username and password.";
                } else {
                    $user = $this->userModel->authenticate($username, $password);
                    error_log("Authenticate result: " . print_r($user, true));
                    
                    if ($user && isset($user['user_type'])) {
                        if ($user['user_type'] === $user_type) {
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['username'] = $user['username'];
                            $_SESSION['user_type'] = $user['user_type'];
                            error_log("Login successful - Session set: " . print_r($_SESSION, true));
                            
                            if ($user['user_type'] === 'admin') {
                                header("Location: " . BASE_PATH . "/admin");
                            } else {
                                header("Location: " . BASE_PATH . "/user/dashboard");
                            }
                            exit;
                        } else {
                            $error = "Invalid user type for these credentials.";
                            error_log("User type mismatch - DB: {$user['user_type']}, Form: $user_type");
                        }
                    } else {
                        $error = "Invalid username or password.";
                        error_log("Authentication failed - No user found or missing user_type");
                    }
                }
            }
        }

        include_once "src/views/auth/login.php";
    }
    
    public function logout() {
        session_destroy();
        header("Location: " . BASE_PATH . "/login");
        exit;
    }
}