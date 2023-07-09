<?php

namespace app\controllers;

use Yii;
use yii\db\Query;
use app\models\users;
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
                        'roles' => ['admin']
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
        $query = new Query();
        $model = $query->select(['count', 'shop', 'method', 'resulted_time'])
        ->from('orders')->where(['tg_member_id' => Yii::$app->user->identity->tg_member_id, 'status' => 1, 'is_test' => 0])->all();
        return $this->render('payments', [
            'model' => $model
        ]);
    }

    public function actionSubscriptions(){
        return $this->render('subscriptions');
    }

    public function actionFinance(){
        $query = new Query();
        $admin = Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id, 'admin');
        $params = Yii::$app->request->get();
        $clientsPage = 1;

        if(isset($params['clientsPage']) && $params['clientsPage'] >= 1){
            $clientsPage = $params['clientsPage'];
        }
        
        if($admin && isset($params['showTestClients']) && $params['showTestClients'] == 1){
            $clients = $query->select(['id', 'shop', 'test_balance', 'test_blocked_balance', 'test_total_withdrawal'])
            ->from('clients')
            ->where(['tg_member_id' => Yii::$app->user->identity->tg_member_id])
            ->offset((5 * $clientsPage) - 5)
            ->limit(5)
            ->all();
        }
        else{
            $clients = $query->select(['id', 'shop', 'balance', 'blocked_balance', 'total_withdrawal'])
            ->from('clients')
            ->where(['tg_member_id' => Yii::$app->user->identity->tg_member_id])
            ->offset((5 * $clientsPage) - 5)
            ->limit(5)
            ->all();
        }

        $countClients = 0;

        if(!empty($clients)){
            $modelClients = new Clients();
            $countArr = count($clients);

            if($countArr < 5){
                $countClients = $countArr;
            }
            else{
                $countClients = $query->select('id')
                ->from('clients')
                ->where(['tg_member_id' => Yii::$app->user->identity->tg_member_id])
                ->count();
            }

            $allWithdrawals = 1;

            $withdrawalsPage = 1;
            $accrualsPage = 1;

            $showTestWithdrawals = 0;
            $showTestAccruals = 0;

            if(isset($params['allWithdrawals']) && $params['allWithdrawals'] == 1){
                $allWithdrawals = 4;
            }

            if(isset($params['withdrawalsPage']) && $params['withdrawalsPage'] >= 1){
                $withdrawalsPage = $params['withdrawalsPage'];
            }
            if(isset($params['accrualsPage']) && $params['accrualsPage'] >= 1){
                $accrualsPage = $params['accrualsPage'];
            }

            if($admin && isset($params['showTestWithdrawals']) && $params['showTestWithdrawals'] == 1){
                $showTestWithdrawals = 1;
            }
            if($admin && isset($params['showTestAccruals']) && $params['showTestAccruals'] == 1){
                $showTestAccruals = 1;
            }

            $countWithdrawals = 0;
            $countAccruals = 0;
            for($i = 0; $i < $countArr; $i++){
                $modelClients->id = $clients[$i]['id'];
                $withdrawals[$i] = $modelClients->getWithdrawals()
                ->andWhere(['>=', 'is_test', 0])
                ->andWhere(['<=', 'is_test', $showTestWithdrawals])
                ->andWhere(['>=', 'status', 0])
                ->andWhere(['<=', 'status', $allWithdrawals])
                ->offset((25 * $withdrawalsPage) - 25)
                ->limit(25)
                ->all();

                $countWithdrawals += $modelClients->getWithdrawals()
                ->andWhere(['=', 'is_test', 0])
                ->andWhere(['=', 'is_test', $showTestWithdrawals])
                ->andWhere(['>=', 'status', 0])
                ->andWhere(['<=', 'status', $allWithdrawals])
                ->count();

                $accruals[$i] = $modelClients->getOrders()
                ->andWhere(['status' => 1])
                ->andWhere(['=', 'is_test', 0])
                ->andWhere(['=', 'is_test', $showTestAccruals])
                ->offset((25 * $accrualsPage) - 25)
                ->limit(25)
                ->all();
        
                $countAccruals += $modelClients->getOrders()
                ->andWhere(['status' => 1])
                ->andWhere(['=', 'is_test', 0])
                ->andWhere(['=', 'is_test', $showTestAccruals])
                ->count();

                if(empty($withdrawals[$i])){
                    unset($withdrawals[$i]);
                }
                if(empty($accruals[$i])){
                    unset($accruals[$i]);
                }
            }
            sort($withdrawals);
            sort($accruals);
            
            $countArr = count($withdrawals);
            $j = 0;
            for($i = 0; $i < $countArr; $i++){
                $countSubArr = count($withdrawals[$i]);
                for($k = 0; $k < $countSubArr; $k ++){
                    $withdrawals[$countArr + $j] = $withdrawals[$i][$k];
                    $j++;
                }
                unset($withdrawals[$i]);
            }
            
            $countArr = count($accruals);
            $j = 0;
            for($i = 0; $i < $countArr; $i++){
                $countSubArr = count($accruals[$i]);
                for($k = 0; $k < $countSubArr; $k ++){
                    $accruals[$countArr + $j] = $accruals[$i][$k];
                    $j++;
                }
                unset($accruals[$i]);
            }

            sort($withdrawals);
            sort($accruals);
        }
        else{
            //$channels = null;
            $withdrawals = null;
            $accruals = null;
        }

        return $this->render('finance', [
            'clients' => $clients,
            'clientsCount' => $countClients,
            'withdrawals' => $withdrawals,
            'withdrawalsCount' => $countWithdrawals,
            'accruals' => $accruals,
            'accrualsCount' => $countAccruals,
            'admin' => $admin
        ]);
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