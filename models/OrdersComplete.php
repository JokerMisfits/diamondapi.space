<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders_complete".
 *
 * @property int $id ID
 * @property string $shop Название магазина
 * @property int $count Сумма платежа
 * @property string $method Платежная система
 * @property string|null $payment_method Способ оплаты
 * @property float $fee Комиссия платежной системы
 * @property int $order_id ID заказа
 * @property int $client_id ID клиента
 *
 * @property Clients $client
 * @property Orders $order
 */
class OrdersComplete extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders_complete';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shop', 'count', 'method', 'fee', 'order_id', 'client_id'], 'required'],
            [['count', 'order_id', 'client_id'], 'integer'],
            [['fee'], 'number'],
            [['shop', 'payment_method'], 'string', 'max' => 255],
            [['method'], 'string', 'max' => 25],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::class, 'targetAttribute' => ['client_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop' => 'Название магазина',
            'count' => 'Сумма платежа',
            'method' => 'Платежная система',
            'payment_method' => 'Способ оплаты',
            'fee' => 'Комиссия платежной системы',
            'order_id' => 'ID заказа',
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
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery|OrdersQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Orders::class, ['id' => 'order_id']);
    }

    /**
     * {@inheritdoc}
     * @return OrdersCompleteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrdersCompleteQuery(get_called_class());
    }
}
