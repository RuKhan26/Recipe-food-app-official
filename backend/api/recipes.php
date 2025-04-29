<?php
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$ingredients = $_GET['ingredients'] ?? '';

if ($method === 'GET' && $action === 'random') {
    $stmt = $pdo->query("SELECT * FROM recipes ORDER BY RAND() LIMIT 1");
    echo json_encode($stmt->fetch());
} elseif ($method === 'GET' && !empty($ingredients)) {
    $terms = explode(',', $ingredients);
    $placeholders = implode('%', array_map('trim', $terms));
    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE ingredients LIKE ?");
    $stmt->execute(["%$placeholders%"]);
    echo json_encode($stmt->fetchAll());
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
