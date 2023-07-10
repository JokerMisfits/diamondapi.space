<?php

if(!isset($_SERVER['API_KEY_0'])){
    $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__FILE__) . '/../');
    $dotenv->load();
}

return [
    'adminEmail' => 'admin@diamondapi.space',
    'testEmail' => 'test@diamondapi.space',
    'senderEmail' => 'noreply@diamondapi.space',
    'senderName' => 'diamondapi.space'
];