<?php

namespace app\controllers;

class MailController extends AppController{

    /**
     * {@inheritdoc}
     * @return bool
     * @throws \yii\web\BadRequestHttpException|\yii\web\ForbiddenHttpException
     */
    public function beforeAction($action) : bool{
        if($action->id == 'verify'){
            $params = \Yii::$app->request->get();
            if(array_key_exists('user', $params) && array_key_exists('id', $params) && array_key_exists('token', $params) && array_key_exists('hash', $params)){
                if(md5($_SERVER['API_KEY_0'] . $params['user'] . $params['id'] . $params['token'] . $_SERVER['API_KEY_1']) == $params['hash']){
                    return parent::beforeAction($action);
                }
            }
        }
        throw new \yii\web\ForbiddenHttpException('Доступ запрещен.', 403);
    }

    /**
     *
     * @return \yii\web\Response
     */
    public function actionVerify() : \yii\web\Response{
        \Yii::$app->getSession()->addFlash('success', 'Почтовый адрес подтвержден.');
        return $this->redirect(['lk/index']);
    }

}