<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$text = $_POST['text'] ?? '';
if (!$text) {
    echo json_encode(['error' => 'No text provided']);
    exit;
}

$chunks = str_split($text, 200);
$audioData = '';

foreach ($chunks as $chunk) {
    $url = "https://translate.google.com/translate_tts?ie=UTF-8&q=" . urlencode($chunk) . "&tl=ar&client=tw-ob";
    $audioData .= file_get_contents($url);
   
}

// إرسال Base64 مباشرة
echo json_encode(['audio' => base64_encode($audioData)]);
