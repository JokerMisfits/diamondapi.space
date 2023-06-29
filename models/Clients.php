<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clients".
 *
 * @property int $id ID
 * @property int $tg_user_id ID пользователя в telegram
 * @property string $shop Название магазина
 * @property float $balance Баланс клиента
 * @property float $blocked_balance Заблокированный баланс клиента
 * @property float $test_balance Тестовый баланс клиента
 * @property float $test_blocked_balance Заблокированный тестовый баланс клиента
 * @property int $cost Стоимость подключения
 * @property float $profit Прибыль
 * @property float $test_profit Тестовая прибыль
 * @property int $commission Процент прибыли
 * @property string $last_change Последнее изминение
 * @property string $admin_email Почта владельца
 * @property float $total_withdrawal Сумма выведенных ДС клиентом
 * @property float $test_total_withdrawal Тестовая сумма выведенных ДС клиентом
 * @property float $total_withdrawal_profit Cумма выведенных ДС из прибыли(profit)
 * @property float $total_withdrawal_profit_test Cумма выведенных ДС из тестовой прибыли(test_profit)
 * @property int $min_count_withdrawal Минимальная сумма вывода
 * @property int $is_action_test Тест оплат
 * @property int $is_lk_test Тест личного кабинета
 * @property string $bot_token Токен бота
 * @property string|null $robokassa Настройки RoboKassa
 * @property string|null $paykassa Настройки PayKassa
 * @property string|null $freekassa Настройки FreeKassa
 * @property string|null $paypall Настройки PayPall
 * @property int|null $member_id ID пользователя в БД
 *
 * @property BotConfigs[] $botConfigs
 * @property BotGifts[] $botGifts
 * @property BotMembers[] $botMembers
 * @property BotTexts[] $botTexts
 * @property BotTickets[] $botTickets
 * @property Orders[] $orders
 * @property OrdersComplete[] $ordersCompletes
 * @property Withdrawals[] $withdrawals
 */
class Clients extends \yii\db\ActiveRecord{
    /**
     * {@inheritdoc}
     */
    public static function tableName(){
        return 'clients';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['tg_user_id', 'shop', 'balance', 'admin_email', 'bot_token'], 'required'],
            [['tg_user_id', 'cost', 'commission', 'min_count_withdrawal', 'is_action_test', 'is_lk_test', 'member_id'], 'integer'],
            [['balance', 'blocked_balance', 'test_balance', 'test_blocked_balance', 'profit', 'test_profit', 'total_withdrawal', 'test_total_withdrawal', 'total_withdrawal_profit', 'total_withdrawal_profit_test'], 'number'],
            [['last_change'], 'safe'],
            [['robokassa', 'paykassa', 'freekassa', 'paypall'], 'string'],
            [['shop', 'bot_token'], 'string', 'max' => 255],
            [['admin_email'], 'string', 'max' => 128],
            [['shop'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(){
        return [
            'id' => 'ID',
            'tg_user_id' => 'ID пользователя в telegram',
            'shop' => 'Название магазина',
            'balance' => 'Баланс клиента',
            'blocked_balance' => 'Заблокированный баланс клиента',
            'test_balance' => 'Тестовый баланс клиента',
            'test_blocked_balance' => 'Заблокированный тестовый баланс клиента',
            'cost' => 'Стоимость подключения',
            'profit' => 'Прибыль',
            'test_profit' => 'Тестовая прибыль',
            'commission' => 'Процент прибыли',
            'last_change' => 'Последнее изминение',
            'admin_email' => 'Почта владельца',
            'total_withdrawal' => 'Сумма выведенных ДС клиентом',
            'test_total_withdrawal' => 'Тестовая сумма выведенных ДС клиентом',
            'total_withdrawal_profit' => 'Cумма выведенных ДС из прибыли(profit)',
            'total_withdrawal_profit_test' => 'Cумма выведенных ДС из тестовой прибыли(test_profit)',
            'min_count_withdrawal' => 'Минимальная сумма вывода',
            'is_action_test' => 'Тест оплат',
            'is_lk_test' => 'Тест личного кабинета',
            'bot_token' => 'Токен бота',
            'robokassa' => 'Настройки RoboKassa',
            'paykassa' => 'Настройки PayKassa',
            'freekassa' => 'Настройки FreeKassa',
            'paypall' => 'Настройки PayPall',
            'member_id' => 'ID пользователя в БД',
        ];
    }

    /**
     * Gets query for [[BotConfigs]].
     *
     * @return \yii\db\ActiveQuery|BotConfigsQuery
     */
    public function getBotConfigs(){
        return $this->hasMany(BotConfigs::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[BotGifts]].
     *
     * @return \yii\db\ActiveQuery|BotGiftsQuery
     */
    public function getBotGifts(){
        return $this->hasMany(BotGifts::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[BotMembers]].
     *
     * @return \yii\db\ActiveQuery|BotMembersQuery
     */
    public function getBotMembers(){
        return $this->hasMany(BotMembers::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[BotTexts]].
     *
     * @return \yii\db\ActiveQuery|BotTextsQuery
     */
    public function getBotTexts(){
        return $this->hasMany(BotTexts::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[BotTickets]].
     *
     * @return \yii\db\ActiveQuery|BotTicketsQuery
     */
    public function getBotTickets(){
        return $this->hasMany(BotTickets::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery|yii\db\ActiveQuery
     */
    public function getOrders(){
        return $this->hasMany(Orders::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[OrdersCompletes]].
     *
     * @return \yii\db\ActiveQuery|OrdersCompleteQuery
     */
    public function getOrdersCompletes(){
        return $this->hasMany(OrdersComplete::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[Withdrawals]].
     *
     * @return \yii\db\ActiveQuery|yii\db\ActiveQuery
     */
    public function getWithdrawals(){
        return $this->hasMany(Withdrawals::class, ['client_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return ClientsQuery the active query used by this AR class.
     */
    public static function find(){
        return new ClientsQuery(get_called_class());
    }
}