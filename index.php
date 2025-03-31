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

// Check if the request is for a static file
$staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'pdf'];
$fileExtension = pathinfo($request, PATHINFO_EXTENSION);
if (in_array($fileExtension, $staticExtensions)) {
    // Serve the file directly from the filesystem
    $filePath = __DIR__ . '/assets/' . $request;
    if (file_exists($filePath)) {
        // Set appropriate MIME type
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
    '/admin/records' => ['AdminController', 'records'],
    '/admin/employees' => ['AdminController', 'employees'],
    '/admin/jobs' => ['AdminController', 'jobs'],
    '/admin/attendance' => ['AdminController', 'attendance'],
    '/admin/expenses' => ['AdminController', 'expenses'],
    '/admin/users' => ['AdminController', 'users'],
    '/admin/sql' => ['AdminController', 'sql'],
];

// Rest of your routing logic remains unchanged
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