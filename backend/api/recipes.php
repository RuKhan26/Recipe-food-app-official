<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db.php';
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

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_GET['action'] ?? '';
    $ingredients = $_GET['ingredients'] ?? '';

    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    if ($action === 'ai_search') {
        try {
            if (empty($ingredients)) {
                throw new Exception('Ingredients are required');
            }

            $client = new Client();
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_ENV['OPENAI_API_KEY'],
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a helpful cooking assistant. Generate recipes in JSON format with name, ingredients, and instructions fields.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Generate 3 recipe suggestions using these ingredients: $ingredients. Return the response as a JSON array where each recipe is an object with 'name', 'ingredients' (as array), and 'instructions' (as array) fields."
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 800,
                    'response_format' => ['type' => 'json_object']
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            
            if (isset($result['choices'][0]['message']['content'])) {
                $recipes = json_decode($result['choices'][0]['message']['content'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($recipes)) {
                    http_response_code(200);
                    echo json_encode($recipes);
                } else {
                    throw new Exception('Invalid response format from AI');
                }
            } else {
                throw new Exception('No recipes found');
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error finding recipes: ' . $e->getMessage()]);
        }
    } elseif ($method === 'GET' && $action === 'random') {
        // Get a random recipe
        $stmt = $pdo->query("SELECT * FROM recipes ORDER BY RAND() LIMIT 1");
        $recipe = $stmt->fetch();
        
        if ($recipe) {
            echo json_encode($recipe);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'No recipes found in the database']);
        }
    } elseif ($method === 'GET' && !empty($ingredients)) {
        $terms = explode(',', $ingredients);
        $placeholders = implode('%', array_map('trim', $terms));
        $stmt = $pdo->prepare("SELECT * FROM recipes WHERE ingredients LIKE ?");
        $stmt->execute(["%$placeholders%"]);
        $recipes = $stmt->fetchAll();
        
        if (!empty($recipes)) {
            echo json_encode($recipes);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'No recipes found with those ingredients']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request method or missing parameters']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
