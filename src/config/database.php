<?php
// src/config/database.php

return [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'dbname' => $_ENV['DB_NAME'] ?? 'operational_db',
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    // Fallback shared credentials
    'user' => $_ENV['DB_USER'] ?? null,
    'pass' => $_ENV['DB_PASS'] ?? null,
];
