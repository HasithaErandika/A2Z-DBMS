<?php
// src/config/routes.php

use App\Middleware\AuthMiddleware;
use App\Middleware\RateLimitMiddleware;

return [
    '/' => ['AuthController', 'login', [RateLimitMiddleware::class]],
    '/login' => ['AuthController', 'login', [RateLimitMiddleware::class]],
    '/logout' => ['AuthController', 'logout', []],
    '/support' => ['HomeController', 'support', []],
    
    // Admin Routes
    '/admin' => ['DashboardController', 'dashboard', [AuthMiddleware::class]],
    '/admin/dashboard' => ['DashboardController', 'dashboard', [AuthMiddleware::class]],
    '/admin/tables' => ['DashboardController', 'tables', [AuthMiddleware::class]],
    '/admin/reports' => ['ReportController', 'records', [AuthMiddleware::class]],
    
    // Report Routes
    '/reports/cost_calculation' => ['ReportController', 'costCalculation', [AuthMiddleware::class]],
    '/reports/expenses_report' => ['ReportController', 'expenseReport', [AuthMiddleware::class]],
    '/reports/wages_report' => ['ReportController', 'wagesReport', [AuthMiddleware::class]],
    '/reports/material_find' => ['ReportController', 'materialFind', [AuthMiddleware::class]],
    '/reports/a2z_engineering_jobs' => ['ReportController', 'a2zEngineeringJobs', [AuthMiddleware::class]],
    '/reports/maintenance_report' => ['ReportController', 'maintenanceReport', [AuthMiddleware::class]],
];
