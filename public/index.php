<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/routes.php';

// Autoload models
spl_autoload_register(function ($class) {
    $modelFile = __DIR__ . "/../app/models/$class.php";
    if (file_exists($modelFile)) {
        require_once $modelFile;
    }
});

session_start();

// Get the requested path
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Remove the script name from request URI
$path = str_replace(dirname($script_name), '', $request_uri);
$path = parse_url($path, PHP_URL_PATH);

// Remove any trailing slashes
$path = rtrim($path, '/');

// Default to home if empty
if (empty($path)) {
    $path = '/';
}

// Route handling
if (array_key_exists($path, $routes)) {
    list($controllerName, $method) = explode('@', $routes[$path]);
    $controllerFile = __DIR__ . "/../app/controllers/$controllerName.php";
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        $db = new Database();
        $conn = $db->getConnection();
        
        $controller = new $controllerName($conn);
        $controller->$method();
    } else {
        http_response_code(404);
        echo "Controller file not found: $controllerFile";
    }
} else {
    http_response_code(404);
    echo "Page not found: $path";
}