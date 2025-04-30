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

try {
    $db = getDBConnection();
    $method = $_SERVER['REQUEST_METHOD'];
    $data = json_decode(file_get_contents("php://input"), true);

    // Create the meal_plans table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS meal_plans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        recipe_id INT NOT NULL,
        recipe_name VARCHAR(255) NOT NULL,
        day VARCHAR(10) NOT NULL,
        meal_type VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    if ($method === 'POST') {
        if (!isset($data['user_id']) || !isset($data['recipe_id']) || !isset($data['recipe_name']) || !isset($data['day']) || !isset($data['meal_type'])) {
            throw new Exception('All fields are required');
        }

        // Insert the meal plan
        $stmt = $db->prepare("INSERT INTO meal_plans (user_id, recipe_id, recipe_name, day, meal_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['user_id'],
            $data['recipe_id'],
            $data['recipe_name'],
            $data['day'],
            $data['meal_type']
        ]);
        
        $mealId = $db->lastInsertId();
        echo json_encode([
            'message' => 'Meal added to plan successfully',
            'id' => $mealId
        ]);
    }
    elseif ($method === 'GET') {
        if (!isset($_GET['user_id'])) {
            throw new Exception('User ID is required');
        }

        // Get all meal plans for the user
        $stmt = $db->prepare("SELECT * FROM meal_plans WHERE user_id = ? ORDER BY day, meal_type");
        $stmt->execute([$_GET['user_id']]);
        $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($meals);
    }
    elseif ($method === 'DELETE') {
        if (!isset($_GET['id']) || !isset($_GET['user_id'])) {
            throw new Exception('Meal plan ID and user ID are required');
        }

        // Delete only if the meal belongs to the user
        $stmt = $db->prepare("DELETE FROM meal_plans WHERE id = ? AND user_id = ?");
        $stmt->execute([$_GET['id'], $_GET['user_id']]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('Meal plan not found or not authorized');
        }
        
        echo json_encode(['message' => 'Meal removed from plan successfully']);
    }
    else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 