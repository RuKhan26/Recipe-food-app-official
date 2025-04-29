<?php
require_once '../config/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents("php://input"), true);

if ($method === 'POST' && $action === 'register') {
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $success = $stmt->execute([$data['username'], password_hash($data['password'], PASSWORD_DEFAULT)]);
    echo json_encode(['message' => $success ? 'User registered!' : 'Registration failed.']);
}

elseif ($method === 'POST' && $action === 'login') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$data['username']]);
    $user = $stmt->fetch();
    if ($user && password_verify($data['password'], $user['password'])) {
        echo json_encode(['message' => 'Login successful!']);
    } else {
        echo json_encode(['message' => 'Login failed.']);
    }
}

elseif ($method === 'GET') {
    $stmt = $pdo->query("SELECT id, username FROM users");
    echo json_encode($stmt->fetchAll());
}
?>
