<?php
class AuthController {
    private $conn;
    private $userModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->userModel = new User($conn);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            $user = $this->userModel->authenticate($username, $password);
            
            if ($user) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['userid'];
                header('Location: /client/dashboard');
                exit;
            } else {
                $error = "Invalid credentials";
                include __DIR__ . '/../views/auth/login.php';
            }
        } else {
            include __DIR__ . '/../views/auth/login.php';
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /login');
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $clientData = [
                'first_name' => trim($_POST['first_name']),
                'last_name' => trim($_POST['last_name']),
                'email' => trim($_POST['email']),
                'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : null,
                'address' => isset($_POST['address']) ? trim($_POST['address']) : null
            ];

            // Validate required fields
            if (empty($username) || empty($password) || empty($clientData['first_name']) || 
                empty($clientData['last_name']) || empty($clientData['email'])) {
                $error = "All required fields must be filled out.";
                include __DIR__ . '/../views/auth/register.php';
                return;
            }

            // Validate email format
            if (!filter_var($clientData['email'], FILTER_VALIDATE_EMAIL)) {
                $error = "Please enter a valid email address.";
                include __DIR__ . '/../views/auth/register.php';
                return;
            }

            // Check if username exists
            if ($this->userModel->usernameExists($username)) {
                $error = "Username already exists. Please choose a different username.";
                include __DIR__ . '/../views/auth/register.php';
                return;
            }

            $userId = $this->userModel->create($username, $password, $clientData);
            
            if ($userId) {
                session_start();
                $_SESSION['message'] = "Registration successful! Please login.";
                header('Location: /login');
                exit;
            } else {
                $error = "Registration failed. Please try again later.";
                include __DIR__ . '/../views/auth/register.php';
            }
        } else {
            include __DIR__ . '/../views/auth/register.php';
        }
    }
}
?>