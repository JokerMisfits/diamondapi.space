<?php

use yii\db\Schema;
use yii\db\Migration;

class m230627_035644_users extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%users}}',
            [
                'id'=> $this->primaryKey(10)->unsigned()->comment('ID'),
                'username'=> $this->string(32)->notNull()->comment('Имя пользователя'),
                'password'=> $this->string(128)->notNull()->comment('Пароль'),
                'tg_user_id'=> $this->bigInteger(12)->unsigned()->null()->defaultValue(null)->comment('ID пользователя в telegram'),
                'email'=> $this->string(128)->null()->defaultValue(null)->comment('Email'),
                'phone'=> $this->string(20)->null()->defaultValue(null)->comment('Номер телефона'),
                'cookie'=> $this->string(64)->null()->defaultValue(null)->comment('Кука'),
                'last_activity'=> $this->timestamp()->notNull()->defaultExpression("CURRENT_TIMESTAMP")->comment('Дата последней активности'),
                'access_level'=> $this->integer(3)->unsigned()->notNull()->defaultValue(0)->comment('Уровень доступа'),
                'member_id'=> $this->integer(10)->unsigned()->null()->defaultValue(null)->comment('ID пользователя'),
            ],$tableOptions
        );
        $this->createIndex('idx_username_users','{{%users}}',['username'],true);
        $this->createIndex('idx_email_users','{{%users}}',['email'],true);
        $this->createIndex('idx_phone_users','{{%users}}',['phone'],true);

    }

    public function safeDown()
    {
        $this->dropIndex('idx_username_users', '{{%users}}');
        $this->dropIndex('idx_email_users', '{{%users}}');
        $this->dropIndex('idx_phone_users', '{{%users}}');
        $this->dropTable('{{%users}}');
    }
}
