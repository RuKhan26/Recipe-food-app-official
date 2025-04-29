<?php
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

if ($method === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
    $stmt->execute([$data['user_id'], $data['recipe_id']]);
    echo json_encode(['message' => 'Recipe saved to favorites!']);
}

elseif ($method === 'GET' && isset($_GET['user_id'])) {
    $stmt = $pdo->prepare("SELECT r.* FROM recipes r INNER JOIN favorites f ON r.id = f.recipe_id WHERE f.user_id = ?");
    $stmt->execute([$_GET['user_id']]);
    echo json_encode($stmt->fetchAll());
}
?>
