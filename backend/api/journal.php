<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db.php';

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $data = json_decode(file_get_contents("php://input"), true);

    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    if ($method === 'POST') {
        if (!isset($data['title']) || !isset($data['content'])) {
            throw new Exception('Title and content are required');
        }

        $stmt = $pdo->prepare("INSERT INTO journal_entries (title, content) VALUES (?, ?)");
        $stmt->execute([$data['title'], $data['content']]);
        echo json_encode(['message' => 'Entry saved!']);
    } 
    elseif ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM journal_entries ORDER BY created_at DESC");
        $entries = $stmt->fetchAll();
        echo json_encode($entries);
    }
    else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
