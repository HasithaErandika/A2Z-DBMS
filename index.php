<?php
session_start();

// Define base path
define('BASE_PATH', '/A2Z-DBMS');

// Get the requested URL
$request = $_SERVER['REQUEST_URI'];
$request = str_replace(BASE_PATH, '', $request);

// Remove query string
if (($pos = strpos($request, '?')) !== false) {
    $request = substr($request, 0, $pos);
}

// Remove trailing slash
$request = rtrim($request, '/');

// If no path specified, default to home
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
    $controller = $routes[$request][0];
    $method = $routes[$request][1];
    
    // Check if controller file exists
    $controllerFile = "src/controllers/{$controller}.php";
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        // Check if method exists
        if (method_exists($controller, $method)) {
            $controllerInstance = new $controller();
            $controllerInstance->$method();
            exit;
        }
    }
}

// If route not found or controller/method doesn't exist, show 404
require_once 'src/controllers/ErrorController.php';
$errorController = new ErrorController();
$errorController->notFound();
