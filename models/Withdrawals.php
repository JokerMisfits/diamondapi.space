<?php

namespace app\models;

/**
 * This is the model class for table "withdrawals".
 *
 * @property int $id ID
 * @property string $shop Название магазина
 * @property int $count Сумма
 * @property int $status Статус вывода ДС
 * @property int $is_test Тест? 
 * @property int $commission Комиссия 
 * @property string $card_number Номер карты 
 * @property string|null $comment Комментарий 
 * @property string $created_time Дата создания заявки на вывод ДС
 * @property string|null $confirmed_time Дата подтверждения заявки от клиента
 * @property string|null $resulted_time Дата вывода ДС
 * @property string $confirmation_link Ссылка для подтверждения
 * @property int $tg_member_id ID tg_member
 * @property int $client_id ID клиента
 *
 * @property Clients $client
 */
class Withdrawals extends \yii\db\ActiveRecord{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName(){
        return 'withdrawals';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(){
        return [
            [['shop', 'count', 'card_number', 'confirmation_link', 'tg_member_id', 'client_id'], 'required'],
            [['count', 'status', 'is_test', 'commission', 'tg_member_id', 'client_id'], 'integer'],
            [['comment'], 'string'],
            [['created_time', 'confirmed_time', 'resulted_time'], 'safe'],
            [['shop', 'confirmation_link'], 'string', 'max' => 255],
            [['card_number'], 'string', 'max' => 19],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::class, 'targetAttribute' => ['client_id' => 'id']],
            [['tg_member_id'], 'exist', 'skipOnError' => true, 'targetClass' => TgMembers::class, 'targetAttribute' => ['tg_member_id' => 'id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(){
        return [
            'id' => 'ID',
            'shop' => 'Название магазина',
            'count' => 'Сумма', 
            'status' => 'Статус вывода ДС',
            'is_test' => 'Тест?', 
            'commission' => 'Комиссия', 
            'card_number' => 'Номер карты', 
            'comment' => 'Комментарий',
            'created_time' => 'Дата создания заявки на вывод ДС',
            'confirmed_time' => 'Дата подтверждения заявки от клиента',
            'resulted_time' => 'Дата вывода ДС',
            'confirmation_link' => 'Ссылка для подтверждения',
            'tg_member_id' => 'ID tg_member',
            'client_id' => 'ID клиента'
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery|ClientsQuery
     */
    public function getClient(){
        return $this->hasOne(Clients::class, ['id' => 'client_id']);
    }

   /** 
    * Gets query for [[TgMember]]. 
    * 
    * @return \yii\db\ActiveQuery|TgMembersQuery
    */ 
   public function getTgMember(){ 
       return $this->hasOne(TgMembers::class, ['id' => 'tg_member_id']); 
   }

    /**
     * {@inheritdoc}
     * @return WithdrawalsQuery the active query used by this AR class.
     */
    public static function find(){
        return new WithdrawalsQuery(get_called_class());
    }
}