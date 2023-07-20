<?php

error_reporting(-1);
ini_set('display_errors', 0);
ini_set('log_errors', 'on');
ini_set('error_log', __DIR__ . '/errors.log');

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

/** @var $weather_url */

$telegram = new \Telegram\Bot\Api(TOKEN);
$update = $telegram->getWebhookUpdate();

debug($update);

$chat_id = $update['message']['chat']['id'] ?? 0;
$text = $update['message']['text'] ?? '';
$name = $update['message']['from']['first_name'] ?? 'Guest';

if (!$chat_id) {
    die;
}

if ($text == '/start') {
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => "I am a weatherman bot that will tell you the weather in any city in the world. To get the weather,
 send a geolocation (available from mobile devices).\nAlso it is possible to specify the city in the format: <b>City</b> or in the format <b>City,country code</b>.\nExample: <b>London </b>, <b>London,uk</b>, <b>Kyiv,ua</b>,<b> Київ</b>",
        'parse_mode' => 'HTML',
    ]);
} elseif ($text == '/help') {
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => "I am a weatherman bot that will tell you the weather in any city in the world. To get the weather,
 send a geolocation (available from mobile devices).\nAlso it is possible to specify the city in the format: <b>City</b> or in the format <b>City,country code</b>.\nExample: <b>London </b>, <b>London,uk</b>, <b>Kyiv,ua</b>,<b> Київ</b>",
        'parse_mode' => 'HTML',
    ]);
} elseif (!empty($text)) {
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => "Requesting data...",
    ]);
    $weather_url .= "&q={$text}";
    $weather = send_request($weather_url);
} elseif (isset($update['message']['location'])) {
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => "Requesting data...",
    ]);
    $weather_url .= "&lat={$update['message']['location']['latitude']}&lon={$update['message']['location']['longitude']}";

    $weather = send_request($weather_url);
    debug($weather);
} else {
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => "Please send correct location",
    ]);
}

if (isset($weather)) {
    if ($weather->cod == 200) {
        $temp = round($weather->main->temp);
        $answer = "<u>Infromation about weather:</u>\nCity: <b>{$weather->name}</b>\nCountry: <b>{$weather->sys->country}
</b>\nWeather: <b>{$weather->weather[0]->description}</b>\nTemperature: <b>{$temp}℃</b>";
        $telegram->sendPhoto([
            'chat_id' => $chat_id,
            'photo' => \Telegram\Bot\FileUpload\InputFile::create(__DIR__ . "/img/{$weather->weather[0]->icon}.png"),
            'caption' => $answer,
            'parse_mode' => 'HTML',
        ]);
    } elseif ($weather->cod == 404) {
        $telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => "Please send correct location ",
        ]);
    } else {
        debug($weather);
        $telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Something went wrong. Please try later...',
        ]);
    }
}
