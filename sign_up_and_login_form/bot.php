<?php
// Replace with your bot token
$token = "7763712242:AAFGCV-kZpLnAcB9LHlLRqKsorT5Kp_e8og";

$update = json_decode(file_get_contents("php://input"), TRUE);
$message = $update["message"];
$chatId = $message["chat"]["id"];
$text = $message["text"];

// Function to send message
function sendMessage($chatId, $message, $token) {
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $post = [
        'chat_id' => $chatId,
        'text' => $message
    ];
    file_get_contents($url . "?" . http_build_query($post));
}

// Respond to messages
if ($text == "/start") {
    sendMessage($chatId, "👋 Welcome to ScamCheck Ethiopia! Send a Telegram link, username, or group name to check.", $token);
} else {
    // Later we'll connect this to a database
    sendMessage($chatId, "✅ Received! We will review: $text", $token);
}
?>
