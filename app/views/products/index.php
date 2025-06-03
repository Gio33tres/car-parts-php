<?php
require_once __DIR__ . '../../../config/database.php';
require_once __DIR__ . '../../../config/routes.php';

session_start();

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

$found = false;
foreach ($routes as $route => $handler) {
    if ($path === $route) {
        $found = true;
        list($controllerName, $method) = explode('@', $handler);
        require_once __DIR__ . "/../app/controllers/$controllerName.php";
        
        $db = new Database();
        $conn = $db->getConnection();
        
        $controller = new $controllerName($conn);
        $controller->$method();
        break;
    }
}

if (!$found) {
    http_response_code(404);
    include __DIR__ . '/../app/views/errors/404.php';
}
?>