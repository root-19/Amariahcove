<?php
class Database {
    private static $instance = null;
    private $host = "localhost";   // Change if needed
    private $db_name = "amariah";
    private $username = "root";    // Change for your DB user
    private $password = "";        // Change for your DB password
    private $conn;

    // Private constructor to prevent direct instantiation
    private function __construct() {
        $this->createConnection();
    }

    // Get singleton instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Create connection
    private function createConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false, // Disable persistent connections to prevent connection leaks
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    // Get connection
    public function getConnection() {
        // Check if connection is still alive
        try {
            $this->conn->query('SELECT 1');
        } catch (PDOException $e) {
            // Connection lost, reconnect
            $this->createConnection();
        }
        
        return $this->conn;
    }

    // Legacy method for backward compatibility
    public function connect() {
        return $this->getConnection();
    }

    // Close connection
    public function close() {
        $this->conn = null;
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
