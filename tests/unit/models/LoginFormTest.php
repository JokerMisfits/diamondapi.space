<?php

namespace tests\unit\models;

use Yii;
use app\models\Users;
use Codeception\Test\Unit;

class LoginFormTest extends Unit{
    private $model;

    protected function _after(){
        Yii::$app->user->logout();
    }

    public function testLoginNoUser(){
        $this->model = new Users([
            'username' => 'not_existing_username',
            'password' => 'not_existing_password',
        ]);

        verify($this->model->login())->false();
        verify(Yii::$app->user->isGuest)->true();
    }

    public function testLoginWrongPassword(){
        $this->model = new Users([
            'username' => 'demo',
            'password' => 'wrong_password',
        ]);

        verify($this->model->login())->false();
        verify(Yii::$app->user->isGuest)->true();
        verify($this->model->errors)->arrayHasKey('password');
    }

    public function testLoginCorrect(){
        $this->model = new Users([
            'username' => 'demo',
            'password' => 'demo',
        ]);

        verify($this->model->login())->true();
        verify(Yii::$app->user->isGuest)->false();
        verify($this->model->errors)->arrayHasNotKey('password');
    }

}