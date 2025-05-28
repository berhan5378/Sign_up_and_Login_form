<?php
// Replace with your bot token
$token = "7763712242:AAFGCV-kZpLnAcB9LHlLRqKsorT5Kp_e8og";

$update = json_decode(file_get_contents("php://input"), TRUE);
$message = $update["message"];
$chatId = $message["chat"]["id"];
$text = $message["text"];
$photoArray = $update['message']['photo'] ?? [];

// Function to send message
function sendMessage($chatId, $message, $token ,$photoArray = []) { 
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $keyboard = [
        "inline_keyboard" => [
            [
                ["text" => "Start", "callback_data" => "/start"],
                ["text" => "Report", "callback_data" => "/report"],
                ["text" => "Help", "callback_data" => "/help"], 
            ],
            [
                ["text" => "About", "url" => "https://t.me/CodingBerhan"],
                ["text" => "Contact", "callback_data" => "/contact"],
                ["text" => "Settings", "callback_data" => "/settings"]
            ]]
    ];
    $keyboards = [
        "keyboard" => [[
            ["text" => "📞 Share Phone Number", "request_contact" => true]
        ]],
        "resize_keyboard" => true,
        "one_time_keyboard" => true
    ];
    /*
    $keyboard = [
        "keyboard" => [
            [["text" => "/start"], ["text" => "/report"]]
        ],
        "resize_keyboard" => true,
        "one_time_keyboard" => true
    ];*/
    if (!empty($photoArray)) {
    // Get the highest quality photo (last in the array)
    $fileId = end($photoArray)['file_id'];
        $post = [
            'chat_id' => $chatId,
            'photo' => $fileId,
            'caption' => $message ?? '',
            'reply_markup' => json_encode($keyboard),
            'parse_mode' => 'HTML' // Optional: use HTML formatting
        ];

        file_get_contents("https://api.telegram.org/bot$token/sendPhoto?" . http_build_query($post));
        return;
    }
 
    $post = [
        'chat_id' => $chatId,
        'text' => $message,

        'reply_markup' => json_encode($keyboard),
        'parse_mode' => 'HTML' // Optional: use HTML formatting
    ];
    file_get_contents($url . "?" . http_build_query($post));
   // file_get_contents("https://api.telegram.org/bot7763712242:AAFGCV-kZpLnAcB9LHlLRqKsorT5Kp_e8og/sendMessage?chat_id=$chatId&text=" . urlencode($message));
}

// Respond to messages
if ($text == "/start") {
    sendMessage($chatId, "👋 Welcome to ScamCheck Ethiopia! Send a Telegram link, username, or group name to check.", $token,$photoArray);
} else {
    // Later we'll connect this to a database
    sendMessage($chatId, "✅ Received! We will review: $text", $token,$photoArray);
}
// Notify the admin if a user sends a message
if (isset($update["message"])) {
    $fromUser = $update["message"]["from"]["username"] ?? 'Unknown';
    $text = $update["message"]["text"] ?? '';
    $phoneNumber = $update['message']['contact']['phone_number'] ?? null;

    if ($phoneNumber) {
        $text = "User @$fromUser shared their phone number: $phoneNumber - $text";
    }
    if($photoArray) {
        $text = "User @$fromUser sent a photo with caption: " . ($update['message']['caption'] ?? '');
    }
    // Notify the admin
    $adminChatId = "1357507412";
    $msg = "User @$fromUser sent: $text";

    // forward msg to group
    $groupChatId = "-4971362312"; // Replace with your group chat ID
    file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$groupChatId&text=" . urlencode($text));
    // Notify the admin

    file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$adminChatId&text=" . urlencode($msg));
}

// Get the update from Telegram
$update = json_decode(file_get_contents('php://input'), true);

// Check if it's a callback query
if (isset($update['callback_query'])) {
    $callback = $update['callback_query'];
    $chatId = $callback['message']['chat']['id'];
    $messageId = $callback['message']['message_id'];
    $data = $callback['data'];
    
    // Answer the callback (removes the "loading" animation)
    answerCallbackQuery($callback['id'], $token);
    
    // Handle different button actions
    switch ($data) {
        case '/start':
            // Do something for action1
            sendMessage($chatId, "You clicked start!", $token);
            break;
        case '/report':
            // Do something for action2
            sendMessage($chatId, "report!", $token);
            break;
        // Add more cases as needed
    }
}

function answerCallbackQuery($callbackId, $token) { 
    $url = "https://api.telegram.org/bot{$token}/answerCallbackQuery";
    
    $data = [
        'callback_query_id' => $callbackId
    ];
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

 
