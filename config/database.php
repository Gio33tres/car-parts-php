<?php
class Database {
    private $conn;

    public function __construct() {
        $host = 'localhost';
        $dbname = 'shopdb';
        $username = 'root';
        $password = '4rm4d1ll0_bc3';

        try {
            $this->conn = new PDO(
                "mysql:host=$host;dbname=$dbname", 
                $username, 
                $password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>