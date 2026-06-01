<?php
// src/core/Controller.php

class Controller {
    protected function render($view, $data = []) {
        $viewFile = __DIR__ . "/../views/$view.php"; // e.g., src/views/reports/cost_calculation.php
        error_log("Rendering view: $viewFile"); // Debug log
        if (file_exists($viewFile)) {
            extract($data);
            require_once $viewFile;
        } else {
            error_log("View not found: $viewFile");
            header("HTTP/1.0 404 Not Found");
            $errorViewFile = __DIR__ . "/../views/errors/error.php";
            if (file_exists($errorViewFile)) {
                $_GET['code'] = '404';
                require_once $errorViewFile;
                exit;
            }
            echo "404 Not Found";
            exit;
        }
    }
}