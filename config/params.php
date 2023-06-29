<?php

if(!isset($_SERVER['API_KEY_0'])){
    $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__FILE__) . '/../');
    $dotenv->load();
}

return [
    'adminEmail' => 'admin@www.diamondapi.space',
    'senderEmail' => 'noreply@www.diamondapi.space',
    'senderName' => 'www.diamondapi.space mailer',
    'apikey0' => $_SERVER['API_KEY_0'],
    'apikey1' => $_SERVER['API_KEY_1']
];