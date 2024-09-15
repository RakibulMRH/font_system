<?php
class Database {
    private static $instance = null;
    private $conn;

    private $host = 'localhost';
    private $db_name = 'font_system';
    private $username = 'root';
    private $password = '';

    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->username,
                $this->password,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
            );
            // Set Error Mode to Exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Database could not be connected: " . $e->getMessage();
            exit;
        }
    }

    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
