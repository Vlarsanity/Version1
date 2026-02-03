<?php
require_once '../includes/env-loader.php';


class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $host = EnvLoader::get('DB_HOST', 'localhost');
        $port = EnvLoader::get('DB_PORT', 3306);
        $name = EnvLoader::get('DB_NAME', 'smarttravel');
        $user = EnvLoader::get('DB_USER', 'root');
        $pass = EnvLoader::get('DB_PASS', '');

        $this->connection = new mysqli($host, $user, $pass, $name, $port);
        
        if ($this->connection->connect_error) {
            throw new Exception("Database connection failed: " . $this->connection->connect_error);
        }
        
        $this->connection->set_charset("utf8mb4");
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}