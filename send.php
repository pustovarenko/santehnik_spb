<?php
// send.php

// Подключаем файл с настройками
require_once 'config.php';

// Проверяем, что форма была отправлена методом POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Получаем номер телефона и очищаем от лишнего
    $phone = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : '';
    
    // Если телефон заполнен
    if (!empty($phone)) {
        
        // Формируем текст сообщения
        $message = "🚽 <b>Новая заявка с сайта</b>\n\n";
        $message .= "📞 <b>Телефон:</b> " . $phone . "\n";
        $message .= "📅 <b>Время:</b> " . date('d.m.Y H:i');

        // Адрес API Telegram
        $url = "https://api.telegram.org/bot{$tg_bot_token}/sendMessage";

        // Проходимся по списку ID и отправляем каждому
        foreach ($tg_chat_ids as $chat_id) {
            $data = [
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => 'HTML' // Включаем поддержку жирного шрифта
            ];

            // Пытаемся отправить через cURL (самый надежный способ)
            if(extension_loaded('curl')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
            } else {
                // Если cURL нет, используем простой метод
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

        // Выводим сообщение об успехе и возвращаем на главную
        echo '<script>
            alert("Спасибо! Ваша заявка принята. Мастер скоро свяжется с вами.");
            window.location.href = "index.html"; 
        </script>';
    } else {
        // Если телефон не ввели
        echo '<script>
            alert("Пожалуйста, укажите номер телефона.");
            history.back();
        </script>';
    }
} else {
    // Если открыли файл напрямую в браузере
    header("Location: index.html");
    exit();
}
?>