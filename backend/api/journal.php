<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDBConnection();
    $method = $_SERVER['REQUEST_METHOD'];
    $data = json_decode(file_get_contents("php://input"), true);

    if ($method === 'POST') {
        if (!isset($data['title']) || !isset($data['content'])) {
            throw new Exception('Title and content are required');
        }

        // Create journal_entries table if it doesn't exist
        $db->exec("CREATE TABLE IF NOT EXISTS journal_entries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(100) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Get the current user from the session or request
        $user_id = $data['user_id'] ?? 1; // For now, using a default user_id

        // Insert the journal entry
        $stmt = $db->prepare("INSERT INTO journal_entries (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $data['title'], $data['content']]);
        
        echo json_encode(['message' => 'Journal entry saved successfully']);
    }
    elseif ($method === 'GET') {
        // Get the current user from the session or request
        $user_id = $_GET['user_id'] ?? 1; // For now, using a default user_id

        // Get all journal entries for the user
        $stmt = $db->prepare("SELECT * FROM journal_entries WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
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
