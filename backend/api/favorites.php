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
        if (!isset($data['recipe_id'])) {
            throw new Exception('Recipe ID is required');
        }

        // Create favorites table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS favorites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            recipe_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (recipe_id) REFERENCES recipes(id)
        )");

        // Get the current user from the session or request
        $user_id = $data['user_id'] ?? 1; // For now, using a default user_id

        // Check if recipe exists
        $stmt = $pdo->prepare("SELECT id FROM recipes WHERE id = ?");
        $stmt->execute([$data['recipe_id']]);
        if (!$stmt->fetch()) {
            throw new Exception('Recipe not found');
        }

        // Check if already favorited
        $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $stmt->execute([$user_id, $data['recipe_id']]);
        if ($stmt->fetch()) {
            echo json_encode(['message' => 'Recipe already in favorites']);
            exit;
        }

        // Add to favorites
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $data['recipe_id']]);
        
        echo json_encode(['message' => 'Recipe added to favorites']);
    }
    elseif ($method === 'GET') {
        // Get the current user from the session or request
        $user_id = $_GET['user_id'] ?? 1; // For now, using a default user_id

        // Get all favorite recipes for the user
        $stmt = $pdo->prepare("
            SELECT r.* 
            FROM recipes r 
            JOIN favorites f ON r.id = f.recipe_id 
            WHERE f.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $favorites = $stmt->fetchAll();
        
        echo json_encode($favorites);
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
