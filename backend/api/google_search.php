<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
    $query = $_GET['query'] ?? '';

    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    if ($method === 'GET' && !empty($query)) {
        $client = new Client();
        
        // Google Custom Search API endpoint
        $url = 'https://www.googleapis.com/customsearch/v1';
        
        // Make the request to Google Custom Search API
        $response = $client->get($url, [
            'query' => [
                'key' => $_ENV['GOOGLE_API_KEY'],
                'cx' => $_ENV['GOOGLE_CSE_ID'],
                'q' => $query . ' recipe',
                'num' => 10 // Number of results to return
            ]
        ]);

        $results = json_decode($response->getBody(), true);
        
        // Format the results
        $formattedResults = [];
        if (isset($results['items'])) {
            foreach ($results['items'] as $item) {
                $formattedResults[] = [
                    'title' => $item['title'],
                    'link' => $item['link'],
                    'snippet' => $item['snippet'],
                    'thumbnail' => $item['pagemap']['cse_thumbnail'][0]['src'] ?? null
                ];
            }
        }

        echo json_encode($formattedResults);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request method or missing query parameter']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error searching recipes: ' . $e->getMessage()]);
}
?> 