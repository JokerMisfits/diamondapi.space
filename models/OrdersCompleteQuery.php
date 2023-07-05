<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[OrdersComplete]].
 *
 * @see OrdersComplete
 */
class OrdersCompleteQuery extends ActiveQuery{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return OrdersComplete[]|array
     */
    public function all($db = null){
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return OrdersComplete|array|null
     */
    public function one($db = null){
        return parent::one($db);
    }
}