<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
<<<<<<< Updated upstream
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
=======
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
>>>>>>> Stashed changes
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

<<<<<<< Updated upstream
// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$action = $_GET['action'] ?? '';
$db = getDBConnection();

try {
    switch ($action) {
        case 'add':
            addMealPlan($db);
            break;
        case 'get':
            getMealPlans($db);
            break;
        case 'update':
            updateMealPlan($db);
            break;
        case 'delete':
            deleteMealPlan($db);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function addMealPlan($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['user_id']) || !isset($data['recipe_id']) || !isset($data['day']) || !isset($data['meal_type'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    $stmt = $db->prepare("INSERT INTO meal_plans (user_id, recipe_id, day, meal_type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data['user_id'], $data['recipe_id'], $data['day'], $data['meal_type']]);
    
    echo json_encode(['message' => 'Meal added successfully']);
}

function getMealPlans($db) {
    $user_id = $_GET['user_id'] ?? null;
    
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(['error' => 'User ID is required']);
        return;
    }

    $stmt = $db->prepare("
=======
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        addMealPlan();
        break;
    case 'get':
        getMealPlans();
        break;
    case 'update':
        updateMealPlan();
        break;
    case 'delete':
        deleteMealPlan();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

function addMealPlan() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['user_id']) || !isset($data['recipe_id']) || !isset($data['day']) || !isset($data['meal_type'])) {
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }
    
    $user_id = $data['user_id'];
    $recipe_id = $data['recipe_id'];
    $day = $data['day'];
    $meal_type = $data['meal_type'];
    
    $stmt = $conn->prepare("INSERT INTO meal_plans (user_id, recipe_id, day, meal_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $recipe_id, $day, $meal_type);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Meal plan added successfully']);
    } else {
        echo json_encode(['error' => 'Failed to add meal plan']);
    }
}

function getMealPlans() {
    global $conn;
    
    $user_id = $_GET['user_id'] ?? null;
    
    if (!$user_id) {
        echo json_encode(['error' => 'User ID is required']);
        return;
    }
    
    $stmt = $conn->prepare("
>>>>>>> Stashed changes
        SELECT mp.*, r.name as recipe_name, r.ingredients, r.instructions 
        FROM meal_plans mp 
        JOIN recipes r ON mp.recipe_id = r.id 
        WHERE mp.user_id = ?
<<<<<<< Updated upstream
    ");
    $stmt->execute([$user_id]);
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($meals);
}

function updateMealPlan($db) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id']) || !isset($data['day']) || !isset($data['meal_type'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    $stmt = $db->prepare("UPDATE meal_plans SET day = ?, meal_type = ? WHERE id = ?");
    $stmt->execute([$data['day'], $data['meal_type'], $data['id']]);
    
    echo json_encode(['message' => 'Meal plan updated successfully']);
}

function deleteMealPlan($db) {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Meal plan ID is required']);
        return;
    }

    $stmt = $db->prepare("DELETE FROM meal_plans WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['message' => 'Meal plan deleted successfully']);
=======
        ORDER BY FIELD(mp.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $meal_plans = [];
    while ($row = $result->fetch_assoc()) {
        $meal_plans[] = $row;
    }
    
    echo json_encode($meal_plans);
}

function updateMealPlan() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id']) || !isset($data['day']) || !isset($data['meal_type'])) {
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }
    
    $id = $data['id'];
    $day = $data['day'];
    $meal_type = $data['meal_type'];
    
    $stmt = $conn->prepare("UPDATE meal_plans SET day = ?, meal_type = ? WHERE id = ?");
    $stmt->bind_param("ssi", $day, $meal_type, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Meal plan updated successfully']);
    } else {
        echo json_encode(['error' => 'Failed to update meal plan']);
    }
}

function deleteMealPlan() {
    global $conn;
    
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        echo json_encode(['error' => 'Meal plan ID is required']);
        return;
    }
    
    $stmt = $conn->prepare("DELETE FROM meal_plans WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Meal plan deleted successfully']);
    } else {
        echo json_encode(['error' => 'Failed to delete meal plan']);
    }
>>>>>>> Stashed changes
}
?> 