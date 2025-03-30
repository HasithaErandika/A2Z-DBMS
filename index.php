<?php
// Start session once at the entry point
session_start();

// Define base path
define('BASE_PATH', '/A2Z-DBMS');

// Get the requested URL and normalize it
$request = $_SERVER['REQUEST_URI'];
$request = str_replace(BASE_PATH, '', $request); // Remove BASE_PATH prefix
$request = parse_url($request, PHP_URL_PATH); // Remove query string
$request = rtrim($request, '/'); // Remove trailing slash

// Default to home if no path specified
if (empty($request)) {
    $request = '/';
}

// Define routes
$routes = [
    '/' => ['HomeController', 'index'],
    '/login' => ['AuthController', 'login'],
    '/logout' => ['AuthController', 'logout'],
    '/admin' => ['AdminController', 'dashboard'],
    '/admin/databases' => ['AdminController', 'databases'],
    '/admin/tables' => ['AdminController', 'tables'],
    '/admin/users' => ['AdminController', 'users'],
    '/admin/sql' => ['AdminController', 'sql']
];

// Check if route exists
if (isset($routes[$request])) {
    $controllerName = $routes[$request][0];
    $methodName = $routes[$request][1];
    
    // Load controller file
    $controllerFile = "src/controllers/{$controllerName}.php";
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        // Check if controller class and method exist
        if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {
            $controllerInstance = new $controllerName();
            $controllerInstance->$methodName();
            exit;
        } else {
            error_log("Method {$methodName} not found in {$controllerName}");
        }
    } else {
        error_log("Controller file not found: {$controllerFile}");
    }
}

// If route not found or controller/method doesn’t exist, show 404
require_once 'src/controllers/ErrorController.php';
$errorController = new ErrorController();
$errorController->notFound();