<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
class BotTickets extends ActiveRecord{
    /**
     * {@inheritdoc}
     */
    public static function tableName(){
        return 'bot_tickets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['shop', 'tg_user_id', 'client_id'], 'required'],
            [['tg_user_id', 'member_id', 'client_id'], 'integer'],
            [['tickets'], 'string'],
            [['shop', 'tag'], 'string', 'max' => 255],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::class, 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(){
        return [
            'id' => 'ID',
            'shop' => 'Название магазина',
            'tg_user_id' => 'ID пользователя в telegram',
            'tag' => '//tg_username',
            'tickets' => '//ПОДУМАТЬ как не удалять',
            'member_id' => '//CДЕЛАТЬ ОТДЕЛЬНУЮ ТАБЛИЦУ С МЕМБАРАМИ И СВЯЗАТЬ',
            'client_id' => 'ID клиента',
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
     * {@inheritdoc}
     * @return BotTicketsQuery the active query used by this AR class.
     */
    public static function find(){
        return new BotTicketsQuery(get_called_class());
    }
}