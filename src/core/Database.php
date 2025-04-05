<?php
class Database {
    private static $instance = null;
    private $connection;

    private function __construct($dbUsername, $dbPassword) {
        try {
            $this->connection = new PDO(
                "mysql:host=localhost;dbname=suramalr_a2zOperationalDB",
                $dbUsername,
                $dbPassword,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $_SESSION['db_username'] = $dbUsername;
            $_SESSION['db_password'] = $dbPassword; // Store password in session
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance($dbUsername = null, $dbPassword = null) {
        if (self::$instance === null) {
            if ($dbUsername === null || $dbPassword === null) {
                if (isset($_SESSION['db_username']) && isset($_SESSION['db_password'])) {
                    $dbUsername = $_SESSION['db_username'];
                    $dbPassword = $_SESSION['db_password'];
                } else {
                    throw new Exception("Database credentials not provided.");
                }
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
}