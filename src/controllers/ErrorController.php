<?php
require_once 'src/core/Controller.php';

class ErrorController extends Controller {
    public function notFound() {
        $errorCode = 404;
        $errorMessage = "Oops! The page you're looking for doesn't exist.";
        
        ob_start();
        include_once "src/views/errors/error.php";
        $content = ob_get_clean();
        include_once "src/views/layouts/home.php";
    }

    public function serverError() {
        $errorCode = 500;
        $errorMessage = "Oops! Something went wrong on our end.";
        
        ob_start();
        include_once "src/views/errors/error.php";
        $content = ob_get_clean();
        include_once "src/views/layouts/home.php";
    }
} 