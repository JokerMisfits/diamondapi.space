<?php

namespace app\models;

/**
 * This is the model class for table "bot_configs".
 *
 * @property int $id ID
 * @property string $shop Название магазина
 * @property int $owner //tg_user_id
 * @property string|null $admins Json строка администраторов
 * @property string|null $moders Json строка модераторов
 * @property string|null $techNotify Json строка саппортов
 * @property string $chatLink Ссылка на чат
 * @property string $chatId ID чата
 * @property string|null $privateChatLink Ссылка на приватный чат
 * @property string|null $privateChatId ID приватного чата
 * @property string|null $prices Json строка цен
 * @property string $baseRole //ИЗМЕНИТЬ НАЗВАНИЕ НА defaultRoleName
 * @property string|null $options Json строка опций
 * @property string $lastChange Последнее изминение
 * @property int $client_id ID клиента
 *
 * @property Clients $client
 */
class BotConfigs extends \yii\db\ActiveRecord{

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public static function tableName() : string{
        return 'bot_configs';
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() : array{
        return [
            [['shop', 'owner', 'chatLink', 'chatId', 'baseRole', 'client_id'], 'required'],
            [['owner', 'client_id'], 'integer'],
            [['admins', 'moders', 'techNotify', 'prices', 'options'], 'string'],
            [['lastChange'], 'safe'],
            [['shop', 'chatLink', 'chatId', 'privateChatLink', 'privateChatId', 'baseRole'], 'string', 'max' => 255],
            [['shop'], 'unique'],
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
            'owner' => '//tg_user_id',
            'admins' => 'Json строка администраторов',
            'moders' => 'Json строка модераторов',
            'techNotify' => 'Json строка саппортов',
            'chatLink' => 'Ссылка на чат',
            'chatId' => 'ID чата',
            'privateChatLink' => 'Ссылка на приватный чат',
            'privateChatId' => 'ID приватного чата',
            'prices' => 'Json строка цен',
            'baseRole' => '//ИЗМЕНИТЬ НАЗВАНИЕ НА defaultRoleName',
            'options' => 'Json строка опций',
            'lastChange' => 'Последнее изминение',
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
     * @return BotConfigsQuery the active query used by this AR class.
     */
    public static function find() : BotConfigsQuery{
        return new BotConfigsQuery(get_called_class());
    }
}