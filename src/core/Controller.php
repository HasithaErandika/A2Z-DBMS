<?php
class Controller {
    protected function view($view, $data = []) {
        // Extract data to make variables available in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        include_once "src/views/{$view}.php";
        
        // Get the contents and clean the buffer
        $content = ob_get_clean();
        
        // Include the layout
        include_once "src/views/layouts/main.php";
    }
    
    protected function redirect($url) {
        header("Location: {$url}");
        exit();
    }
    
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    protected function post($key = null) {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? null;
    }
    
    protected function get($key = null) {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? null;
    }

    protected function render($view, $data = []) {
        $viewFile = __DIR__ . "/../views/$view.php"; // e.g., src/views/reports/cost_calculation.php
        error_log("Rendering view: $viewFile"); // Debug log
        if (file_exists($viewFile)) {
            extract($data);
            require_once $viewFile;
        } else {
            error_log("View not found: $viewFile");
            header("HTTP/1.0 404 Not Found");
            require_once __DIR__ . "/../controllers/ErrorController.php";
            $errorController = new ErrorController();
            $errorController->notFound();
            exit;
        }
    }
} 