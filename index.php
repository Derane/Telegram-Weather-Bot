<?php

error_reporting(-1);
ini_set('display_errors', 0);
ini_set('log_errors', 'on');
ini_set('error_log', __DIR__ . '/errors.log');

require __DIR__ . '/vendor/autoload.php';
require_once 'config.php';
require_once 'functions.php';

$telegram = new \Telegram\Bot\Api(TOKEN);

$update = $telegram->getWebhookUpdate();
$chat_id = $update['message']['chat']['id'] ?? 0;
$text = $update['message']['text'] ?? '';
$name = $update['message']['from']['first_name'] ?? 'Guest';

if (!$chat_id) {
    die;
}

if ($text == '/start') {
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => 'Hello! \n I am a weatherman bot that will tell you the weather in any city in the world',
        'parse_mode' => 'HTML'
    ]);
} elseif ($text == '/help') {
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => 'I am a weatherman bot that will tell you the weather in any city in the world',
        'parse_mode' => 'HTML'
    ]);
} else {
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => 'Please send correct address format',
    ]);
}
