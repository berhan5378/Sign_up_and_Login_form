<?php  
$token = getenv('BOT_TOKEN');  
$responsed = json_decode(file_get_contents("php://input"), TRUE); 
$channelId = getenv('CHANNEL_ID'); 
$adminChatId = getenv('ADMIN_CHAT_ID');
$reportChatId = getenv('REPORT_CHAT_ID');
$message = $responsed["message"] ?? null;
$callback = $responsed["callback_query"] ?? null;
$messageId = $message["message_id"] ?? null; 
$keyboard_default = [
    "inline_keyboard" => [ 
        [
            ["text" => "5K Vent", "url" => "https://t.me/aau5k_vent"],
            ["text" => "Help", "callback_data" => "/help"]
        ]]
];

$keyboard_for_channel = [
    "inline_keyboard" => [ 
        [
            ["text" => "✍️ Write a vent", "url" => "https://t.me/aau_5k_vent_bot?start=writeMsg"],
            ["text" => "⚠️ Report", "url" => "https://t.me/aau_5k_vent_bot?start=report_$messageId"]
        ] 
        ]
];

$keyboard_for_reply = [
    'force_reply' => true,
    'selective' => true
];

if($message) { 
    $fromUser = $message["from"]["username"] ?? 'Unknown';
    $chatId = $message["chat"]["id"] ?? null;
    $chatType = $message["chat"]["type"] ?? 'private';
    $text = $message["text"] ?? '';
    $reply= $message["reply_to_message"] ?? null;

    if($reply) { 
        $ReplyToMsg = $reply['text'] ?? '';
    
        if (str_contains($ReplyToMsg, "Report for vent")){
            // Notify the admin about the report
            $msgforAdmin = <<<TEXT
            📢 *New Report from @$fromUser*
            Report: $text
            Vent msg : $ReplyToMsg
            TEXT;
            sendMessage($reportChatId, $msgforAdmin, $token, $keyboard_default);
                    
            $msg = "✅ Thank you! Your report has been received.";
            sendMessage($chatId, $msg, $token, $keyboard_default);
            
        }
    }

    elseif ($text) {

        if ($text == "/start") {
            $msg = <<<TEXT
            🌟 *Welcome to 5K Vent — your anonymous voice at AAiT University.* 🌟
            
            🎓 Campus life can be a lot — pressure, joy, stress, friendships, heartbreaks, and everything in between.
            Whatever you're feeling, this is your space to speak freely.
            
            💬 Just type and send your thoughts — we’ll post them to the 5K Vent channel, *100% anonymously.*
            _No names. No judgments. Just a safe place where your voice matters._
            
            🛡 *Anonymous & Safe* 
            📢 *Say what’s on your mind — freely.*
            
            📩 *Ready when you are. Just send your message below.*  
            _We’re listening._ 💙
            TEXT;
            
            sendMessage($chatId, $msg, $token,$keyboard_default);
        }elseif (strpos($text, '/start report_') === 0) {
            $msg_id = str_replace('/start report_', '', $text);
            $msg = "⚠️ *Report for vent : $msg_id*";
            sendMessage($chatId, $msg, $token, $keyboard_for_reply);
        }elseif ($text == "/start writeMsg") {
            $msg = "✍️ Write your anonymous message below 👇";
            sendMessage($chatId, $msg, $token, $keyboard_default);
        } elseif ($text == "/help") {
            $msg = <<<TEXT
            🤖 *5K Vent Help* 🤖
            
            1️⃣ *To send a message:* Just type your thoughts and hit send. 
            2️⃣ *To report an issue:* click report button below message.
            3️⃣ *To view the channel:* click the 5K Vent button below.
            4️⃣ *To view comments:* click the view comments button below message.
            5️⃣ *To write comments:* click the reply button below message.
            
            _Your voice matters. We’re here to listen._
            TEXT;
            
            sendMessage($chatId, $msg, $token,$keyboard_default);
        } else {

            // If the user sends a message, forward it to the channel
            if ($chatType == 'private') {
                // send the message to the channel
                $forwardMsg = <<<TEXT
                📢 *Anonymous Vent * #$messageId
                
                $text
                TEXT; 
                sendMessage($channelId, $forwardMsg, $token, $keyboard_for_channel);
                
                // Send a confirmation message to the user 
                $yourmsg = "✅ Your message has been sent to the 5K Vent channel. Thank you for sharing! \n \n _your message:_ $text";
               sendMessage($chatId, $yourmsg, $token,$keyboard_default);

               // Notify the admin about the new vent
                $msgforAdmin = <<<TEXT
                📢 *New Vent from @$fromUser* #$messageId
                Phone: $phoneNumber
                TEXT;
                sendMessage($adminChatId, $msgforAdmin, $token,$keyboard_default);
               
            } else {
                // If it's a group or channel, just ignore the message
                return;
            }
        }
    }
} 

if ($callback) { 
    $chatId = $callback['message']['chat']['id'];
    $messageId = $callback['message']['message_id'];
    $callbackId = $callback['id'];
    $data = $callback['data'] ?? '';
    
    // Answer the callback (removes the "loading" animation)
    answerCallbackQuery($callbackId, $token);
    if($data == "/help") {
        $msg = <<<TEXT
        🤖 *5K Vent Help* 🤖
        
        1️⃣ *To send a message:* Just type your thoughts and hit send. 
        2️⃣ *To report an issue:* click report button below message.
        3️⃣ *To view the channel:* click the 5K Vent button below.
        4️⃣ *To view comments:* click the view comments button below message.
        5️⃣ *To write comments:* click the reply button below message.
        
        _Your voice matters. We’re here to listen._
        TEXT;
        
        sendMessage($chatId, $msg, $token, $keyboard_default);
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

// Function to send message
function sendMessage($chatId, $message, $token ,$keyboard) { 
    $url = "https://api.telegram.org/bot$token/sendMessage";

    $post = [
        'chat_id' => $chatId,
        'text' => $message,

        'reply_markup' => json_encode($keyboard),
        'parse_mode' => 'Markdown' // Optional: use HTML formatting
    ];
    file_get_contents($url . "?" . http_build_query($post));
     
}
 
