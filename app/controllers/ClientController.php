<?php
class ClientController {
    private $conn;
    private $clientModel;
    private $productModel;
    private $userModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->clientModel = new Client($conn);
        $this->productModel = new Product($conn);
        $this->userModel = new User($conn);
    }

    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $clientId = $_SESSION['user_id'];
        
        $client = $this->userModel->getById($clientId);
        if ($client) {
            $_SESSION['username'] = $client['userid'];
        }
        
        $products = $this->productModel->getAll();
        $purchaseHistory = $this->clientModel->getPurchaseHistory($clientId);

        include __DIR__ . '/../views/clients/dashboard.php';
    }
}
?>