<?php
// src/middleware/RateLimitMiddleware.php

namespace App\Middleware;

use App\Helpers\Request;
use App\Helpers\Response;

class RateLimitMiddleware implements Middleware {
    private $maxRequests = 100; // max requests
    private $timeFrame = 60;    // per 60 seconds

    public function handle(Request $request, Response $response, callable $next) {
        $now = time();
        if (!isset($_SESSION['rate_limit'])) {
            $_SESSION['rate_limit'] = [];
        }

        // Filter out requests older than timeframe
        $_SESSION['rate_limit'] = array_filter($_SESSION['rate_limit'], function($timestamp) use ($now) {
            return ($now - $timestamp) < $this->timeFrame;
        });

        if (count($_SESSION['rate_limit']) >= $this->maxRequests) {
            header('HTTP/1.1 429 Too Many Requests');
            header('Retry-After: ' . $this->timeFrame);
            $response->json(['success' => false, 'error' => 'Too many requests. Please try again in a minute.'], 429);
            return;
        }

        $_SESSION['rate_limit'][] = $now;
        return $next($request, $response);
    }
}
