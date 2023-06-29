<?php

use yii\db\Schema;
use yii\db\Migration;

class m230627_035633_Relations extends Migration
{

    public function init()
    {
       $this->db = 'db';
       parent::init();
    }

    public function safeUp()
    {
        $this->addForeignKey('fk_orders_complete_client_id',
            '{{%orders_complete}}','client_id',
            '{{%clients}}','id',
            'CASCADE','CASCADE'
         );
        $this->addForeignKey('fk_orders_complete_order_id',
            '{{%orders_complete}}','order_id',
            '{{%orders}}','id',
            'CASCADE','CASCADE'
         );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_orders_complete_client_id', '{{%orders_complete}}');
        $this->dropForeignKey('fk_orders_complete_order_id', '{{%orders_complete}}');
    }
}
