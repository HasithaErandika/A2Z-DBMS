<?php
require_once 'src/core/Controller.php';

class AdminController extends Controller {
    public function __construct() {
        parent::__construct();
        // Check if user is logged in and is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header("Location: " . BASE_PATH . "/login");
            exit;
        }
    }

    public function dashboard() {
        include_once "src/views/admin/dashboard.php";
    }
    
    public function databases() {
        include_once "src/views/admin/databases.php";
    }
    
    public function tables() {
        include_once "src/views/admin/tables.php";
    }
    
    public function users() {
        include_once "src/views/admin/users.php";
    }
    
    public function sql() {
        include_once "src/views/admin/sql.php";
    }
} 