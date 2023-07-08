<?php

use yii\web\User;
use app\models\Users;
use yii\rbac\DbManager;
use yii\web\Application;
use yii\console\Application AS consoleApplication;

/**
 * This class only exists here for IDE (PHPStorm/Netbeans/...) autocompletion.
 * This file is never included anywhere.
 * Adjust this file to match classes configured in your application config, to enable IDE autocompletion for custom components.
 * Example: A property phpdoc can be added in `__Application` class as `@property \vendor\package\Rollbar|__Rollbar $rollbar` and adding a class in this file
 * ```php
 * // @property of \vendor\package\Rollbar goes here
 * class __Rollbar {
 * }
 * ```
 */
class Yii{
    /**
     * @var Application|consoleApplication|__Application
     */
    public static $app;
}

/**
 * @property DbManager $authManager 
 * @property User|__WebUser $user
 * 
 */
class __Application{
}

/**
 * @property Users $identity
 */
class __WebUser{
}