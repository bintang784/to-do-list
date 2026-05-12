<?php

class Connect {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "uas_todolist";
    private $conn;

    private static $instance = null;

    private function __construct() {
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $error) {
            die("Database Error: " . $error->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Connect();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
