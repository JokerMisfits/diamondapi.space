<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TgChats]].
 *
 * @see TgChats
 */
class TgChatsQuery extends \yii\db\ActiveQuery{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return TgChats[]|array
     */
    public function all($db = null) : TgChats|array{
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return TgChats|array|null
     */
    public function one($db = null) : TgChats|array|null{
        return parent::one($db);
    }
}