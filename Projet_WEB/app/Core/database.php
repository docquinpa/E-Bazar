<?php 
class Database {
    private static $instance = null;
    private $pdo;
    private function __construct() {
        $config = require __DIR__ ."../../config/config.php.example";
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8";
        $this->pdo = new PDO($dsn, $config["db_user"], $config["db_pass"], [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
        ); 
    }
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}


?>