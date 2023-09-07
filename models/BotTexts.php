<?php

namespace app\models;

/**
 * This is the model class for table "bot_texts".
 *
 * @property int $id
 * @property int $type
 * @property string $name
 * @property string $value
 * @property int $client_id
 *
 * @property Clients $client
 */
class BotTexts extends \yii\db\ActiveRecord{

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public static function tableName() : string{
        return 'bot_texts';
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() : array{
        return [
            [['type', 'name', 'value', 'client_id'], 'required'],
            [['type', 'client_id'], 'integer'],
            [['value'], 'string'],
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
            'type' => 'Type',
            'name' => 'Name',
            'value' => 'Value',
            'client_id' => 'Client ID'
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
     * @return BotTextsQuery the active query used by this AR class.
     */
    public static function find() : BotTextsQuery{
        return new BotTextsQuery(get_called_class());
    }
}