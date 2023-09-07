<?php

namespace app\models;

/**
 * This is the model class for table "tg_chats".
 *
 * @property int $id ID
 * @property string $tg_chat_id ID чата в telegram
 * @property string $tg_chat_title Title чата в telegram
 * @property string $tg_chat_type Type чата в telegram
 * @property string|null $tg_chat_description Description чата в telegram
 * @property string $tg_chat_invite_link Пригласительная ссылка в чат telegram
 * @property string $last_change Последнее изменение
 * @property int|null $client_id ID clients
 *
 * @property Clients $client
 * @property Clients $clients
 * @property Clients $clients0
 */
class TgChats extends \yii\db\ActiveRecord{
    
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public static function tableName() : string{
        return 'tg_chats';
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() : array{
        return [
            [['tg_chat_id', 'tg_chat_title', 'tg_chat_type', 'tg_chat_invite_link'], 'required'],
            [['tg_chat_description'], 'string'],
            [['last_change'], 'safe'],
            [['client_id'], 'integer'],
            [['tg_chat_id', 'tg_chat_title', 'tg_chat_type', 'tg_chat_invite_link'], 'string', 'max' => 255],
            [['tg_chat_id'], 'unique'],
            [['tg_chat_invite_link'], 'unique'],
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
            'tg_chat_id' => 'ID чата в telegram',
            'tg_chat_title' => 'Title чата в telegram',
            'tg_chat_type' => 'Type чата в telegram',
            'tg_chat_description' => 'Description чата в telegram',
            'tg_chat_invite_link' => 'Пригласительная ссылка в чат telegram',
            'last_change' => 'Последнее изменение',
            'client_id' => 'ID clients'
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
     * Gets query for [[Clients]].
     *
     * @return \yii\db\ActiveQuery|ClientsQuery
     */
    public function getClients() : \yii\db\ActiveQuery|ClientsQuery{
        return $this->hasOne(Clients::class, ['tg_chat_id' => 'id']);
    }

    /**
     * Gets query for [[Clients0]].
     *
     * @return \yii\db\ActiveQuery|ClientsQuery
     */
    public function getClients0() : \yii\db\ActiveQuery|ClientsQuery{
        return $this->hasOne(Clients::class, ['tg_private_chat_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     *
     * @return TgChatsQuery the active query used by this AR class.
     */
    public static function find() : TgChatsQuery{
        return new TgChatsQuery(get_called_class());
    }
}