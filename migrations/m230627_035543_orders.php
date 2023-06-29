<?php

use yii\db\Schema;
use yii\db\Migration;

class m230627_035543_orders extends Migration
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
            '{{%orders}}',
            [
                'id'=> $this->primaryKey(10)->unsigned()->comment('ID'),
                'tg_user_id'=> $this->bigInteger(15)->unsigned()->notNull()->comment('ID пользователя в telegram'),
                'status'=> $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('Статус платежа'),
                'count'=> $this->integer(10)->unsigned()->notNull()->comment('Сумма платежа'),
                'method'=> $this->string(25)->notNull()->comment('Платежная система'),
                'shop'=> $this->string(255)->notNull()->comment('Название магазина'),
                'position_name'=> $this->string(255)->null()->defaultValue(null)->comment('Название товара'),
                'access_days'=> $this->integer(10)->unsigned()->notNull()->comment('Количество дней'),
                'created_time'=> $this->timestamp(6)->notNull()->defaultExpression("CURRENT_TIMESTAMP(6)")->comment('Дата создания заказа'),
                'resulted_time'=> $this->timestamp(6)->null()->defaultValue(null)->comment('Дата оплаты заказа'),
                'is_test'=> $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('Тест?'),
                'web_app_query_id'=> $this->string(64)->notNull()->defaultValue('')->comment('ID web_app окна в telegram'),
                'currency'=> $this->string(255)->null()->defaultValue(null)->comment('Валюта'),
                'count_in_currency'=> $this->float()->null()->defaultValue("0")->comment('Сумма в валюте'),
                'commission'=> $this->float()->unsigned()->notNull()->defaultValue("0")->comment('Комиссия платежа'),
                'paypal_order_id'=> $this->string(40)->null()->defaultValue(null)->comment('Номер заказа в платежной системе PayPal'),
                'client_id'=> $this->integer(10)->unsigned()->notNull()->comment('ID клиента'),
            ],$tableOptions
        );
        $this->createIndex('idx_client_id_orders','{{%orders}}',['client_id'],false);

    }

    public function safeDown()
    {
        $this->dropIndex('idx_client_id_orders', '{{%orders}}');
        $this->dropTable('{{%orders}}');
    }
}
