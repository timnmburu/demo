<?php

    require_once __DIR__ . '/../vendor/autoload.php';

    use IntaSend\IntaSendPHP\Collection;
    use Dotenv\Dotenv;

    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();

    function notify($errorMsg){

        // Your bot's API token
        $botToken = $_ENV['TELEGRAM_BOT_API_TOKEN'];

        // Your chat ID
        $chatID = $_ENV['TELEGRAM_CHAT_ID'];
        

        // Error details
        //$errorMsg = 'An error occurred on the server: Something went wrong!';

        // Telegram API endpoint
        $telegramAPI = "https://api.telegram.org/bot{$botToken}/sendMessage";

        // Prepare the message data
        $messageData = [
            'chat_id' => $chatID,
            'text' => $errorMsg,
        ];

        // Use cURL to send the message
        $ch = curl_init($telegramAPI);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $messageData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // Check the response if needed
        if ($response === false) {
            // Handle cURL error
        } else {
            $responseData = json_decode($response, true);
            // Handle the Telegram API response if needed
        }

    }


?>