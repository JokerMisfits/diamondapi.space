<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[BotTexts]].
 *
 * @see BotTexts
 */
class BotTextsQuery extends \yii\db\ActiveQuery{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return BotTexts[]|array
     */
    public function all($db = null) : BotTexts|array{
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return BotTexts|array|null
     */
    public function one($db = null) : BotTexts|array|null{
        return parent::one($db);
    }
}