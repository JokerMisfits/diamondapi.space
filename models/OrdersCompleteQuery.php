<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[OrdersComplete]].
 *
 * @see OrdersComplete
 */
class OrdersCompleteQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return OrdersComplete[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return OrdersComplete|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
