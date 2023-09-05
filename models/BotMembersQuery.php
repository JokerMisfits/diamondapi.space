<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[BotMembers]].
 *
 * @see BotMembers
 */
class BotMembersQuery extends \yii\db\ActiveQuery{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return BotMembers[]|array
     */
    public function all($db = null){
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return BotMembers|array|null
     */
    public function one($db = null){
        return parent::one($db);
    }
}