<?php

namespace app\models;

/**
 * This is the model class for table "orders_complete".
 *
 * @property int $id ID
 * @property string $shop Название магазина
 * @property string $method Платежная система
 * @property string|null $payment_method Способ оплаты
 * @property float $fee Комиссия платежной системы
 * @property string|null $revise Сведения о сверке
 * @property int $order_id ID заказа
 * @property int $client_id ID клиента
 *
 * @property Clients $client
 * @property Orders $order
 */
class OrdersComplete extends \yii\db\ActiveRecord{

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public static function tableName() : string{
        return 'orders_complete';
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() : array{
        return [
            [['shop', 'method', 'fee', 'order_id', 'client_id'], 'required'],
            [['fee'], 'number'],
            [['revise'], 'safe'],
            [['order_id', 'client_id'], 'integer'],
            [['shop', 'payment_method'], 'string', 'max' => 255],
            [['method'], 'string', 'max' => 25],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::class, 'targetAttribute' => ['client_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::class, 'targetAttribute' => ['order_id' => 'id']]
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function attributeLabels() : array{
        return [
            'id' => 'ID',
            'shop' => 'Название магазина',
            'method' => 'Платежная система',
            'payment_method' => 'Способ оплаты',
            'fee' => 'Комиссия платежной системы',
            'revise' => 'Сведения о сверке',
            'order_id' => 'ID заказа',
            'client_id' => 'ID клиента'
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery|ClientsQuery
     */
    public function getClient() : \yii\db\ActiveQuery|ClientsQuery{
        return $this->hasOne(Clients::class, ['id' => 'client_id']);
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery|OrdersQuery
     */
    public function getOrder() : \yii\db\ActiveQuery|OrdersQuery{
        return $this->hasOne(Orders::class, ['id' => 'order_id']);
    }

    /**
     * {@inheritdoc}
     *
     * @return OrdersCompleteQuery the active query used by this AR class.
     */
    public static function find() : OrdersCompleteQuery{
        return new OrdersCompleteQuery(get_called_class());
    }
}