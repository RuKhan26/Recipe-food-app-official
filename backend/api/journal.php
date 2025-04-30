<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $data = json_decode(file_get_contents("php://input"), true);

    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    $db = getDBConnection();

    if ($method === 'POST') {
        if (!isset($data['title']) || !isset($data['content'])) {
            throw new Exception('Title and content are required');
        }

        $user_id = $data['user_id'] ?? null;
        if (!$user_id) {
            throw new Exception('User ID is required');
        }

        $stmt = $db->prepare("INSERT INTO journal (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $data['title'], $data['content']]);
        echo json_encode(['message' => 'Entry saved!']);
    } 
    elseif ($method === 'GET') {
        $user_id = $_GET['user_id'] ?? null;
        if (!$user_id) {
            throw new Exception('User ID is required');
        }

        // First, check if created_at column exists
        $stmt = $db->query("SHOW COLUMNS FROM journal LIKE 'created_at'");
        $columnExists = $stmt->fetch();

        if ($columnExists) {
            $stmt = $db->prepare("SELECT * FROM journal WHERE user_id = ? ORDER BY created_at DESC");
        } else {
            $stmt = $db->prepare("SELECT * FROM journal WHERE user_id = ? ORDER BY id DESC");
        }
        
        $stmt->execute([$user_id]);
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
