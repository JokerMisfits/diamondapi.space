<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

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
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['components']['errorHandler'] = [
        'errorAction' => 'site/error'
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;