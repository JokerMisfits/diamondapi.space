<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[BotGifts]].
 *
 * @see BotGifts
 */
class BotGiftsQuery extends \yii\db\ActiveQuery{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return BotGifts[]|array
     */
    public function all($db = null){
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return BotGifts|array|null
     */
    public function one($db = null){
        return parent::one($db);
    }
}