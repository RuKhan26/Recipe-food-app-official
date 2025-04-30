<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/api/recipes.php';
require_once __DIR__ . '/api/users.php';
require_once __DIR__ . '/api/favorites.php';
require_once __DIR__ . '/api/journal.php';
require_once __DIR__ . '/api/meal_plans.php';

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Basic routing
switch (true) {
    case $request === '/':
        echo json_encode(['status' => 'success', 'message' => 'Recipe App API is running']);
        break;
    case strpos($request, '/api/') === 0:
        // Let the individual API files handle their own routing
        $apiFile = __DIR__ . str_replace('/api/', '/api/', $request);
        if (file_exists($apiFile)) {
            require_once $apiFile;
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'API endpoint not found']);
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Endpoint not found']);
        break;
}
?> 