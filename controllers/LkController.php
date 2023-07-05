<?php

namespace app\controllers;

use Yii;
use app\models\users;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;


class LkController extends AppController{

    public $layout = 'lk';

    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ],
        ];
    }

    public function beforeAction($action){
        if($action->id == 'verify'){
            if(Yii::$app->request->isPost){
                $params = Yii::$app->request->post();
                if(isset($params['target']) && isset($params['csrf'])){
                    if(Yii::$app->session->get('csrf') == $params['csrf']){
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
        elseif($action->id == 'confirmation'){
            if(Yii::$app->request->isPost){
                $params = Yii::$app->request->post();
                if(isset($params['target']) && isset($params['csrf'])){
                    if(Yii::$app->session->get('csrf') == $params['csrf']){
                        if($params['target'] == 'telegram'){
                            if(is_numeric($params['tg_user_id'])){
                                if(isset($_SERVER['TG_VERIFY'])){
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
                        elseif($params['target'] == 'phone'){
                            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
                        }
                        elseif($params['target'] == 'email'){
                            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
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
            else{
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403); 
            }
        }
        else{
            return parent::beforeAction($action);
        }
    }

    public function actionIndex(){
        $model = new Users();
        $model = $model->findIdentity(Yii::$app->user->identity->id);
        return $this->render('index', [
            'username' => $model->username,
            'tg_user_id' => $model->tg_user_id,
            'email' => $model->email,
            'phone' => $model->phone,
            'csrf' => Yii::$app->session->get('csrf')
        ]);
    }

    public function actionChannel(){
        return $this->render('channel');
    }

    public function actionPayments(){
        return $this->render('payments');
    }

    public function actionSubscriptions(){
        return $this->render('subscriptions');
    }

    public function actionFinance(){
        return $this->render('finance');
    }

    public function actionOptions(){
        return $this->render('options');
    }

    public function actionVerify(){
        $target = Yii::$app->request->post()['target'];
        if($target == 'telegram'){
            $model = new Users();
            $model = $model->findIdentity(Yii::$app->user->identity->id);
            if($model->tg_user_id === NULL){
                return $this->render('verify', [
                    'target' => 'telegram',
                    'token' => md5(uniqid(rand(), true)),
                    'csrf' => Yii::$app->session->get('csrf')
                ]);
            }
            else{
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif($target == 'phone'){
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
        elseif($target == 'email'){
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
        else{
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
    }

    public function actionConfirmation(){
        $params = Yii::$app->request->post();
        if($params['target'] == 'telegram'){
            try{
                $result = file_get_contents('https://api.telegram.org/bot' . $_SERVER['TG_VERIFY'] . '/getChat?chat_id=' . $params['tg_user_id']);
            }
            catch(\Exception|\Throwable $e){
                if($e->getCode() == 2){
                    Yii::$app->getSession()->setFlash('error', 'Пользователь не найден!');
                    $model = new Users();
                    $model = $model->findIdentity(Yii::$app->user->identity->id);
                    return $this->render('verify', [
                        'target' => 'telegram',
                        'token' => md5(uniqid(rand(), true)),
                        'csrf' => Yii::$app->session->get('csrf')
                    ]);
                }
                else{
                    throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
            }
            $result = json_decode($result);
            if(isset($result->ok) && $result->ok == true){
                $result = $result->result;
                if(isset($result->bio)){
                    if(stripos($result->bio, $params['token']) === false){
                        AppController::debug([$result->bio, $params['token']], 1);
                        Yii::$app->getSession()->setFlash('error', 'Пункт 4 не выполнен!<br>Необходимо повторить пункт 3 и 4');
                        $model = new Users();
                        $model = $model->findIdentity(Yii::$app->user->identity->id);
                        return $this->render('verify', [
                            'target' => 'telegram',
                            'token' => md5(uniqid(rand(), true)),
                            'csrf' => Yii::$app->session->get('csrf')
                        ]);
                    }
                    else{
                        AppController::debug(['ВСЕ ЗБС', $result], 1); //todo сохранить модель
                    }
                }
                else{
                    Yii::$app->getSession()->setFlash('error', 'Описание пользователя недоступно!');
                    $model = new Users();
                    $model = $model->findIdentity(Yii::$app->user->identity->id);
                    return $this->render('verify', [
                        'target' => 'telegram',
                        'token' => md5(uniqid(rand(), true)),
                        'csrf' => Yii::$app->session->get('csrf')
                    ]);
                }
            }
            else{
                Yii::$app->getSession()->setFlash('error', 'Пользователь не найден!');
                $model = new Users();
                $model = $model->findIdentity(Yii::$app->user->identity->id);
                return $this->render('verify', [
                    'target' => 'telegram',
                    'token' => md5(uniqid(rand(), true)),
                    'csrf' => Yii::$app->session->get('csrf')
                ]);
            }
        }
        elseif($params['target'] == 'phone'){
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
        elseif($params['target'] == 'email'){
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
        else{
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
    }

}