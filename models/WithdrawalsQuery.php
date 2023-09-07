<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Withdrawals]].
 *
 * @see Withdrawals
 */
class WithdrawalsQuery extends \yii\db\ActiveQuery{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return Withdrawals[]|array
     */
    public function all($db = null) : Withdrawals|array{
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return Withdrawals|array|null
     */
    public function one($db = null) : Withdrawals|array|null{
        return parent::one($db);
    }
}