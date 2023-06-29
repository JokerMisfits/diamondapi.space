<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $id ID
 * @property int $tg_user_id ID пользователя в telegram
 * @property int $status Статус платежа
 * @property int $count Сумма платежа
 * @property string $method Платежная система
 * @property string $shop Название магазина
 * @property string|null $position_name Название товара
 * @property int $access_days Количество дней
 * @property string $created_time Дата создания заказа
 * @property string|null $resulted_time Дата оплаты заказа
 * @property int $is_test Тест?
 * @property string $web_app_query_id ID web_app окна в telegram
 * @property string|null $currency Валюта
 * @property float|null $count_in_currency Сумма в валюте
 * @property float $commission Комиссия платежа
 * @property string|null $paypal_order_id Номер заказа в платежной системе PayPal
 * @property int $client_id ID клиента
 *
 * @property Clients $client
 * @property OrdersComplete[] $ordersCompletes
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tg_user_id', 'count', 'method', 'shop', 'access_days', 'client_id'], 'required'],
            [['tg_user_id', 'status', 'count', 'access_days', 'is_test', 'client_id'], 'integer'],
            [['created_time', 'resulted_time'], 'safe'],
            [['count_in_currency', 'commission'], 'number'],
            [['method'], 'string', 'max' => 25],
            [['shop', 'position_name', 'currency'], 'string', 'max' => 255],
            [['web_app_query_id'], 'string', 'max' => 64],
            [['paypal_order_id'], 'string', 'max' => 40],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::class, 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tg_user_id' => 'ID пользователя в telegram',
            'status' => 'Статус платежа',
            'count' => 'Сумма платежа',
            'method' => 'Платежная система',
            'shop' => 'Название магазина',
            'position_name' => 'Название товара',
            'access_days' => 'Количество дней',
            'created_time' => 'Дата создания заказа',
            'resulted_time' => 'Дата оплаты заказа',
            'is_test' => 'Тест?',
            'web_app_query_id' => 'ID web_app окна в telegram',
            'currency' => 'Валюта',
            'count_in_currency' => 'Сумма в валюте',
            'commission' => 'Комиссия платежа',
            'paypal_order_id' => 'Номер заказа в платежной системе PayPal',
            'client_id' => 'ID клиента',
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery|ClientsQuery
     */
    public function getClient()
    {
        return $this->hasOne(Clients::class, ['id' => 'client_id']);
    }

    /**
     * Gets query for [[OrdersCompletes]].
     *
     * @return \yii\db\ActiveQuery|OrdersCompleteQuery
     */
    public function getOrdersCompletes()
    {
        return $this->hasMany(OrdersComplete::class, ['order_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return OrdersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrdersQuery(get_called_class());
    }
}
