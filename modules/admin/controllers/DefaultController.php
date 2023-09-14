<?php

namespace app\modules\admin\controllers;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends AppAdminController{

    public function beforeAction($action){
        return parent::beforeAction($action);
    }

    /**
     * Renders the index view for the module
     * @return string|\yii\web\response
     */
    public function actionIndex() : string|\yii\web\response{
        return $this->redirect('/admin/user');
        //return $this->render('index');
    }
}
