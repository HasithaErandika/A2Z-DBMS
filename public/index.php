<?php
// public/index.php

// Define application root directory
define('APP_ROOT', dirname(__DIR__));

// Load Composer Autoloader
if (file_exists(APP_ROOT . '/vendor/autoload.php')) {
    require_once APP_ROOT . '/vendor/autoload.php';
}

// Load Environment Variables from .env if present
if (class_exists('Dotenv\Dotenv') && file_exists(APP_ROOT . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);
    $dotenv->load();
}

// Define BASE_PATH for routing (relative to domain root, default to env or config)
$basePath = $_ENV['BASE_PATH'] ?? '/A2Z-DBMS';
define('BASE_PATH', $basePath);

// Define full base URL for absolute links if needed (default to env or config)
$fullBaseUrl = $_ENV['FULL_BASE_URL'] ?? 'https://records.a2zengineering.net/A2Z-DBMS';
define('FULL_BASE_URL', $fullBaseUrl);

// Set secure session cookie parameters (httponly, secure, samesite)
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'),
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// Clean the request URI
$request = $_SERVER['REQUEST_URI'];
$request = str_replace(BASE_PATH, '', $request); // Remove BASE_PATH prefix
$request = parse_url($request, PHP_URL_PATH); // Get path only
$request = rtrim($request, '/'); // Remove trailing slashes
if (empty($request)) {
    $request = '/';
}

// Handle static files (fallback route for direct asset requests)
$staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'pdf'];
$fileExtension = pathinfo($request, PATHINFO_EXTENSION);
if (in_array($fileExtension, $staticExtensions)) {
    $filePath = APP_ROOT . $request;
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

// Define routes using config file
$routes = require APP_ROOT . '/src/config/routes.php';

$requestObj = new App\Helpers\Request();
$responseObj = new App\Helpers\Response();

// Handle defined routes
if (isset($routes[$request])) {
    $controllerName = $routes[$request][0];
    $methodName = $routes[$request][1];
    $middlewares = $routes[$request][2] ?? [];

    $controllerFile = APP_ROOT . "/src/controllers/{$controllerName}.php";
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {
            $pipeline = new App\Core\Pipeline();
            $pipeline->send($requestObj, $responseObj)
                ->through($middlewares)
                ->then(function($req, $res) use ($controllerName, $methodName) {
                    $controllerInstance = new $controllerName();
                    $controllerInstance->$methodName($req, $res);
                    exit;
                });
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
        $controllerFile = APP_ROOT . "/src/controllers/{$controllerName}.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            if (class_exists($controllerName)) {
                $controllerInstance = new $controllerName();
                if ($parts[2] === 'manageTable' && !empty($parts[3])) {
                    $methodName = 'manageTable';
                    $table = $parts[3];
                    if (method_exists($controllerInstance, $methodName)) {
                        $middlewares = [App\Middleware\AuthMiddleware::class];
                        $pipeline = new App\Core\Pipeline();
                        $pipeline->send($requestObj, $responseObj)
                            ->through($middlewares)
                            ->then(function($req, $res) use ($controllerInstance, $methodName, $table) {
                                $controllerInstance->$methodName($table, $req, $res);
                                exit;
                            });
                    } else {
                        error_log("Method $methodName not found in $controllerName for route $request");
                    }
                }
            }
        } else {
            error_log("Controller file $controllerFile not found for dynamic route $request");
        }
    }
}

// Fallback to 404
header("HTTP/1.0 404 Not Found");
$viewFile = APP_ROOT . '/src/views/errors/error.php';
if (file_exists($viewFile)) {
    $_GET['code'] = '404'; // Set code for view logic
    require_once $viewFile;
    exit;
}
echo "404 Not Found";
