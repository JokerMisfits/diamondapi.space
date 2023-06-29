<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Clients]].
 *
 * @see Clients
 */
class ClientsQuery extends ActiveQuery{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Clients[]|array
     */
    public function all($db = null){
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Clients|array|null
     */
    public function one($db = null){
        return parent::one($db);
    }
}
