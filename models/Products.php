<?php

namespace app\models;

/**
 * This is the model class for table "products".
 *
 * @property int $id ID
 * @property string $name Название позиции
 * @property float $price Стоимость
 * @property int $access_days Количество дней
 * @property int $discount Процент скидки
 * @property int $status Статус позиции
 * @property int $client_id ID клиента
 *
 * @property Clients $client
 */
class Products extends \yii\db\ActiveRecord{

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public static function tableName() : string{
        return 'products';
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() :array{
        return [
            [['name', 'client_id'], 'required'],
            [['price'], 'number'],
            [['access_days', 'discount', 'status', 'client_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::class, 'targetAttribute' => ['client_id' => 'id']]
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
            'name' => 'Название позиции',
            'price' => 'Стоимость',
            'access_days' => 'Количество дней',
            'discount' => 'Процент скидки',
            'status' => 'Статус позиции',
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
     * {@inheritdoc}
     *
     * @return ProductsQuery the active query used by this AR class.
     */
    public static function find() : ProductsQuery{
        return new ProductsQuery(get_called_class());
    }
}