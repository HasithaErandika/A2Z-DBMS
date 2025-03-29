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
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = "Please enter both username and password.";
            } else {
                $user = $this->userModel->authenticate($username, $password);
                
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_type'] = $user['user_type'];
                    
                    // Redirect based on user type
                    if ($user['user_type'] === 'admin') {
                        header("Location: " . BASE_PATH . "/admin");
                    } else {
                        header("Location: " . BASE_PATH . "/user/dashboard");
                    }
                    exit;
                } else {
                    $error = "Invalid username or password.";
                }
            }
        }

        // Show login form
        ob_start();
        include_once "src/views/auth/login.php";
        $content = ob_get_clean();
        include_once "src/views/layouts/home.php";
    }
    
    public function logout() {
        session_destroy();
        header("Location: " . BASE_PATH . "/login");
        exit;
    }
} 