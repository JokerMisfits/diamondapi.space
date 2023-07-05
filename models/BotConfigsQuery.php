<?php

namespace app\models;

use yii\db\ActiveQuery;
/**
 * This is the ActiveQuery class for [[BotConfigs]].
 *
 * @see BotConfigs
 */
class BotConfigsQuery extends ActiveQuery{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return BotConfigs[]|array
     */
    public function all($db = null){
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return BotConfigs|array|null
     */
    public function one($db = null){
        return parent::one($db);
    }
}