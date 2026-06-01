<?php
class Database {
    private static $instance = null;
    private $connection;

    private function __construct($dbUsername, $dbPassword) {
        try {
            $config = require __DIR__ . '/../config/database.php';
            $host = $config['host'] ?? 'localhost';
            $port = $config['port'] ?? '3306';
            $dbname = $config['dbname'] ?? 'suramalr_a2zOperationalDB';
            $charset = $config['charset'] ?? 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
            
            $this->connection = new PDO(
                $dsn,
                $dbUsername,
                $dbPassword,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $_SESSION['db_username'] = $dbUsername;
            $_SESSION['db_password'] = self::encryptPassword($dbPassword);
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance($dbUsername = null, $dbPassword = null) {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/database.php';
            
            // Check if shared DB credentials are set in configuration first
            if (!empty($config['user']) && !empty($config['pass'])) {
                $dbUsername = $config['user'];
                $dbPassword = $config['pass'];
            }

            if ($dbUsername === null || $dbPassword === null) {
                if (isset($_SESSION['db_username']) && isset($_SESSION['db_password'])) {
                    $dbUsername = $_SESSION['db_username'];
                    $dbPassword = self::decryptPassword($_SESSION['db_password']);
                }
            } else {
                // If they passed session values directly, check if it's the encrypted session password
                if (isset($_SESSION['db_password']) && $dbPassword === $_SESSION['db_password']) {
                    $dbPassword = self::decryptPassword($dbPassword);
                }
            }

            // Fallback to default env values if still empty
            if (empty($dbUsername) || empty($dbPassword)) {
                $dbUsername = $config['user'] ?? $_ENV['DB_USER'] ?? 'root';
                $dbPassword = $config['pass'] ?? $_ENV['DB_PASS'] ?? 'Login@123456';
            }

            self::$instance = new self($dbUsername, $dbPassword);
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public static function resetInstance() {
        self::$instance = null;
    }

    public static function getDatabaseName() {
        $config = require __DIR__ . '/../config/database.php';
        return $config['dbname'] ?? 'suramalr_a2zOperationalDB';
    }

    public static function encryptPassword($password) {
        $key = $_ENV['APP_KEY'] ?? '83b7f16f5c88ea7651a2d5be32a2491a';
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($password, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public static function decryptPassword($encryptedPassword) {
        $key = $_ENV['APP_KEY'] ?? '83b7f16f5c88ea7651a2d5be32a2491a';
        $data = base64_decode($encryptedPassword);
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        if (strlen($data) < $ivLength) {
            return '';
        }
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    }
}