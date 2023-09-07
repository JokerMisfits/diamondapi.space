<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[BotConfigs]].
 *
 * @see BotConfigs
 */
class BotConfigsQuery extends \yii\db\ActiveQuery{

    /**
     * {@inheritdoc}
     *
     * @return BotConfigs[]|array
     */
    public function all($db = null) : BotConfigs|array{
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return BotConfigs|array|null
     */
    public function one($db = null) : BotConfigs|array|null{
        return parent::one($db);
    }
}