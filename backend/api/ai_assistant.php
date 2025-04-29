<?php
// Load the environment variables from the .env file
require_once '../vendor/autoload.php'; // Ensure that autoload.php is included for phpdotenv

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get the OpenAI API key from the environment variable
$openai_api_key = getenv(name: 'OPENAI_API_KEY');

// Check if the API key is available
if (!$openai_api_key) {
    echo json_encode(['ai_response' => 'API key is missing!']);
    exit();
}

header('Content-Type: application/json');

// Get the user's prompt
$prompt = $_GET['prompt'] ?? '';

// Fallback AI response if no prompt is provided
if (empty($prompt)) {
    echo json_encode(['ai_response' => 'Please provide a prompt to ask the AI!']);
    exit();
}

// Initialize Guzzle client
$client = new \GuzzleHttp\Client();

try {
    // Make the API request to OpenAI's GPT model
    $response = $client->post('https://api.openai.com/v1/completions', [
        'json' => [
            'model' => 'gpt-4', // You can use 'gpt-3.5-turbo' as an alternative
            'prompt' => $prompt,
            'max_tokens' => 150, // Limit response length
            'temperature' => 0.7, // Control randomness
        ],
        'headers' => [
            'Authorization' => 'Bearer ' . $openai_api_key,
        ],
    ]);

    // Decode the response from OpenAI
    $body = $response->getBody();
    $data = json_decode($body, true);

    // Extract the AI-generated response
    $ai_response = $data['choices'][0]['text'] ?? 'Sorry, I couldn’t get a response from the AI.';

    // Return the AI response as JSON
    echo json_encode(['ai_response' => trim($ai_response)]);
} catch (\GuzzleHttp\Exception\RequestException $e) {
    echo json_encode(['ai_response' => 'Error: ' . $e->getMessage()]);
}
?>
