<?php
// src/middleware/Middleware.php

namespace App\Middleware;

use App\Helpers\Request;
use App\Helpers\Response;

interface Middleware {
    public function handle(Request $request, Response $response, callable $next);
}
