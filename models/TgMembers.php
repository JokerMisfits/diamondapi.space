<?php

namespace app\models;

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
 * @property int $is_filed Заполнено? 
 * @property Clients[] $clients 
 * @property Orders[] $orders 
 * @property Users[] $users 
 * @property Withdrawals[] $withdrawals 
 */
class TgMembers extends \yii\db\ActiveRecord{
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
            [['tg_user_id', 'is_filed'], 'integer'],
            [['tg_username', 'tg_first_name', 'tg_last_name', 'tg_bio', 'tg_type'], 'string', 'max' => 255],
            [['tg_user_id'], 'unique']
        ];
    }

    public function attributeLabels(){
        return [
            'id' => 'ID',
            'tg_user_id' => 'ID пользователя в telegram',
            'tg_username' => 'Ник пользователя в telegram',
            'tg_first_name' => 'Имя пользователя в telegram',
            'tg_last_name' => 'Фамилия пользователя в telegram',
            'tg_bio' => 'Описания пользователя в telegram',
            'tg_type' => 'Тип аккаунта telegram',
            'is_filed' => 'Заполнено?'
        ];
    }

   /** 
    * Gets query for [[Clients]]. 
    * 
    * @return \yii\db\ActiveQuery|ClientsQuery 
    */ 
    public function getClients(){ 
        return $this->hasMany(Clients::class, ['tg_member_id' => 'id']); 
    } 
  
    /** 
     * Gets query for [[Orders]]. 
     * 
     * @return \yii\db\ActiveQuery|OrdersQuery 
     */ 
    public function getOrders(){ 
        return $this->hasMany(Orders::class, ['tg_member_id' => 'id']); 
    } 
  
    /** 
     * Gets query for [[Users]]. 
     * 
     * @return \yii\db\ActiveQuery|UsersQuery 
     */ 
    public function getUsers(){ 
        return $this->hasMany(Users::class, ['tg_member_id' => 'id']); 
    } 
  
    /** 
     * Gets query for [[Withdrawals]]. 
     * 
     * @return \yii\db\ActiveQuery|WithdrawalsQuery 
     */ 
    public function getWithdrawals(){ 
        return $this->hasMany(Withdrawals::class, ['tg_member_id' => 'id']); 
    } 

    /**
     * {@inheritdoc}
     * @return TgMembersQuery the active query used by this AR class.
     */
    public static function find(){
        return new TgMembersQuery(get_called_class());
    }
}