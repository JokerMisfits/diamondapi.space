<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\ForbiddenHttpException;

class SiteController extends AppController{

    public function beforeAction($action){
        if($action->id == 'verify'){
            $params = Yii::$app->request->get();
            if(isset($params['user']) && isset($params['id']) && isset($params['token']) && isset($params['hash'])){
                if(md5($_SERVER['API_KEY_0'] . $params['user'] . $params['id'] . $params['token'] . $_SERVER['API_KEY_1']) == $params['hash']){
                    return parent::beforeAction($action);
                }
                else{
                    throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
            }
            else{
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        else{
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
    }

    /**
     * Displays homepage.
     *
     * @return string|Response
     */
    public function actionVerify(){
        Yii::$app->getSession()->setFlash('success', 'Почтовый адрес подтвержден.');
        return $this->redirect(['lk/index']);
    }

}