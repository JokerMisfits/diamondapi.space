<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
 * @property string $admin_email Почта владельца || удалить брать из tg_member
 * @property float $total_withdrawal Сумма выведенных ДС клиентом
 * @property float $test_total_withdrawal Тестовая сумма выведенных ДС клиентом
 * @property float $total_withdrawal_profit Cумма выведенных ДС из прибыли(profit)
 * @property float $total_withdrawal_profit_test Cумма выведенных ДС из тестовой прибыли(test_profit)
 * @property int $min_count_withdrawal Минимальная сумма вывода
 * @property int $is_action_test Тест оплат || УДАЛИТЬ ПОСЛЕ ПЕРЕНОСА
 * @property int $is_lk_test Тест личного кабинета || УДАЛИТЬ ПОСЛЕ ПЕРЕНОСА
 * @property string|null $bot_token Токен бота
 * @property string|null $robokassa Настройки RoboKassa
 * @property string|null $paykassa Настройки PayKassa
 * @property string|null $freekassa Настройки FreeKassa
 * @property string|null $paypall Настройки PayPall
 * @property int|null $tg_chat_id ID tg_chats
 * @property int|null $tg_private_chat_id ID tg_chats | private
 * @property int|null $tg_member_id ID tg_members
 *
 * @property BotConfigs[] $botConfigs
 * @property BotGifts[] $botGifts
 * @property BotMembers[] $botMembers
 * @property BotTexts[] $botTexts
 * @property BotTickets[] $botTickets
 * @property Orders[] $orders
 * @property OrdersComplete[] $ordersCompletes
 * @property TgChats $tgChat 
 * @property TgChats[] $tgChats 
 * @property TgMembers $tgMember
 * @property TgChats $tgPrivateChat 
 * @property Withdrawals[] $withdrawals
 */
class Clients extends ActiveRecord{
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
            [['tg_user_id', 'shop', 'balance', 'admin_email'], 'required'],
            [['tg_user_id', 'cost', 'commission', 'min_count_withdrawal', 'is_action_test', 'is_lk_test', 'tg_chat_id', 'tg_private_chat_id', 'tg_member_id'], 'integer'],
            [['balance', 'blocked_balance', 'test_balance', 'test_blocked_balance', 'profit', 'test_profit', 'total_withdrawal', 'test_total_withdrawal', 'total_withdrawal_profit', 'total_withdrawal_profit_test'], 'number'],
            [['last_change'], 'safe'],
            [['robokassa', 'paykassa', 'freekassa', 'paypall'], 'string'],
            [['shop', 'bot_token'], 'string', 'max' => 255],
            [['admin_email'], 'string', 'max' => 128],
            [['shop'], 'unique'],
            [['tg_chat_id'], 'unique'],
            [['tg_private_chat_id'], 'unique'],
            [['tg_chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => TgChats::class, 'targetAttribute' => ['tg_chat_id' => 'id']],
            [['tg_private_chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => TgChats::class, 'targetAttribute' => ['tg_private_chat_id' => 'id']],
            [['tg_member_id'], 'exist', 'skipOnError' => true, 'targetClass' => TgMembers::class, 'targetAttribute' => ['tg_member_id' => 'id']]
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
            'admin_email' => 'Почта владельца || удалить брать из tg_member',
            'total_withdrawal' => 'Сумма выведенных ДС клиентом',
            'test_total_withdrawal' => 'Тестовая сумма выведенных ДС клиентом',
            'total_withdrawal_profit' => 'Cумма выведенных ДС из прибыли(profit)',
            'total_withdrawal_profit_test' => 'Cумма выведенных ДС из тестовой прибыли(test_profit)',
            'min_count_withdrawal' => 'Минимальная сумма вывода',
            'is_action_test' => 'Тест оплат || УДАЛИТЬ ПОСЛЕ ПЕРЕНОСА',
            'is_lk_test' => 'Тест личного кабинета || УДАЛИТЬ ПОСЛЕ ПЕРЕНОСА',
            'bot_token' => 'Токен бота',
            'robokassa' => 'Настройки RoboKassa',
            'paykassa' => 'Настройки PayKassa',
            'freekassa' => 'Настройки FreeKassa',
            'paypall' => 'Настройки PayPall',
            'tg_chat_id' => 'ID tg_chats',
            'tg_private_chat_id' => 'ID tg_chats | private',
            'tg_member_id' => 'ID tg_members'
        ];
    }

    /**
     * Gets query for [[BotConfigs]].
     *
     * @return ActiveQuery|BotConfigsQuery
     */
    public function getBotConfigs(){
        return $this->hasMany(BotConfigs::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[BotGifts]].
     *
     * @return ActiveQuery|BotGiftsQuery
     */
    public function getBotGifts(){
        return $this->hasMany(BotGifts::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[BotMembers]].
     *
     * @return ActiveQuery|BotMembersQuery
     */
    public function getBotMembers(){
        return $this->hasMany(BotMembers::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[BotTexts]].
     *
     * @return ActiveQuery|BotTextsQuery
     */
    public function getBotTexts(){
        return $this->hasMany(BotTexts::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[BotTickets]].
     *
     * @return ActiveQuery|BotTicketsQuery
     */
    public function getBotTickets(){
        return $this->hasMany(BotTickets::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return ActiveQuery|OrdersQuery
     */
    public function getOrders(){
        return $this->hasMany(Orders::class, ['client_id' => 'id']);
    }

    /**
     * Gets query for [[OrdersCompletes]].
     *
     * @return ActiveQuery|OrdersCompleteQuery
     */
    public function getOrdersCompletes(){
        return $this->hasMany(OrdersComplete::class, ['client_id' => 'id']);
    }

    /* Gets query for [[TgChat]].
    *
    * @return ActiveQuery|TgChatsQuery
    */
   public function getTgChat(){
       return $this->hasOne(TgChats::class, ['id' => 'tg_chat_id']);
   }
   /**
    * Gets query for [[TgChats]].
    *
    * @return ActiveQuery|TgChatsQuery
    */
   public function getTgChats(){
       return $this->hasMany(TgChats::class, ['client_id' => 'id']);
   }
   /**
    * Gets query for [[TgMember]].
    *
    * @return ActiveQuery|TgMembersQuery
    */
   public function getTgMember(){
       return $this->hasOne(TgMembers::class, ['id' => 'tg_member_id']);
   }
   /**
    * Gets query for [[TgPrivateChat]].
    *
    * @return ActiveQuery|TgChatsQuery
    */
   public function getTgPrivateChat(){
       return $this->hasOne(TgChats::class, ['id' => 'tg_private_chat_id']);
   }
 
    /**
     * Gets query for [[Withdrawals]].
     *
     * @return ActiveQuery|WithdrawalsQuery
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