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
                $error = "Credenciales incorrectas. Por favor, intente de nuevo.";
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

            if (empty($username) || empty($password) || empty($clientData['first_name']) || 
                empty($clientData['last_name']) || empty($clientData['email'])) {
                $error = "Todos los campos requeridos deben ser llenados.";
                include __DIR__ . '/../views/auth/register.php';
                return;
            }
            
            if (!filter_var($clientData['email'], FILTER_VALIDATE_EMAIL)) {
                $error = "Ingrese una dirección de correo válida.";
                include __DIR__ . '/../views/auth/register.php';
                return;
            }

            if ($this->userModel->usernameExists($username)) {
                $error = "Nombre de usuario existente, intente con otro.";
                include __DIR__ . '/../views/auth/register.php';
                return;
            }

            $userId = $this->userModel->create($username, $password, $clientData);
            
            if ($userId) {
                session_start();
                $_SESSION['message'] = "Registrado con éxisto! Inicie sesión.";
                header('Location: /login');
                exit;
            } else {
                $error = "Error al registrar.";
                include __DIR__ . '/../views/auth/register.php';
            }
        } else {
            include __DIR__ . '/../views/auth/register.php';
        }
    }
}
?>