<?php

use yii\db\Schema;
use yii\db\Migration;

class m230627_035632_orders_complete extends Migration
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
            '{{%orders_complete}}',
            [
                'id'=> $this->primaryKey(10)->unsigned()->comment('ID'),
                'shop'=> $this->string(255)->notNull()->comment('Название магазина'),
                'count'=> $this->integer(10)->unsigned()->notNull()->comment('Сумма платежа'),
                'method'=> $this->string(25)->notNull()->comment('Платежная система'),
                'payment_method'=> $this->string(255)->null()->defaultValue(null)->comment('Способ оплаты'),
                'fee'=> $this->float()->unsigned()->notNull()->comment('Комиссия платежной системы'),
                'order_id'=> $this->integer(10)->unsigned()->notNull()->comment('ID заказа'),
                'client_id'=> $this->integer(10)->unsigned()->notNull()->comment('ID клиента'),
            ],$tableOptions
        );
        $this->createIndex('idx_client_id_orders_complete','{{%orders_complete}}',['client_id'],false);
        $this->createIndex('idx_order_id_orders_complete','{{%orders_complete}}',['order_id'],false);
        $this->createIndex('idx_shop_orders_complete','{{%orders_complete}}',['shop'],false);

    }

    public function safeDown()
    {
        $this->dropIndex('idx_client_id_orders_complete', '{{%orders_complete}}');
        $this->dropIndex('idx_order_id_orders_complete', '{{%orders_complete}}');
        $this->dropIndex('idx_shop_orders_complete', '{{%orders_complete}}');
        $this->dropTable('{{%orders_complete}}');
    }
}
