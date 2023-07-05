<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[BotMembers]].
 *
 * @see BotMembers
 */
class BotMembersQuery extends ActiveQuery{
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