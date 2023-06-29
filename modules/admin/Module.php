<?php

namespace app\modules\admin;

use yii\base\Module AS yiiBaseModule;

/**
 * admin module definition class
 */
class Module extends yiiBaseModule{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * {@inheritdoc}
     */
    public function init(){
        parent::init();

        // custom initialization code goes here
    }
}
