<?php

namespace tests\unit\models;

use app\models\Users;
use Codeception\Test\Unit;

class UserTest extends Unit{
    public function testFindUserById(){
        verify($user = Users::findIdentity(100))->notEmpty();
        verify($user->username)->equals('admin');
        verify(Users::findIdentity(999))->empty();
    }

    public function testFindUserByAccessToken(){
        verify($user = Users::findIdentityByAccessToken('100-token'))->notEmpty();
        //verify($user->username)->equals('admin');
        verify(Users::findIdentityByAccessToken('non-existing'))->empty();        
    }

    public function testFindUserByUsername(){
        //verify($user = Users::findByUsername('admin'))->notEmpty();
        //verify(Users::findByUsername('not-admin'))->empty();
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testValidateUser(){
        //$user = Users::findByUsername('admin');
        //verify($user->validateAuthKey('test100key'))->notEmpty();
        //verify($user->validateAuthKey('test102key'))->empty();

        //verify($user->validatePassword('admin'))->notEmpty();
        //verify($user->validatePassword('123456'))->empty();        
    }

}