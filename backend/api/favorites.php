<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

if ($method === 'POST') {
    if (isset($data['user_id'], $data['recipe_id'])) {
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
        $stmt->execute([$data['user_id'], $data['recipe_id']]);
        echo json_encode(['message' => 'Recipe saved to favorites!']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing user_id or recipe_id']);
    }
}

elseif ($method === 'GET' && isset($_GET['user_id'])) {
    $stmt = $pdo->prepare("SELECT r.* FROM recipes r 
                           INNER JOIN favorites f ON r.id = f.recipe_id 
                           WHERE f.user_id = ?");
    $stmt->execute([$_GET['user_id']]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed or missing parameters']);
}
?>
