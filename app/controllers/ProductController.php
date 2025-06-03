<?php
class ProductController {
    private $conn;
    private $productModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->productModel = new Product($conn);
    }

    public function index() {
        $products = $this->productModel->getAll();
        include __DIR__ . '/../views/products/index.php';
    }

    public function purchase() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            $quantity = $_POST['quantity'];
            $clientId = $_SESSION['user_id'];

            if ($this->productModel->purchase($productId, $clientId, $quantity)) {
                $_SESSION['message'] = "Compra completada!";
            } else {
                $_SESSION['message'] = "No se puede comprar. Sin stock.";
            }

            header('Location: /client/dashboard');
            exit;
        }
    }
}
?>