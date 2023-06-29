<?php

use yii\db\Schema;
use yii\db\Migration;

class m230627_035319_clients extends Migration
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
            '{{%clients}}',
            [
                'id'=> $this->primaryKey(10)->unsigned()->comment('ID'),
                'tg_user_id'=> $this->bigInteger(15)->unsigned()->notNull()->comment('ID пользователя в telegram'),
                'shop'=> $this->string(255)->notNull()->comment('Название магазина'),
                'balance'=> $this->float()->notNull()->comment('Баланс клиента'),
                'blocked_balance'=> $this->float()->unsigned()->notNull()->defaultValue("0")->comment('Заблокированный баланс клиента'),
                'test_balance'=> $this->float()->notNull()->defaultValue("0")->comment('Тестовый баланс клиента'),
                'test_blocked_balance'=> $this->float()->unsigned()->notNull()->defaultValue("0")->comment('Заблокированный тестовый баланс клиента'),
                'cost'=> $this->integer(7)->unsigned()->notNull()->defaultValue(0)->comment('Стоимость подключения'),
                'profit'=> $this->float()->notNull()->defaultValue("0")->comment('Прибыль'),
                'test_profit'=> $this->float()->notNull()->defaultValue("0")->comment('Тестовая прибыль'),
                'commission'=> $this->integer(3)->unsigned()->notNull()->defaultValue(20)->comment('Процент прибыли'),
                'last_change'=> $this->timestamp(6)->notNull()->defaultExpression("CURRENT_TIMESTAMP(6)")->comment('Последнее изминение'),
                'admin_email'=> $this->string(128)->notNull()->comment('Почта владельца'),
                'total_withdrawal'=> $this->float()->unsigned()->notNull()->defaultValue("0")->comment('Сумма выведенных ДС клиентом'),
                'test_total_withdrawal'=> $this->float()->unsigned()->notNull()->defaultValue("0")->comment('Тестовая сумма выведенных ДС клиентом'),
                'total_withdrawal_profit'=> $this->float()->unsigned()->notNull()->defaultValue("0")->comment('Cумма выведенных ДС из прибыли(profit)'),
                'total_withdrawal_profit_test'=> $this->float()->unsigned()->notNull()->defaultValue("0")->comment('Cумма выведенных ДС из тестовой прибыли(test_profit)'),
                'min_count_withdrawal'=> $this->integer(6)->unsigned()->notNull()->defaultValue(1000)->comment('Минимальная сумма вывода'),
                'is_action_test'=> $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('Тест оплат'),
                'is_lk_test'=> $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('Тест личного кабинета'),
                'bot_token'=> $this->string(255)->notNull()->comment('Токен бота'),
                'robokassa'=> $this->text()->null()->defaultValue(null)->comment('Настройки RoboKassa'),
                'paykassa'=> $this->text()->null()->defaultValue(null)->comment('Настройки PayKassa'),
                'freekassa'=> $this->text()->null()->defaultValue(null)->comment('Настройки FreeKassa'),
                'paypall'=> $this->text()->null()->defaultValue(null)->comment('Настройки PayPall'),
                'member_id'=> $this->integer(10)->unsigned()->null()->defaultValue(null)->comment('ID пользователя в БД'),
            ],$tableOptions
        );
        $this->createIndex('idx_shop_clients','{{%clients}}',['shop'],true);

    }

    public function safeDown()
    {
        $this->dropIndex('idx_shop_clients', '{{%clients}}');
        $this->dropTable('{{%clients}}');
    }
}
