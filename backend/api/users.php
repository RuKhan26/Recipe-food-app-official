<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';

function validateUsername($username) {
    // Check length
    if (strlen($username) < 3 || strlen($username) > 20) {
        return ['valid' => false, 'error' => 'Username must be between 3 and 20 characters'];
    }
    
    // Check for valid characters
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return ['valid' => false, 'error' => 'Username can only contain letters, numbers, and underscores'];
    }
    
    return ['valid' => true];
}

// Get action from either query parameter or URL path
$action = $_GET['action'] ?? '';
if (empty($action)) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $pathParts = explode('/', trim($path, '/'));
    $action = end($pathParts);
}

// Remove .php from action if present
$action = str_replace('.php', '', $action);

try {
    $db = getDBConnection();

    switch ($action) {
        case 'list':
            // Get list of users (excluding passwords)
            $stmt = $db->query('SELECT id, username FROM users');
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode($users);
            break;

        case 'register':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['username']) || !isset($data['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Username and password are required']);
                exit;
            }

            // Validate username
            $validation = validateUsername($data['username']);
            if (!$validation['valid']) {
                http_response_code(400);
                echo json_encode(['error' => $validation['error']]);
                exit;
            }

            // Check if username already exists
            $stmt = $db->prepare('SELECT COUNT(*) as count FROM users WHERE username = ?');
            $stmt->execute([$data['username']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['count'] > 0) {
                http_response_code(409);
                echo json_encode(['error' => 'Username already exists']);
                break;
            }

            // Hash the password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Create new user
            $stmt = $db->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            $stmt->execute([$data['username'], $hashedPassword]);
            
            http_response_code(201);
            echo json_encode(['message' => 'User registered successfully']);
            break;

        case 'login':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['username']) || !isset($data['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Username and password are required']);
                exit;
            }

            // Get user by username
            $stmt = $db->prepare('SELECT id, username, password FROM users WHERE username = ?');
            $stmt->execute([$data['username']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($data['password'], $user['password'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid username or password']);
                break;
            }

            // Remove password from response
            unset($user['password']);
            
            http_response_code(200);
            echo json_encode([
                'message' => 'Login successful!',
                'user_id' => $user['id'],
                'username' => $user['username']
            ]);
            break;

        case 'delete':
            if (!isset($_GET['username'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Username is required']);
                break;
            }

            $stmt = $db->prepare('DELETE FROM users WHERE username = ?');
            $stmt->execute([$_GET['username']]);

            http_response_code(200);
            echo json_encode(['message' => 'User deleted']);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
