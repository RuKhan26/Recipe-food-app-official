<?php
header('Content-Type: application/json');

$prompt = $_GET['prompt'] ?? '';
$response = "Based on your mood, how about a creamy mushroom pasta or a spicy chickpea curry?";

if (!empty($prompt)) {
    $suggestion = strtolower($prompt);
    if (strpos($suggestion, 'light') !== false) {
        $response = "Try a refreshing quinoa salad or grilled veggies!";
    } elseif (strpos($suggestion, 'comfort') !== false) {
        $response = "How about mac and cheese or a hearty stew?";
    }
}

echo json_encode(['ai_response' => $response]);
?>
