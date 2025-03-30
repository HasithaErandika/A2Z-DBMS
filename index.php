<?php
session_start();
define('BASE_PATH', '/A2Z-DBMS');

$request = $_SERVER['REQUEST_URI'];
$request = str_replace(BASE_PATH, '', $request);
$request = parse_url($request, PHP_URL_PATH);
$request = rtrim($request, '/');
if (empty($request)) {
    $request = '/';
}

$routes = [
    '/' => ['HomeController', 'index'],
    '/login' => ['AuthController', 'login'],
    '/logout' => ['AuthController', 'logout'],
    '/admin' => ['AdminController', 'dashboard'],
    '/admin/dashboard' => ['AdminController', 'dashboard'], // Explicit dashboard route
    '/admin/tables' => ['AdminController', 'tables'],      // New tables route
    '/admin/records' => ['AdminController', 'records'],    // New records route
    '/admin/employees' => ['AdminController', 'employees'],
    '/admin/jobs' => ['AdminController', 'jobs'],
    '/admin/attendance' => ['AdminController', 'attendance'],
    '/admin/expenses' => ['AdminController', 'expenses'],
    '/admin/users' => ['AdminController', 'users'],
    '/admin/sql' => ['AdminController', 'sql'],
];

if (isset($routes[$request])) {
    $controllerName = $routes[$request][0];
    $methodName = $routes[$request][1];
    $controllerFile = "src/controllers/{$controllerName}.php";
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {
            $controllerInstance = new $controllerName();
            $controllerInstance->$methodName();
            exit;
        }
    }
} else {
    $parts = explode('/', $request);
    if (count($parts) >= 3 && $parts[1] === 'admin' && $parts[2] === 'manageTable' && !empty($parts[3])) {
        $controllerName = 'AdminController';
        $methodName = 'manageTable';
        $table = $parts[3];
        $controllerFile = "src/controllers/{$controllerName}.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {
                $controllerInstance = new $controllerName();
                $controllerInstance->$methodName($table);
                exit;
            }
        }
    }
}

require_once 'src/controllers/ErrorController.php';
$errorController = new ErrorController();
$errorController->notFound();