<?php

error_reporting(-1);
ini_set('display_errors', 0);
ini_set('log_errors', 'on');
ini_set('error_log', __DIR__ . '/errors.log');

require __DIR__ . '/vendor/autoload.php';
require_once 'config.php';
require_once 'functions.php';

// var_dump(BASE_URL . 'setWebhook?url=https://webformyself-bots.space/bots/1/');
$telegram = new \Telegram\Bot\Api(TOKEN);

$update = json_decode(file_get_contents('php://input'));

debug($update);

