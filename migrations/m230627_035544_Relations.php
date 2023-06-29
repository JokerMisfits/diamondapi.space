<?php

use yii\db\Schema;
use yii\db\Migration;

class m230627_035544_Relations extends Migration
{

    public function init()
    {
       $this->db = 'db';
       parent::init();
    }

    public function safeUp()
    {
        $this->addForeignKey('fk_orders_client_id',
            '{{%orders}}','client_id',
            '{{%clients}}','id',
            'CASCADE','CASCADE'
         );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_orders_client_id', '{{%orders}}');
    }
}
