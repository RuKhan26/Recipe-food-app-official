<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
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
        // Get the current user from the session or request
        $user_id = $data['user_id'] ?? null;
        if (!$user_id) {
            throw new Exception('User ID is required');
        }

        // Handle journal entries
        if (isset($data['source']) && $data['source'] === 'journal') {
            // First, insert the recipe into the recipes table
            $stmt = $db->prepare("
                INSERT INTO recipes (name, ingredients, instructions) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $data['name'],
                $data['ingredients'],
                $data['instructions']
            ]);
            $recipe_id = $db->lastInsertId();

            // Then add to favorites
            $stmt = $db->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $recipe_id]);
            
            echo json_encode(['message' => 'Journal entry added to favorites']);
            exit;
        }

        // Handle regular recipes
        if (!isset($data['recipe_id'])) {
            throw new Exception('Recipe ID is required');
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
            SELECT r.*, f.id as favorite_id
            FROM recipes r 
            JOIN favorites f ON r.id = f.recipe_id 
            WHERE f.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($favorites);
    }
    elseif ($method === 'DELETE') {
        $user_id = $data['user_id'] ?? null;
        $favorite_id = $data['favorite_id'] ?? null;

        if (!$user_id || !$favorite_id) {
            throw new Exception('User ID and Favorite ID are required');
        }

        // Verify the favorite belongs to the user
        $stmt = $db->prepare("SELECT id FROM favorites WHERE id = ? AND user_id = ?");
        $stmt->execute([$favorite_id, $user_id]);
        if (!$stmt->fetch()) {
            throw new Exception('Favorite not found or unauthorized');
        }

        // Delete the favorite
        $stmt = $db->prepare("DELETE FROM favorites WHERE id = ? AND user_id = ?");
        $stmt->execute([$favorite_id, $user_id]);
        
        echo json_encode(['message' => 'Recipe removed from favorites']);
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
