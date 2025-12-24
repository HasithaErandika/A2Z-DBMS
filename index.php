<?php
// A2Z-DBMS/index.php

session_start();

// Define BASE_PATH for routing (relative to domain root)
define('BASE_PATH', '/A2Z-DBMS'); // Adjust to '' if app is at root (e.g., records.a2zengineering.net/)

// Optionally define full base URL for absolute links if needed
define('FULL_BASE_URL', 'https://records.a2zengineering.net/A2Z-DBMS');

// Clean the request URI
$request = $_SERVER['REQUEST_URI'];
$request = str_replace(BASE_PATH, '', $request); // Remove BASE_PATH prefix
$request = parse_url($request, PHP_URL_PATH); // Get path only
$request = rtrim($request, '/'); // Remove trailing slashes
if (empty($request)) {
    $request = '/';
}

// Handle static files
$staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'pdf'];
$fileExtension = pathinfo($request, PATHINFO_EXTENSION);
if (in_array($fileExtension, $staticExtensions)) {
    $filePath = __DIR__ . '/src/assets' . $request;
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
    }
}

// Define routes
$routes = [
    '/' => ['HomeController', 'index'],
    '/login' => ['AuthController', 'login'],
    '/logout' => ['AuthController', 'logout'],
    '/admin' => ['AdminController', 'dashboard'],
    '/admin/dashboard' => ['AdminController', 'dashboard'],
    '/admin/tables' => ['AdminController', 'tables'],
    '/admin/reports' => ['AdminController', 'records'], // Matches https://records.a2zengineering.net/A2Z-DBMS/admin/reports
    '/admin/employees' => ['AdminController', 'employees'],
    '/admin/jobs' => ['AdminController', 'jobs'],
    '/admin/attendance' => ['AdminController', 'attendance'],
    '/admin/expenses' => ['AdminController', 'expenses'],
    '/admin/users' => ['AdminController', 'users'],
    '/admin/sql' => ['AdminController', 'sql'],
    '/reports/cost_calculation' => ['AdminController', 'costCalculation'],
    '/reports/expenses_report' => ['AdminController', 'expenseReport'], // Fixed typo from previous
    '/reports/wages_report' => ['AdminController', 'wagesReport'], // Added for consistency with reports.php
    '/reports/material_find' => ['AdminController', 'materialFind'], // Added from reports.php
    '/reports/a2z_engineering_jobs' => ['AdminController', 'a2zEngineeringJobs'], // Added from reports.php
    '/reports/maintenance_report' => ['AdminController', 'maintenanceReport'], // Added maintenance report
    '/user/dashboard' => ['UserController', 'dashboard'],
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
        } else {
            error_log("Controller $controllerName or method $methodName not found for route $request");
        }
    } else {
        error_log("Controller file $controllerFile not found for route $request");
    }
} else {
    // Handle dynamic routes (e.g., /admin/manageTable/{table})
    $parts = explode('/', $request);
    if (count($parts) >= 3 && $parts[1] === 'admin') {
        $controllerName = 'AdminController';
        $controllerFile = "src/controllers/{$controllerName}.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controllerInstance = new $controllerName();

            if ($parts[2] === 'manageTable' && !empty($parts[3])) {
                $methodName = 'manageTable';
                $table = $parts[3];
                if (method_exists($controllerInstance, $methodName)) {
                    $controllerInstance->$methodName($table);
                    exit;
                } else {
                    error_log("Method $methodName not found in $controllerName for route $request");
                }
            }
        } else {
            error_log("Controller file $controllerFile not found for dynamic route $request");
        }
    }
}

// Fallback to 404
require_once 'src/controllers/ErrorController.php';
$errorController = new ErrorController();
$errorController->notFound();