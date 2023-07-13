<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "verify_email".
 *
 * @property int $id ID
 * @property string $email Emaill
 * @property string $verify_code Проверочный код
 * @property int $status Статус
 * @property int $user_id ID пользователя 
 * 
 * @property Users $user 
 */
class VerifyEmail extends ActiveRecord{
    /**
     * {@inheritdoc}
     */
    public static function tableName(){
        return 'verify_email';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['email', 'verify_code', 'user_id'], 'required'],
            [['status', 'user_id'], 'integer'],
            [['email'], 'string', 'max' => 255],
            [['verify_code'], 'string', 'max' => 128],
            [['email'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(){
        return [
            'id' => 'ID',
            'email' => 'Email',
            'verify_code' => 'Проверочный код',
            'status' => 'Статус',
            'user_id' => 'ID пользователя'
        ];
    }

   /** 
    * Gets query for [[User]]. 
    * 
    * @return ActiveQuery|UsersQuery 
    */ 
   public function getUser(){ 
       return $this->hasOne(Users::class, ['id' => 'user_id']); 
   }

    /**
     * {@inheritdoc}
     * @return VerifyEmailQuery the active query used by this AR class.
     */
    public static function find(){
        return new VerifyEmailQuery(get_called_class());
    }
}