<?php

use yii\db\Schema;
use yii\db\Migration;

class m230627_035705_withdrawals extends Migration
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
            '{{%withdrawals}}',
            [
                'id'=> $this->primaryKey(10)->unsigned()->comment('ID'),
                'tg_user_id'=> $this->bigInteger(15)->notNull()->comment('ID пользователя в telegram'),
                'shop'=> $this->string(255)->null()->defaultValue(null)->comment('Название магазина'),
                'status'=> $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('Статус вывода ДС'),
                'created_time'=> $this->timestamp(6)->notNull()->defaultExpression("CURRENT_TIMESTAMP(6)")->comment('Дата создания заявки на вывод ДС'),
                'confirmed_time'=> $this->timestamp(6)->null()->defaultValue(null)->comment('Дата подтверждения заявки от клиента'),
                'resulted_time'=> $this->timestamp(6)->null()->defaultValue(null)->comment('Дата вывода ДС'),
                'is_test'=> $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('Тест?'),
                'count'=> $this->integer(10)->notNull()->comment('Сумма'),
                'commission'=> $this->integer(10)->notNull()->defaultValue(0)->comment('Комиссия'),
                'confirmation_link'=> $this->string(255)->notNull()->defaultValue('')->comment('Ссылка для подтверждения'),
                'card_number'=> $this->string(19)->notNull()->comment('Номер карты'),
                'comment'=> $this->text()->notNull()->comment('Комментарий'),
                'client_id'=> $this->integer(10)->unsigned()->notNull()->comment('ID клиента'),
            ],$tableOptions
        );
        $this->createIndex('idx_client_id','{{%withdrawals}}',['client_id'],false);

    }

    public function safeDown()
    {
        $this->dropIndex('idx_client_id', '{{%withdrawals}}');
        $this->dropTable('{{%withdrawals}}');
    }
}
