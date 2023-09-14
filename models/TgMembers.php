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
 * @property int $is_filled Заполнено?
 * @property string $last_change Последнее изминение
 * @property Clients[] $clients
 * @property Orders[] $orders
 * @property Users[] $users
 * @property Withdrawals[] $withdrawals
 */
class TgMembers extends \yii\db\ActiveRecord{
    
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public static function tableName() : string{
        return 'tg_members';
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function rules() : array{
        return [
            [['tg_user_id'], 'required'],
            [['tg_user_id', 'is_filled'], 'integer'],
            [['last_change'], 'safe'],
            [['tg_username', 'tg_first_name', 'tg_last_name', 'tg_bio', 'tg_type'], 'string', 'max' => 255],
            [['tg_user_id'], 'unique']
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
            'tg_user_id' => 'ID пользователя в telegram',
            'tg_username' => 'Ник пользователя в telegram',
            'tg_first_name' => 'Имя пользователя в telegram',
            'tg_last_name' => 'Фамилия пользователя в telegram',
            'tg_bio' => 'Описание пользователя в telegram',
            'tg_type' => 'Тип аккаунта telegram',
            'is_filled' => 'Заполнено?',
            'last_change' => 'Последнее изминение',
        ];
    }

   /** 
    * Gets query for [[Clients]]. 
    * 
    * @return \yii\db\ActiveQuery|ClientsQuery 
    */ 
    public function getClients() : \yii\db\ActiveQuery|ClientsQuery{ 
        return $this->hasMany(Clients::class, ['tg_member_id' => 'id']); 
    } 
  
    /** 
     * Gets query for [[Orders]]. 
     * 
     * @return \yii\db\ActiveQuery|OrdersQuery 
     */ 
    public function getOrders() : \yii\db\ActiveQuery|OrdersQuery{ 
        return $this->hasMany(Orders::class, ['tg_member_id' => 'id']); 
    } 
  
    /** 
     * Gets query for [[Users]]. 
     * 
     * @return \yii\db\ActiveQuery|UsersQuery 
     */ 
    public function getUsers() : \yii\db\ActiveQuery|UsersQuery{ 
        return $this->hasMany(Users::class, ['tg_member_id' => 'id']); 
    } 
  
    /** 
     * Gets query for [[Withdrawals]]. 
     * 
     * @return \yii\db\ActiveQuery|WithdrawalsQuery 
     */ 
    public function getWithdrawals() : \yii\db\ActiveQuery|WithdrawalsQuery{ 
        return $this->hasMany(Withdrawals::class, ['tg_member_id' => 'id']); 
    } 

    /**
     * {@inheritdoc}
     * @return TgMembersQuery the active query used by this AR class.
     */
    public static function find() : TgMembersQuery{
        return new TgMembersQuery(get_called_class());
    }
}