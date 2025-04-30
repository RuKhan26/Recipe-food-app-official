<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
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

switch ($action) {
    case 'ai_search':
        aiSearch();
        break;
    case 'random':
        getRandomRecipes();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
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
