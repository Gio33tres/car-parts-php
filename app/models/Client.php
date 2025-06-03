<?php
class Client {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getPurchaseHistory($clientId) {
        $stmt = $this->conn->prepare("
            SELECT ph.*, p.name as product_name, p.price as unit_price
            FROM shopdb.purchase_history ph
            JOIN shopdb.products p ON ph.product_id = p.id
            WHERE ph.client_id = ?
            ORDER BY ph.purchase_date DESC
        ");
        $stmt->execute([$clientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>