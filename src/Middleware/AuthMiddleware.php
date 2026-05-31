<?php
// src/middleware/AuthMiddleware.php

namespace App\Middleware;

use App\Helpers\Request;
use App\Helpers\Response;

class AuthMiddleware implements Middleware {
    public function handle(Request $request, Response $response, callable $next) {
        if (!isset($_SESSION['db_username']) || !isset($_SESSION['db_password'])) {
            $response->redirect(BASE_PATH . "/login");
            return;
        }
        return $next($request, $response);
    }
}
