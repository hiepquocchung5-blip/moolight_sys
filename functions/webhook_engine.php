<?php
/**
 * Moonlight Digital - Telegram Webhook Engine
 * Connects the system to the MoonAdmin and MoonUser Telegram bots.
 */

function moon_notify_admin($message, $image_path = null) {
    $bot_token = $_ENV['TELEGRAM_BOT_TOKEN'] ?? null;
    $admin_chat_id = $_ENV['ADMIN_CHAT_ID'] ?? null; // Add this to your .env

    if (!$bot_token || !$admin_chat_id) return false;

    $url = "https://api.telegram.org/bot" . $bot_token;

    if ($image_path && file_exists($image_path)) {
        // Send Photo with caption
        $url .= "/sendPhoto";
        $post_fields = [
            'chat_id' => $admin_chat_id,
            'caption' => $message,
            'photo' => new CURLFile(realpath($image_path))
        ];
    } else {
        // Send Text Message
        $url .= "/sendMessage";
        $post_fields = [
            'chat_id' => $admin_chat_id,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}
?>