<?php
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

if ($method === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO journal_entries (title, content) VALUES (?, ?)");
    $stmt->execute([$data['title'], $data['content']]);
    echo json_encode(['message' => 'Entry saved!']);
} elseif ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM journal_entries ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll());
}
?>
