<?php

namespace app\models;

/**
 * This is the model class for table "bot_tickets".
 *
 * @property int $id ID
 * @property string $shop Название магазина
 * @property int $tg_user_id ID пользователя в telegram
 * @property string|null $tag //tg_username
 * @property string|null $tickets //ПОДУМАТЬ как не удалять
 * @property int|null $member_id //CДЕЛАТЬ ОТДЕЛЬНУЮ ТАБЛИЦУ С МЕМБАРАМИ И СВЯЗАТЬ
 * @property int $client_id ID клиента
 *
 * @property Clients $client
 */
class BotTickets extends \yii\db\ActiveRecord{

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public static function tableName() : string{
        return 'bot_tickets';
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() : array{
        return [
            [['shop', 'tg_user_id', 'client_id'], 'required'],
            [['tg_user_id', 'member_id', 'client_id'], 'integer'],
            [['tickets'], 'string'],
            [['shop', 'tag'], 'string', 'max' => 255],
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
            'tg_user_id' => 'ID пользователя в telegram',
            'tag' => '//tg_username',
            'tickets' => '//ПОДУМАТЬ как не удалять',
            'member_id' => '//CДЕЛАТЬ ОТДЕЛЬНУЮ ТАБЛИЦУ С МЕМБАРАМИ И СВЯЗАТЬ',
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
     * @return BotTicketsQuery the active query used by this AR class.
     */
    public static function find() : BotTicketsQuery{
        return new BotTicketsQuery(get_called_class());
    }
}