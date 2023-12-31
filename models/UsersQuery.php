<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Users]].
 *
 * @see Users
 */
class UsersQuery extends \yii\db\ActiveQuery{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return Users[]|array
     */
    public function all($db = null) : Users|array{
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return Users|array|null
     */
    public function one($db = null) : Users|array|null{
        return parent::one($db);
    }
}