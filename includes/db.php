<?php
require_once 'config.php';

class Database {
    private $connection;

    public function __construct() {
        $this->connection = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->connection->connect_error) {
            // more descriptive guidance for local development
            die("Connection failed: " . $this->connection->connect_error . ".<br>" .
                "Please make sure the MySQL/MariaDB server is running (e.g. start it via XAMPP Control Panel) " .
                "and that the credentials in includes/config.php are correct.");
        }
        
        $this->connection->set_charset("utf8mb4");
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql) {
        return $this->connection->query($sql);
    }

    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }

    public function escapeString($string) {
        return $this->connection->real_escape_string($string);
    }

    public function close() {
        $this->connection->close();
    }
}

// Buat instance database global
$db = new Database();
