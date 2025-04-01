<?php
// A2Z-DBMS/index.php

session_start();
define('BASE_PATH', '/A2Z-DBMS');

$request = $_SERVER['REQUEST_URI'];
$request = str_replace(BASE_PATH, '', $request);
$request = parse_url($request, PHP_URL_PATH);
$request = rtrim($request, '/');
if (empty($request)) {
    $request = '/';
}

// Check if the request is for a static file
$staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'pdf'];
$fileExtension = pathinfo($request, PATHINFO_EXTENSION);
if (in_array($fileExtension, $staticExtensions)) {
    // Serve the file directly from src/assets/
    $filePath = __DIR__ . '/src/assets' . $request; // Updated to src/assets/
    if (file_exists($filePath)) {
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
        ];
        header('Content-Type: ' . ($mimeTypes[$fileExtension] ?? 'application/octet-stream'));
        readfile($filePath);
        exit;
    } else {
        // File not found, proceed to 404
    }
}

$routes = [
    '/' => ['HomeController', 'index'],
    '/login' => ['AuthController', 'login'],
    '/logout' => ['AuthController', 'logout'],
    '/admin' => ['AdminController', 'dashboard'],
    '/admin/dashboard' => ['AdminController', 'dashboard'],
    '/admin/tables' => ['AdminController', 'tables'],
    '/admin/records' => ['AdminController', 'records'], // Note: Should this be /admin/reports?
    '/admin/employees' => ['AdminController', 'employees'],
    '/admin/jobs' => ['AdminController', 'jobs'],
    '/admin/attendance' => ['AdminController', 'attendance'],
    '/admin/expenses' => ['AdminController', 'expenses'],
    '/admin/users' => ['AdminController', 'users'],
    '/admin/sql' => ['AdminController', 'sql'],
    '/reports/cost_calculation' => ['AdminController', 'costCalculation'], 
    '/reports/expenses_report' => ['AdminController', 'expensesReport'],
];

// Handle defined routes
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
    // Handle dynamic routes
    $parts = explode('/', $request);
    if (count($parts) >= 3 && $parts[1] === 'admin') {
        $controllerName = 'AdminController';
        $controllerFile = "src/controllers/{$controllerName}.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controllerInstance = new $controllerName();

            // Handle /admin/manageTable/{table}
            if ($parts[2] === 'manageTable' && !empty($parts[3])) {
                $methodName = 'manageTable';
                $table = $parts[3];
                if (method_exists($controllerInstance, $methodName)) {
                    $controllerInstance->$methodName($table);
                    exit;
                }
            }
            // Handle /admin/costCalculation/{table} (kept for backward compatibility, optional)
            elseif ($parts[2] === 'costCalculation' && !empty($parts[3])) {
                $methodName = 'costCalculation';
                $table = $parts[3];
                if (method_exists($controllerInstance, $methodName)) {
                    $controllerInstance->$methodName($table);
                    exit;
                }
            }
        }
    }
}

// Fallback to 404
require_once 'src/controllers/ErrorController.php';
$errorController = new ErrorController();
$errorController->notFound();