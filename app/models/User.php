<?php
class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function authenticate($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM shopdb.clients WHERE userid = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function usernameExists($username) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM shopdb.clients WHERE userid = ?");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }

    public function create($username, $password, $clientData) {
        try {
            if ($this->usernameExists($username)) {
                return false;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO shopdb.clients (userid, first_name, last_name, email, password, phone, address) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $username,
                $clientData['first_name'],
                $clientData['last_name'],
                $clientData['email'],
                $hashedPassword,
                $clientData['phone'],
                $clientData['address']
            ]);

            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return false;
        }
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM shopdb.clients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>