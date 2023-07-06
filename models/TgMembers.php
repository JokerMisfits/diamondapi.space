<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "tg_members".
 *
 * @property int $id ID
 * @property int $tg_user_id ID пользователя в telegram
 * @property string|null $tg_username Ник пользователя в telegram
 * @property string|null $tg_first_name Имя пользователя в telegram
 * @property string|null $tg_last_name Фамилия пользователя в telegram
 * @property string|null $tg_bio Описания пользователя в telegram
 * @property string|null $tg_type Тип аккаунта telegram
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
            [['tg_username', 'tg_first_name', 'tg_last_name', 'tg_bio', 'tg_type'], 'string', 'max' => 255],
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
            'tg_username' => 'Ник пользователя в telegram',
            'tg_first_name' => 'Имя пользователя в telegram',
            'tg_last_name' => 'Фамилия пользователя в telegram',
            'tg_bio' => 'Описания пользователя в telegram',
            'tg_type' => 'Тип аккаунта telegram'
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