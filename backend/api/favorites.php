<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

$action = $_GET['action'] ?? '';

try {
    $db = getDBConnection();
    $method = $_SERVER['REQUEST_METHOD'];
    $data = json_decode(file_get_contents("php://input"), true);

    if ($method === 'POST') {
        if (!isset($data['recipe_id'])) {
            throw new Exception('Recipe ID is required');
        }

        // Create favorites table if it doesn't exist
        $db->exec("CREATE TABLE IF NOT EXISTS favorites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            recipe_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (recipe_id) REFERENCES recipes(id)
        )");

        // Get the current user from the session or request
        $user_id = $data['user_id'] ?? null;
        if (!$user_id) {
            throw new Exception('User ID is required');
        }

        // Check if recipe exists
        $stmt = $db->prepare("SELECT id FROM recipes WHERE id = ?");
        $stmt->execute([$data['recipe_id']]);
        if (!$stmt->fetch()) {
            throw new Exception('Recipe not found');
        }

        // Check if already favorited
        $stmt = $db->prepare("SELECT id FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $stmt->execute([$user_id, $data['recipe_id']]);
        if ($stmt->fetch()) {
            echo json_encode(['message' => 'Recipe already in favorites']);
            exit;
        }

        // Add to favorites
        $stmt = $db->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $data['recipe_id']]);
        
        echo json_encode(['message' => 'Recipe added to favorites']);
    }
    elseif ($method === 'GET') {
        // Get the current user from the session or request
        $user_id = $_GET['user_id'] ?? null;
        if (!$user_id) {
            throw new Exception('User ID is required');
        }

        // Get all favorite recipes for the user
        $stmt = $db->prepare("
            SELECT r.id as recipe_id, r.name as recipe_name, r.ingredients, r.instructions, f.id as favorite_id
            FROM recipes r 
            JOIN favorites f ON r.id = f.recipe_id 
            WHERE f.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($favorites);
    }
    elseif ($method === 'DELETE') {
        if (!isset($data['favorite_id'])) {
            throw new Exception('Favorite ID is required');
        }

        // Get the current user from the session or request
        $user_id = $data['user_id'] ?? null;
        if (!$user_id) {
            throw new Exception('User ID is required');
        }

        // Delete the favorite
        $stmt = $db->prepare("DELETE FROM favorites WHERE id = ? AND user_id = ?");
        $stmt->execute([$data['favorite_id'], $user_id]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('Favorite not found or not owned by user');
        }
        
        echo json_encode(['message' => 'Favorite removed successfully']);
    }
    else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    error_log("Favorites API Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
