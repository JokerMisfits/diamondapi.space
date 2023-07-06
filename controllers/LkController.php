<?php

namespace app\controllers;

use Yii;
use app\models\users;
use app\models\Orders;
use app\models\Clients;
use app\models\TgMembers;
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

        if($action->id == 'channels' || $action->id == 'payments' || $action->id == 'subscriptions' || $action->id == 'finance' || $action->id == 'options'){
            if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id, 'tg-verify')){
                return parent::beforeAction($action);
            }
            else{
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
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
        $model = $model->findOne(['id' => Yii::$app->user->identity->id]);
        return $this->render('index', [
            'username' => $model->username,
            'tg_member_id' => $model->tg_member_id,
            'email' => $model->email,
            'phone' => $model->phone,
            'csrf' => Yii::$app->session->get('csrf')
        ]);
    }

    public function actionChannels(){
        $model = new Clients();
        $model = $model->findAll(['tg_member_id' => Yii::$app->user->identity->tg_member_id]);
        return $this->render('channels', [
            'model' => $model
        ]);
    }

    public function actionPayments(){
        $model = new Orders();
        $model = $model->findAll(['tg_member_id' => Yii::$app->user->identity->tg_member_id, 'status' => 0, 'is_test' => 0]);
        return $this->render('payments',[
            'model' => $model
        ]);
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
            $model = $model->findOne(['id' => Yii::$app->user->identity->id]);
            if($model->tg_member_id === NULL){
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
                    $model = $model->findOne(['id' => Yii::$app->user->identity->id]);
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
                        Yii::$app->getSession()->setFlash('error', 'Пункт 3 не был выполнен.<br>Необходимо повторить пункт 3 и 4 повторно!');
                        $model = new Users();
                        $model = $model->findOne(['id' => Yii::$app->user->identity->id]);
                        return $this->render('verify', [
                            'target' => 'telegram',
                            'token' => md5(uniqid(rand(), true)),
                            'csrf' => Yii::$app->session->get('csrf')
                        ]);
                    }
                    else{
                        $model = new TgMembers;
                        $id = $model->findOne(['tg_user_id' => $params['tg_user_id']]);
                        if(isset($id)){
                            $id = $id['id'];
                            $model = new Users();
                            $userId = $model->findOne(['tg_member_id' => $id]);
                            if(isset($userId)){
                                $userId = $userId['id'];
                                Yii::$app->getSession()->setFlash('error', 'Telegram уже привязан к другому аккаунту.');
                                return $this->render('verify', [
                                    'target' => 'telegram',
                                    'token' => md5(uniqid(rand(), true)),
                                    'csrf' => Yii::$app->session->get('csrf')
                                ]);
                            }
                            else{
                                $transaction = Yii::$app->db->beginTransaction();
                                try{
                                    $sql = "UPDATE users SET tg_member_id = :id WHERE id = :account_id;";
                                    $result = Yii::$app->db->createCommand($sql)
                                        ->bindValue(':id', $id)
                                        ->bindValue(':account_id', Yii::$app->user->identity->id)
                                        ->execute();
                                    if($result !== false){
                                        $authManager = Yii::$app->authManager;
                                        $authManager->assign($authManager->getRole('tg-verify'), Yii::$app->user->identity->id);
                                        $transaction->commit();
                                        $model = new Users();
                                        $model = $model->findOne(['id' => Yii::$app->user->identity->id]);
                                        Yii::$app->getSession()->setFlash('success', 'Telegram успешно подтвержден.');
                                        return $this->render('index', [
                                            'username' => $model->username,
                                            'tg_member_id' => $model->tg_member_id,
                                            'email' => $model->email,
                                            'phone' => $model->phone,
                                            'csrf' => Yii::$app->session->get('csrf')
                                        ]);
                                    }
                                }
                                catch(\Exception|\Throwable $e){
                                    $transaction->rollBack();
                                    Yii::error('LkController Telegram verify ошибка во время обновления users: ' . $e->getMessage(), 'lk');
                                    Yii::$app->getSession()->setFlash('error', 'Не удалось привязать telegram к вашему аккаунту.');
                                    return $this->render('verify', [
                                        'target' => 'telegram',
                                        'token' => md5(uniqid(rand(), true)),
                                        'csrf' => Yii::$app->session->get('csrf')
                                    ]);
                                }
                            }
                        }
                        else{
                            $model->tg_user_id = $params['tg_user_id'];
                            if(isset($result->username)){
                                $model->tg_username = $result->username;
                            }                            
                            if(isset($result->first_name)){
                                $model->tg_first_name = $result->first_name;
                            }                            
                            if(isset($result->last_name)){
                                $model->tg_last_name = $result->last_name;
                            }                            
                            if(isset($result->bio)){
                                $model->tg_bio = $result->bio;
                            }
                            if(isset($result->type)){
                                $model->tg_type = $result->type;
                            }
                            if($model->validate()){
                                $transaction = Yii::$app->db->beginTransaction();
                                try{
                                    if($model->save()){
                                        $sql = "UPDATE users SET tg_member_id = :id WHERE id = :account_id;";
                                        $result = Yii::$app->db->createCommand($sql)
                                            ->bindValue(':id', $model->findOne(['tg_user_id' => $params['tg_user_id']])['id'])
                                            ->bindValue(':account_id', Yii::$app->user->identity->id)
                                            ->execute();
                                        if($result !== false){
                                            $authManager = Yii::$app->authManager;
                                            $authManager->assign($authManager->getRole('tg-verify'), Yii::$app->user->identity->id);
                                            $transaction->commit();
                                            $model = new Users();
                                            $model = $model->findOne(['id' => Yii::$app->user->identity->id]);
                                            Yii::$app->getSession()->setFlash('success', 'Telegram успешно подтвержден.');
                                            return $this->render('index', [
                                                'username' => $model->username,
                                                'tg_member_id' => $model->tg_member_id,
                                                'email' => $model->email,
                                                'phone' => $model->phone,
                                                'csrf' => Yii::$app->session->get('csrf')
                                            ]);
                                        }
                                        else{
                                            $transaction->rollBack();
                                            Yii::$app->getSession()->setFlash('error', 'Не удалось привязать telegram к вашему аккаунту.');
                                            return $this->render('verify', [
                                                'target' => 'telegram',
                                                'token' => md5(uniqid(rand(), true)),
                                                'csrf' => Yii::$app->session->get('csrf')
                                            ]);
                                        }
                                    }
                                }
                                catch(\Exception|\Throwable $e){
                                    $transaction->rollBack();
                                    Yii::error('LkController Telegram verify ошибка во время сохранения модели и обновления users: ' . $e->getMessage(), 'lk');
                                    Yii::$app->getSession()->setFlash('error', 'Не удалось привязать telegram к вашему аккаунту.');
                                    return $this->render('verify', [
                                        'target' => 'telegram',
                                        'token' => md5(uniqid(rand(), true)),
                                        'csrf' => Yii::$app->session->get('csrf')
                                    ]);
                                }
                            }
                        }
                        
                    }
                }
                else{
                    Yii::$app->getSession()->setFlash('error', 'Описание пользователя недоступно!');
                    $model = new Users();
                    $model = $model->findOne(['id' => Yii::$app->user->identity->id]);
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
                $model = $model->findOne(['id' => Yii::$app->user->identity->id]);
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