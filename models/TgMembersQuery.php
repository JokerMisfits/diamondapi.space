<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[TgMembers]].
 *
 * @see TgMembers
 */
class TgMembersQuery extends ActiveQuery{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TgMembers[]|array
     */
    public function all($db = null){
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TgMembers|array|null
     */
    public function one($db = null){
        return parent::one($db);
    }
}
