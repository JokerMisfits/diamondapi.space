<?php

namespace app\models;

/**
 * This is the model class for table "bot_members".
 *
 * @property int $id ID
 * @property string $shop Название магазина
 * @property int $memberId //tg_user_id
 * @property string $username telegram username
 * @property string $type Тип подписки
 * @property string $expires Когда истекает
 * @property int $client_id ID клиента
 *
 * @property Clients $client
 */
class BotMembers extends \yii\db\ActiveRecord{

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public static function tableName() : string{
        return 'bot_members';
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() : array{
        return [
            [['shop', 'memberId', 'username', 'type', 'client_id'], 'required'],
            [['memberId', 'client_id'], 'integer'],
            [['expires'], 'safe'],
            [['shop', 'username'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 20],
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
            'shop' => 'Название магазина',
            'memberId' => '//tg_user_id',
            'username' => 'telegram username',
            'type' => 'Тип подписки',
            'expires' => 'Когда истекает',
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
     * @return BotMembersQuery the active query used by this AR class.
     */
    public static function find() : BotMembersQuery{
        return new BotMembersQuery(get_called_class());
    }
}