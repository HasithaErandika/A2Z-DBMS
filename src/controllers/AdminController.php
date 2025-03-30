<?php
require_once 'src/core/Controller.php';

class AdminController extends Controller {
    public function dashboard() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header("Location: " . BASE_PATH . "/login");
            exit;
        }
        include_once "src/views/admin/dashboard.php";
    }

    // Placeholder methods for other routes
    public function databases() { echo "Databases page"; }
    public function tables() { echo "Tables page"; }
    public function users() { echo "Users page"; }
    public function sql() { echo "SQL page"; }
}