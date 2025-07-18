<?php
class HomeController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function index() {
        if (isset($_SESSION['user_id'])) {
            header('Location: /client/dashboard');
            exit;
        }
        
        $viewPath = __DIR__ . '/../views/auth/login.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View file not found: $viewPath");
        }
    }
}