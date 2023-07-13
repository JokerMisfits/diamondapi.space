<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

if(!isset($_SERVER['DB_DSN'])){
    $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__FILE__) . '/../');
    $dotenv->load();
}

$config = [
    'id' => 'basic',
    'name' => 'diamondapi.space',
    'version' => '1.0.0',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-Ru',
    'bootstrap' => ['log'],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
            'layout' => 'admin'
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset'
    ],
    'components' => [
        'request' => [
            'baseUrl'=> '',
            'cookieValidationKey' => 'mMfzDCvGmhFVcozwVFk4JaCND43MKdO0'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache'
        ],
        'user' => [
            'identityClass' => 'app\models\Users',
            'enableAutoLogin' => true
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.mail.ru', // Укажите адрес SMTP-сервера
                'port' => '465', // Укажите порт SMTP-сервера
                'encryption' => 'SSL', // Укажите метод шифрования, если необходимо
                'username' => $_SERVER['EMAIL_ADMIN_LOGIN'], // Укажите имя пользователя для авторизации на SMTP-сервере
                'password' => $_SERVER['EMAIL_ADMIN_PASSWORD'] // Укажите пароль для авторизации на SMTP-сервере
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/app.log'
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix' => '',
            'rules' => [
                '<action:(index|about|contact|update|signup|login|logout)>' => 'site/<action>',
                'payment' => 'payment/index',
                'lk' => 'lk/index',
                'confirmation' => 'payment/confirmation',
                'admin/test' => 'admin/default/test'

            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable' => '{{%auth_item}}',
            'assignmentTable' => '{{%auth_assignment}}',
            'ruleTable' => '{{%auth_rule}}',
            'itemChildTable' => '{{%auth_item_child}}',
            'defaultRoles' => ['guest']
        ],
        'captcha' => [
            'class' => 'yii\captcha\CaptchaAction',
            'fixedVerifyCode' => null
        ],
    ],
    'params' => $params
];


if(YII_ENV_DEV){
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module'
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['components']['errorHandler'] = [
        'errorAction' => 'site/error'
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module'
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;