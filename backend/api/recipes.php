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
require_once '../vendor/autoload.php';

// Load environment variables from .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

use GuzzleHttp\Client;

$action = $_GET['action'] ?? '';

try {
    $db = getDBConnection();
    $method = $_SERVER['REQUEST_METHOD'];
    $data = json_decode(file_get_contents("php://input"), true);

    if ($method === 'POST') {
        if (!isset($data['name']) || !isset($data['ingredients']) || !isset($data['instructions'])) {
            throw new Exception('Name, ingredients, and instructions are required');
        }

        // Check if recipes table exists and has user_id column
        $tableExists = $db->query("SHOW TABLES LIKE 'recipes'")->rowCount() > 0;
        if (!$tableExists) {
            // Create recipes table if it doesn't exist
            $db->exec("CREATE TABLE recipes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                name VARCHAR(100) NOT NULL,
                ingredients TEXT NOT NULL,
                instructions TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
        } else {
            // Check if user_id column exists
            $columns = $db->query("SHOW COLUMNS FROM recipes")->fetchAll(PDO::FETCH_COLUMN);
            if (!in_array('user_id', $columns)) {
                // Add user_id column if it doesn't exist
                $db->exec("ALTER TABLE recipes ADD COLUMN user_id INT NOT NULL AFTER id");
            }
        }

        // Get the current user from the session or request
        $user_id = $data['user_id'] ?? 1; // For now, using a default user_id

        // Insert the recipe
        $stmt = $db->prepare("INSERT INTO recipes (user_id, name, ingredients, instructions) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $data['name'], $data['ingredients'], $data['instructions']]);
        
        $recipe_id = $db->lastInsertId();
        echo json_encode(['id' => $recipe_id, 'message' => 'Recipe created successfully']);
    }
    else {
        switch ($action) {
            case 'ai_search':
                aiSearch();
                break;
            case 'random':
                // Get a random recipe
                $stmt = $db->query("SELECT * FROM recipes ORDER BY RAND() LIMIT 1");
                $recipe = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($recipe) {
                    echo json_encode($recipe);
                } else {
                    throw new Exception('No recipes found');
                }
                break;
            case 'get':
                // Get the current user from the session or request
                $user_id = $_GET['user_id'] ?? 1; // For now, using a default user_id

                // Get all recipes for the user
                $stmt = $db->prepare("SELECT * FROM recipes WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode($recipes);
                break;
            case 'search':
                $query = $_GET['query'] ?? '';
                if (empty($query)) {
                    throw new Exception('Search query is required');
                }

                $stmt = $db->prepare("SELECT * FROM recipes WHERE name LIKE ? OR ingredients LIKE ?");
                $searchTerm = "%$query%";
                $stmt->execute([$searchTerm, $searchTerm]);
                $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode($recipes);
                break;
            default:
                throw new Exception('Invalid action');
        }
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

function aiSearch() {
    $ingredients = $_GET['ingredients'] ?? '';
    
    if (empty($ingredients)) {
        echo json_encode(['error' => 'Ingredients are required']);
        return;
    }
    
    $api_key = getenv('OPENAI_API_KEY');
    if (!$api_key) {
        echo json_encode(['error' => 'OpenAI API key not configured']);
        return;
    }
    
    $client = new Client();
    
    try {
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful cooking assistant. Generate recipes based on the given ingredients. Return the response as a JSON array of recipes, where each recipe has a name, ingredients (array), and instructions (array).'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Generate recipes using these ingredients: $ingredients"
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000
            ]
        ]);
        
        $result = json_decode($response->getBody(), true);
        $recipes = json_decode($result['choices'][0]['message']['content'], true);
        
        if (!is_array($recipes)) {
            throw new Exception('Invalid response format from AI');
        }
        
        echo json_encode($recipes);
    } catch (Exception $e) {
        echo json_encode(['error' => 'OpenAI API error: ' . $e->getMessage()]);
    }
}

function getRandomRecipes() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM recipes ORDER BY RAND() LIMIT 10");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $recipes = [];
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
    
    echo json_encode($recipes);
}
?>
