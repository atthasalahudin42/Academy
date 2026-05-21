<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use GuzzleHttp\Client;

// === CONFIG ===
$apiKey = "AIzaSyDJWCYZpaI5XeaRwb_ZfXew5UKgvIYrIQo";
$model = "gemini-2.5-flash";
$userMessage = "Hello AI, what language we are using right now.";
$showDebug = true; // set false to hide full API response

$client = new Client([
    'base_uri' => 'https://generativelanguage.googleapis.com/v1beta/',
    'timeout' => 10
]);

try {
    $response = $client->post("models/{$model}:generateContent?key={$apiKey}", [
        'json' => [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [["text" => $userMessage]]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.7,
                "topK" => 40,
                "topP" => 0.95,
                "maxOutputTokens" => 200
            ]
        ]
    ]);

    $body = $response->getBody()->getContents();
    $data = json_decode($body, true);

    $aiResponse = "No response received";
    if (isset($data["candidates"][0]["content"]["parts"][0]["text"])) {
        $aiResponse = $data["candidates"][0]["content"]["parts"][0]["text"];
    }

    echo "<h2 style='color:green;'>✅ Success! Gemini Response:</h2>";
    echo "<div style='background: #f0f8ff; padding: 20px; border-radius: 12px; font-family: monospace; white-space: pre-wrap; border-left: 4px solid #4285f4;'>";
    echo htmlspecialchars($aiResponse);
    echo "</div>";

    if ($showDebug) {
        echo "<h3>📋 Full API Response (debug):</h3>";
        echo "<details style='margin-top: 10px;'><summary>Click to expand</summary>";
        echo "<pre style='background: #f5f5f5; padding: 15px; overflow: auto;'>" . htmlspecialchars($body) . "</pre>";
        echo "</details>";
    }

} catch (\GuzzleHttp\Exception\ClientException $e) {
    $response = $e->getResponse();
    $errorBody = $response ? $response->getBody()->getContents() : "No response body";
    echo "<h3 style='color:red;'>❌ Client error ({$e->getCode()}):</h3>";
    echo "<pre style='background: #ffebee; padding: 15px; border-radius: 8px; border-left: 4px solid #ea4335;'>" . htmlspecialchars($errorBody) . "</pre>";
} catch (\Exception $e) {
    echo "<h3 style='color:red;'>❌ General error:</h3>";
    echo "<pre style='background: #ffebee; padding: 15px; border-radius: 8px; border-left: 4px solid #ea4335;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
}

echo "<hr>";
echo "<p><strong>Tip:</strong> Change <code>\$userMessage</code> to any text you want and re-run.</p>";
?>