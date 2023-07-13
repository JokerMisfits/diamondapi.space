<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[VerifyEmail]].
 *
 * @see VerifyEmail
 */
class VerifyEmailQuery extends ActiveQuery{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return VerifyEmail[]|array
     */
    public function all($db = null){
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return VerifyEmail|array|null
     */
    public function one($db = null){
        return parent::one($db);
    }
}