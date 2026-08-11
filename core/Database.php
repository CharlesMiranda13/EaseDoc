<?php
/**
 * Database.php — Singleton Database Connection
 * 
 * Provides a single point of access to the database connection
 * using the credentials from config/database.php
 */

require_once __DIR__ . '/../config/database.php';

class Database {
    private static ?Database $instance = null;
    private mysqli $conn;
    
    private function __construct() {
        mysqli_report(MYSQLI_REPORT_OFF);
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->conn->connect_error) {
            // Try fallback to root/empty password for local development
            $this->conn = new mysqli('localhost', 'root', '', DB_NAME);
            if ($this->conn->connect_error) {
                die("Fatal Error: Can't connect to database");
            }
        }
        
        // Set charset to prevent encoding-based attacks
        $this->conn->set_charset('utf8mb4');
    }
    
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection(): mysqli {
        return $this->conn;
    }
    
    public function escape(string $value): string {
        return $this->conn->real_escape_string($value);
    }
    
    /**
     * Close the database connection
     */
    public function close(): void {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    
    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserializing of the instance
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}