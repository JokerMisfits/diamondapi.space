<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "orders".
 *
 * @property int $id ID
 * @property int $tg_user_id ID пользователя в telegram
 * @property int $status Статус платежа
 * @property int $count Сумма платежа
 * @property string $method Платежная система
 * @property string $shop Название магазина
 * @property int $access_days Количество дней
 * @property string|null $currency Валюта 
 * @property float|null $count_in_currency Сумма в валюте 
 * @property int $is_test Тест? 
 * @property float $commission Комиссия платежа 
 * @property string $created_time Дата создания заказа
 * @property string|null $resulted_time Дата оплаты заказа
 * @property string|null $position_name Название товара
 * @property string $web_app_query_id ID web_app окна в telegram
 * @property string|null $paypal_order_id Номер заказа в платежной системе PayPal
 * @property int|null $tg_member_id ID tg_member
 * @property int $client_id ID клиента
 *
 * @property Clients $client
 * @property OrdersComplete[] $ordersCompletes
 * @property TgMembers $tgMember 
 */
class Orders extends ActiveRecord{
    /**
     * {@inheritdoc}
     */
    public static function tableName(){
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['tg_user_id', 'count', 'method', 'shop', 'access_days', 'client_id'], 'required'],
            [['tg_user_id', 'status', 'count', 'access_days', 'is_test', 'tg_member_id', 'client_id'], 'integer'],
            [['count_in_currency', 'commission'], 'number'],
            [['created_time', 'resulted_time'], 'safe'],
            [['method'], 'string', 'max' => 25],
            [['shop', 'currency', 'position_name'], 'string', 'max' => 255],
            [['web_app_query_id'], 'string', 'max' => 64],
            [['paypal_order_id'], 'string', 'max' => 40],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::class, 'targetAttribute' => ['client_id' => 'id']],
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
            'status' => 'Статус платежа',
            'count' => 'Сумма платежа',
            'method' => 'Платежная система',
            'shop' => 'Название магазина',
            'access_days' => 'Количество дней',
            'currency' => 'Валюта', 
            'count_in_currency' => 'Сумма в валюте', 
            'is_test' => 'Тест?', 
            'commission' => 'Комиссия платежа',
            'created_time' => 'Дата создания заказа',
            'resulted_time' => 'Дата оплаты заказа',
            'position_name' => 'Название товара',
            'web_app_query_id' => 'ID web_app окна в telegram',
            'paypal_order_id' => 'Номер заказа в платежной системе PayPal',
            'tg_member_id' => 'ID tg_member',
            'client_id' => 'ID клиента'
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return ActiveQuery|ClientsQuery
     */
    public function getClient(){
        return $this->hasOne(Clients::class, ['id' => 'client_id']);
    }

    /**
     * Gets query for [[OrdersCompletes]].
     *
     * @return ActiveQuery|OrdersCompleteQuery
     */
    public function getOrdersCompletes(){
        return $this->hasMany(OrdersComplete::class, ['order_id' => 'id']);
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
     * {@inheritdoc}
     * @return OrdersQuery the active query used by this AR class.
     */
    public static function find(){
        return new OrdersQuery(get_called_class());
    }
}