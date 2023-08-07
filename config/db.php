<?php

if(!array_key_exists('DB_DSN', $_SERVER)){
    $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__FILE__) . '/../');
    $dotenv->load();
}

return [
    'class' => 'yii\db\Connection',
    'dsn' => $_SERVER['DB_DSN'],
    'username' => $_SERVER['DB_USERNAME'],
    'password' => $_SERVER['DB_PASSWORD'],
    'charset' => 'utf8mb4',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache'
];