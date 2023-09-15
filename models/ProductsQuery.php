<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Products]].
 *
 * @see Products
 */
class ProductsQuery extends \yii\db\ActiveQuery{

    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return Products[]|array
     */
    public function all($db = null) :Products|array{
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return Products|array|null
     */
    public function one($db = null) :Products|array|null{
        return parent::one($db);
    }
}