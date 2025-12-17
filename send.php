<?php
// send.php

require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $phone = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : '';
    
    if (!empty($phone)) {
        
        $message = "🚽 <b>Новая заявка с сайта</b>\n\n";
        $message .= "📞 <b>Телефон:</b> " . $phone . "\n";
        $message .= "📅 <b>Время:</b> " . date('d.m.Y H:i');

        $url = "https://api.telegram.org/bot{$tg_bot_token}/sendMessage";

        foreach ($tg_chat_ids as $chat_id) {
            $data = [
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => 'HTML'
            ];

            if(extension_loaded('curl')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
            } else {
                $options = [
                    'http' => [
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data)
                    ]
                ];
                $context  = stream_context_create($options);
                @file_get_contents($url, false, $context);
            }
        }

        // --- ВОТ ЭТА ЧАСТЬ ОТВЕЧАЕТ ЗА РЕДИРЕКТ ---
        // Перенаправляем на страницу спасибо
        header("Location: thanks.html");
        exit(); 
        // ------------------------------------------

    } else {
        echo '<script>alert("Пожалуйста, укажите номер телефона."); history.back();</script>';
    }
} else {
    header("Location: index.html");
    exit();
}
?>
