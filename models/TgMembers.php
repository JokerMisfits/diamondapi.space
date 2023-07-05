<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "tg_members".
 *
 * @property int $id ID
 * @property int $tg_user_id ID пользователя в telegram
 * @property string|null $tg_username Имя пользователя в telegram
 */
class TgMembers extends ActiveRecord{
    /**
     * {@inheritdoc}
     */
    public static function tableName(){
        return 'tg_members';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['tg_user_id'], 'required'],
            [['tg_user_id'], 'integer'],
            [['tg_username'], 'string', 'max' => 255],
            [['tg_user_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(){
        return [
            'id' => 'ID',
            'tg_user_id' => 'ID пользователя в telegram',
            'tg_username' => 'Имя пользователя в telegram',
        ];
    }

    /**
     * {@inheritdoc}
     * @return TgMembersQuery the active query used by this AR class.
     */
    public static function find(){
        return new TgMembersQuery(get_called_class());
    }
}