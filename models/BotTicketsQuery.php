<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[BotTickets]].
 *
 * @see BotTickets
 */
class BotTicketsQuery extends \yii\db\ActiveQuery{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return BotTickets[]|array
     */
    public function all($db = null){
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return BotTickets|array|null
     */
    public function one($db = null){
        return parent::one($db);
    }
}