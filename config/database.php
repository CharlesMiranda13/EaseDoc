<?php
/**
 * database.php — Centralized Database Connection Configuration
 * 
 * Provides unified database connectivity across the entire application.
 */

require_once __DIR__ . '/config.php';

// Legacy constant aliases for backward compatibility with existing class.php files
if (!defined('db_host')) define('db_host', DB_HOST);
if (!defined('db_user')) define('db_user', DB_USER);
if (!defined('db_name')) define('db_name', DB_NAME);
if (!defined('db_pass')) define('db_pass', DB_PASS);

/**
 * Standard database connection base class
 */
class db_connect
{
    public $host = db_host;
    public $user = db_user;
    public $pass = db_pass;
    public $name = db_name;
    public $conn;
    public $error;

    public function connect()
    {
        try {
            mysqli_report(MYSQLI_REPORT_OFF);
            $this->conn = @new mysqli($this->host, $this->user, $this->pass, $this->name);

            if ($this->conn && !$this->conn->connect_error) {
                $this->conn->set_charset('utf8mb4');
                return $this->conn;
            }

            // Fallback for local development if empty password was overridden
            $this->conn = @new mysqli('localhost', 'root', '', $this->name);
            if ($this->conn && !$this->conn->connect_error) {
                $this->conn->set_charset('utf8mb4');
                return $this->conn;
            }

            $this->error = "Fatal Error: Can't connect to database";
            return false;
        } catch (\Throwable $th) {
            $this->error = "Fatal Error: Can't connect to database";
            return false;
        }
    }
}
