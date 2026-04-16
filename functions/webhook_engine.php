<?php
/**
 * Moonlight Digital - Advanced Telegram Webhook Engine (V3)
 * Handles rich messages, inline keyboards, photo deliveries, and user notifications.
 */

/**
 * Core cURL Executor for Telegram API
 */
function execute_telegram_curl($url, $data, $is_multipart = false) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    
    if ($is_multipart) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    } else {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // 5-second timeout ensures your website never freezes if Telegram servers are slow
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        error_log("Telegram Webhook Error: " . curl_error($ch));
    }
    
    curl_close($ch);
    return $response;
}

/**
 * Generic Base Function for Text Messages
 */
function send_telegram_message($chat_id, $text, $reply_markup = null) {
    $bot_token = $_ENV['TELEGRAM_BOT_TOKEN'] ?? null;
    if (!$bot_token || !$chat_id) return false;

    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true // Prevents massive link previews in chat
    ];

    if ($reply_markup) {
        $data['reply_markup'] = json_encode($reply_markup);
    }

    return execute_telegram_curl($url, $data);
}

/**
 * Send an Alert specifically to the MoonAdmin
 */
function notify_admin_bot($message, $inline_keyboard = null) {
    $admin_chat_id = $_ENV['TELEGRAM_ADMIN_CHAT_ID'] ?? null; 
    if (!$admin_chat_id) return false;
    
    return send_telegram_message($admin_chat_id, $message, $inline_keyboard);
}

/**
 * Send a direct DM to a specific user (e.g., delivering a key)
 * Requires the user to have started the bot previously (auth_tg_uid)
 */
function notify_user_bot($tg_uid, $message, $inline_keyboard = null) {
    if (!$tg_uid) return false;
    return send_telegram_message($tg_uid, $message, $inline_keyboard);
}

/**
 * Send an Image/Receipt to Telegram
 */
function send_telegram_photo($chat_id, $photo_url_or_path, $caption = '', $reply_markup = null) {
    $bot_token = $_ENV['TELEGRAM_BOT_TOKEN'] ?? null;
    if (!$bot_token || !$chat_id) return false;

    $url = "https://api.telegram.org/bot{$bot_token}/sendPhoto";
    $data = [
        'chat_id' => $chat_id,
        'caption' => $caption,
        'parse_mode' => 'HTML'
    ];
    
    if ($reply_markup) {
        $data['reply_markup'] = json_encode($reply_markup);
    }

    // Determine if we are sending a local file path or a public web URL
    if (filter_var($photo_url_or_path, FILTER_VALIDATE_URL)) {
        $data['photo'] = $photo_url_or_path;
        return execute_telegram_curl($url, $data);
    } elseif (file_exists($photo_url_or_path)) {
        // Send actual file via CURLFile
        $data['photo'] = new CURLFile(realpath($photo_url_or_path));
        return execute_telegram_curl($url, $data, true);
    }
    
    return false;
}

/**
 * Helper: Generate a beautifully formatted Admin Order Alert
 */
function format_admin_order_alert($ledger_id, $amount, $currency, $user_tag) {
    $msg = "🚨 <b>New Transaction Pending!</b>\n";
    $msg .= "━━━━━━━━━━━━━━━━━━━━\n";
    $msg .= "🧾 <b>Ledger:</b> #{$ledger_id}\n";
    $msg .= "👤 <b>User:</b> {$user_tag}\n";
    $msg .= "💰 <b>Value:</b> " . number_format($amount) . " {$currency}\n\n";
    $msg .= "<i>Please verify the transaction proof in the Admin Nexus.</i>";
    
    // Add clickable buttons right inside the Telegram message
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '✅ Approve Order', 'callback_data' => 'approve_' . $ledger_id],
                ['text' => '❌ Reject', 'callback_data' => 'reject_' . $ledger_id]
            ],
            [
                ['text' => '🌐 Open Admin Nexus', 'url' => $_ENV['ADMIN_URL'] ?? 'https://admin.moonlightdigitalmarket.com']
            ]
        ]
    ];
    
    return notify_admin_bot($msg, $keyboard);
}
?>