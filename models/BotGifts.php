<?php

namespace app\models;

/**
 * This is the model class for table "bot_gifts".
 *
 * @property int $id ID
 * @property string $giftCode Подарочный код
 * @property int $days Количество дней
 * @property string $expires Когда истекает
 * @property int $count Оставшееся количество использований
 * @property string $shop Название маагазина
 * @property int $client_id ID клиента
 *
 * @property Clients $client
 */
class BotGifts extends \yii\db\ActiveRecord{

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public static function tableName() : string{
        return 'bot_gifts';
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() : array{
        return [
            [['giftCode', 'shop', 'client_id'], 'required'],
            [['days', 'count', 'client_id'], 'integer'],
            [['expires'], 'safe'],
            [['giftCode', 'shop'], 'string', 'max' => 255],
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
            'giftCode' => 'Подарочный код',
            'days' => 'Количество дней',
            'expires' => 'Когда истекает',
            'count' => 'Оставшееся количество использований',
            'shop' => 'Название маагазина',
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
     * @return BotGiftsQuery the active query used by this AR class.
     */
    public static function find() : BotGiftsQuery{
        return new BotGiftsQuery(get_called_class());
    }
}