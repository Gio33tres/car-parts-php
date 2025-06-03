<?php
class Product {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM shopdb.products WHERE quantity > 0");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function purchase($productId, $clientId, $quantity) {
        try {
            $this->conn->beginTransaction();

            // Check availability
            $stmt = $this->conn->prepare("SELECT * FROM shopdb.products WHERE id = ? FOR UPDATE");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product['quantity'] < $quantity) {
                throw new Exception("Not enough stock");
            }

            // Record purchase
            $total = $product['price'] * $quantity;
            $stmt = $this->conn->prepare("INSERT INTO shopdb.purchase_history (client_id, product_id, quantity, total_price) 
                                        VALUES (?, ?, ?, ?)");
            $stmt->execute([$clientId, $productId, $quantity, $total]);

            // Update stock
            $stmt = $this->conn->prepare("UPDATE shopdb.products SET quantity = quantity - ? WHERE id = ?");
            $stmt->execute([$quantity, $productId]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Purchase error: " . $e->getMessage());
            return false;
        }
    }
}
?>