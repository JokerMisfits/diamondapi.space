<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "withdrawals".
 *
 * @property int $id ID
 * @property int $tg_user_id ID пользователя в telegram
 * @property string|null $shop Название магазина
 * @property int $status Статус вывода ДС
 * @property string $created_time Дата создания заявки на вывод ДС
 * @property string|null $confirmed_time Дата подтверждения заявки от клиента
 * @property string|null $resulted_time Дата вывода ДС
 * @property int $is_test Тест?
 * @property int $count Сумма
 * @property int $commission Комиссия
 * @property string $confirmation_link Ссылка для подтверждения
 * @property string $card_number Номер карты
 * @property string $comment Комментарий
 * @property int $client_id ID клиента
 *
 * @property Clients $client
 */
class Withdrawals extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'withdrawals';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tg_user_id', 'count', 'card_number', 'comment', 'client_id'], 'required'],
            [['tg_user_id', 'status', 'is_test', 'count', 'commission', 'client_id'], 'integer'],
            [['created_time', 'confirmed_time', 'resulted_time'], 'safe'],
            [['comment'], 'string'],
            [['shop', 'confirmation_link'], 'string', 'max' => 255],
            [['card_number'], 'string', 'max' => 19],
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
            'shop' => 'Название магазина',
            'status' => 'Статус вывода ДС',
            'created_time' => 'Дата создания заявки на вывод ДС',
            'confirmed_time' => 'Дата подтверждения заявки от клиента',
            'resulted_time' => 'Дата вывода ДС',
            'is_test' => 'Тест?',
            'count' => 'Сумма',
            'commission' => 'Комиссия',
            'confirmation_link' => 'Ссылка для подтверждения',
            'card_number' => 'Номер карты',
            'comment' => 'Комментарий',
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
     * {@inheritdoc}
     * @return WithdrawalsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WithdrawalsQuery(get_called_class());
    }
}
