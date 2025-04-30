<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
$db = new SQLite3('../database/recipes.db');
$db->enableExceptions(true);

// Create users table if it doesn't exist
$db->exec('CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL
)');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        // Get list of users (excluding passwords)
        $result = $db->query('SELECT id, username FROM users');
        $users = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $users[] = $row;
        }
        http_response_code(200);
        echo json_encode($users);
        break;

    case 'register':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password are required']);
            break;
        }

        try {
            $db->exec('BEGIN EXCLUSIVE TRANSACTION');

            // Check if username already exists
            $stmt = $db->prepare('SELECT COUNT(*) as count FROM users WHERE username = :username');
            $stmt->bindValue(':username', $data['username'], SQLITE3_TEXT);
            $result = $stmt->execute();
            $row = $result->fetchArray(SQLITE3_ASSOC);

            if ($row['count'] > 0) {
                $db->exec('ROLLBACK');
                http_response_code(409);
                echo json_encode(['error' => 'Username already exists']);
                break;
            }

            // Create new user
            $stmt = $db->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
            $stmt->bindValue(':username', $data['username'], SQLITE3_TEXT);
            $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT), SQLITE3_TEXT);
            $stmt->execute();

            $db->exec('COMMIT');
            http_response_code(201);
            echo json_encode(['message' => 'User registered!']);
        } catch (Exception $e) {
            $db->exec('ROLLBACK');
            http_response_code(409);
            echo json_encode(['error' => 'Username already exists']);
        }
        break;

    case 'login':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password are required']);
            break;
        }

        $stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->bindValue(':username', $data['username'], SQLITE3_TEXT);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);

        if ($user && password_verify($data['password'], $user['password'])) {
            http_response_code(201);
            echo json_encode(['message' => 'Login successful!']);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid username or password']);
        }
        break;

    case 'delete':
        if (!isset($_GET['username'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username is required']);
            break;
        }

        $stmt = $db->prepare('DELETE FROM users WHERE username = :username');
        $stmt->bindValue(':username', $_GET['username'], SQLITE3_TEXT);
        $stmt->execute();

        http_response_code(200);
        echo json_encode(['message' => 'User deleted']);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}

$db->close();
?>
