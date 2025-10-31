<?php
require_once __DIR__ . '/../config/config.php';

// Get controller and action from URL
$controller = $_GET['c'] ?? 'dashboard';
$action = $_GET['a'] ?? 'index';

// Route mapping
$routes = [
    'auth' => 'AuthController',
    'dashboard' => 'DashboardController',
    'room' => 'RoomController',
    'booking' => 'BookingController',
    'facility' => 'FacilityController'
];

// Validate controller
if (!array_key_exists($controller, $routes)) {
    $controller = 'dashboard';
}

$controllerClass = $routes[$controller];

try {
    // Instantiate controller and call action
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();
        
        if (method_exists($controllerInstance, $action)) {
            $controllerInstance->$action();
        } else {
            throw new Exception("Action $action not found in $controllerClass");
        }
    } else {
        throw new Exception("Controller $controllerClass not found");
    }
} catch (Exception $e) {
    // Error handling
    http_response_code(500);
    echo "<h1>Royal Error</h1>";
    echo "<p>Something went wrong in the kingdom: " . htmlspecialchars($e->getMessage()) . "</p>";
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
}