<?php
// Application settings
define('APP_NAME', 'A2Z-DBMS');
define('APP_URL', 'http://localhost/A2Z-DBMS');
define('APP_VERSION', '1.0.0');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Time zone
date_default_timezone_set('Asia/Kolkata');

// Security settings
define('HASH_COST', 12);
define('TOKEN_EXPIRY', 3600); // 1 hour 