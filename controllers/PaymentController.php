<?php

//todo Сделать вывод ошибок для telegram в шаблоне telegram
//Todo написать JS закрытия окна если доступен webapp
//Todo PAYKASSA need to TEST on hosting
//TODO НАПИСАТЬ ВАЛИДАЦИЮ CRC freekassa|success
//TODO НАПИСАТЬ PAYPALL|SUCCESS
//TODO ПРОПИСАТЬ TRY CATCH ДЛЯ SUCCESS RESULT

namespace app\controllers;

use Yii;
use yii\db\Query;
use Paykassa\PaykassaSCI;
use yii\httpclient\Client;
use Paykassa\PaykassaCurrency;
use yii\web\ForbiddenHttpException;

class PaymentController extends AppController{

    public function beforeAction($action){
        $params = Yii::$app->request->get();
        if($action->id == 'index'){
            if(isset($params['shop']) && isset($params['count']) && isset($params['name'])  && isset($params['userId']) && isset($params['days']) && isset($params['hash'])){
                $params['count'] = intval($params['count']);
                if(md5($_SERVER['API_KEY_0'] . $params['count'] . $params['userId'] . $params['shop'] . $params['days'] . $_SERVER['API_KEY_1']) == $params['hash'] && $params['count'] > 0){
                    if(Yii::$app->session->has('csrf')){
                        Yii::$app->session->set('csrfOLD', md5(uniqid(rand(), true)));
                        if(Yii::$app->session->get('csrf') == Yii::$app->session->get('csrfOLD')){
                            return parent::beforeAction($action);
                        }
                    }
                    else{
                        Yii::$app->session->set('csrf', md5(uniqid(rand(), true)));
                        return parent::beforeAction($action);
                    }
                }
            }
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
        elseif($action->id == 'route'){
            if(isset($params['webApp']) && $params['webApp'] != ''){
                return parent::beforeAction($action);
            }
            else{
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif($action->id == 'confirmation'){//Todo удалить после теста ЭТО ДЛЯ PAYPALL ПОДТВЕРЖДЕНИЕ ВЫПОЛНЕНИЯ ЗАКАЗА
            if(isset($params['token'])){
                return parent::beforeAction($action);
            }
            else{
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif($action->id == 'success'){
            $this->enableCsrfValidation = false;
            return parent::beforeAction($action); 
        }
        elseif($action->id == 'result'){
            if(Yii::$app->request->isPost){
                $this->enableCsrfValidation = false;
                Yii::debug('Отладка Payment|Result POST: ' . json_encode($_POST) . ' GET: ' . json_encode($_GET), 'payment');//TODO УДАЛИТЬ ПОСЛЕ ТЕСТА СКОЛЬКО КОММИСИИ
                sleep(10);//TODO Переписать через promise а в выполнение уже поставить sleep, чтобы не блокироваался процесс
                return parent::beforeAction($action); 
            }
            else{
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif($action->id == 'fail'){
            if(Yii::$app->request->isPost){
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }
        }
        else{
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
    }

    public function actionIndex() : string{
        $params = Yii::$app->request->get();
        $config = AppController::getConfig($params['shop'], true);
        if(isset($config['paykassa'])){
            $pk = AppController::getConfig($params['shop'], false, 'paykassa');
            $params['pk'] = new PaykassaSCI($pk['paykassa']["merchant_id"], $pk['paykassa']["merchant_password"], $pk['paykassa']["is_test"]);
        }
        return $this->render('index', ['params' => $params, 'csrf' => Yii::$app->session->get('csrf'), 'config' => $config]);
    }

    public function actionRoute() : void{//todo переписать извлечение конфига по методу а не весь
        $params = Yii::$app->request->get();
        $config = AppController::getConfig($params['shop'])[strtolower($params['method'])];
        $isTest = $config['is_test'];
        $name = $params['name'];
        $count = intval($params['count']);
        if($params['method'] == 'RoboKassa'){//RoboKassa start
            $sql = "INSERT INTO orders (tg_user_id, count, method, shop, position_name, access_days, created_time, is_test, web_app_query_id, currency, count_in_currency) 
            VALUES (:user, :count, :method, :shop, :position_name, :access_days, CURRENT_TIMESTAMP(), :is_test, :web_app, :currency, :count_in_currency)";
            $qParams = [
                ':user' => $params['userId'],
                ':count' => $count,
                ':method' => $params['method'],
                ':shop' => $params['shop'],
                ':position_name' => $name,
                ':access_days' => $params['days'],
                ':is_test' => $isTest,
                ':web_app' => $params['webApp'],
                ':currency' => 'RUB',
                ':count_in_currency' => $count,
            ];
            try{
                $result = Yii::$app->db->createCommand($sql, $qParams)->execute();
                if($result !== false){
                    $query = new Query();
                    $result = $query->select('id')
                        ->from('orders')
                        ->where(['tg_user_id' => $params['userId']])
                        ->orderBy(['id' => SORT_DESC])
                        ->limit(1)
                        ->scalar();
                    if($result !== false){
                        $invId = $result;
                        $receipt = "%7B%22items%22:%5B%7B%22name%22:%22$name%22,%22quantity%22:1,%22sum%22:$count,%22tax%22:%22none%22%7D%5D%7D";
                        $receipt_urlencode = urlencode($receipt);
                        $inv_desc = "";
                        $login = $config['shop'];
                        if($isTest){
                            $crc = md5($login . ':' . $count . ':' . $invId . ':' . $receipt . ':' . $config[2]);
                            $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MrchLogin=$login&OutSum=$count&InvId=$invId&Receipt=$receipt_urlencode&Desc=$inv_desc&SignatureValue=$crc&Istest=1";
                        } 
                        else{
                            $crc = md5($login . ':' . $count . ':' . $invId . ':' . $receipt . ':' . $config[0]);
                            $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MrchLogin=$login&OutSum=$count&InvId=$invId&Receipt=$receipt_urlencode&Desc=$inv_desc&SignatureValue=$crc";
                        }
                        Yii::$app->getResponse()->redirect($url)->send();
                        exit(0);
                    }
                    else{
                        Yii::error('Method robo db SELECT, не получилось извлечь ID: ' . Yii::$app->db->getSchema()->errorInfo(), 'payment');
                    }
                }
                else{
                    Yii::error('Method robo db, не получилось записать: ' . Yii::$app->db->getSchema()->errorInfo(), 'payment');
                }
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
            catch(\Exception|\Throwable $e){
                Yii::error('Ошибка в PaymentController Route RoboKassa: ' . $e->getMessage(), 'payment');
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif($params['method'] == 'PayKassa'){//PayKassa start
            $pk = new PaykassaSCI($config['merchant_id'], $config['merchant_password'], $isTest);
            @list($system, $currency) = preg_split('~_(?=[^_]*$)~', $_POST["pscur"]);
            $pairs = 'RUB_' . strtoupper($currency);
            try{
                $result = PaykassaCurrency::getCurrencyPairs([$pairs]);//Todo need to TEST on hosting
                if($result['error']){
                    Yii::error('Method pay не получилось получить курс валютной пары: ' . json_encode($result['message']), 'payment');
                    throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
                else{
                    $countInCurrrency = bcmul($result['data'][0][$pairs], $count, 8);
                    $sql = "INSERT INTO orders (tg_user_id, count, method, shop, position_name, access_days, created_time, is_test, web_app_query_id, currency, count_in_currency)
                    VALUES (:user, :count, :method, :shop, :position_name, :access_days, CURRENT_TIMESTAMP(), :is_test, :web_app, :currency, :count_in_currency)";
                    $qParams = [
                        ':user' => $params['userId'],
                        ':count' => $count,
                        ':method' => $params['method'],
                        ':shop' => $params['shop'],
                        ':position_name' => $name,
                        ':access_days' => $params['days'],
                        ':is_test' => $isTest,
                        ':web_app' => $params['webApp'],
                        ':currency' => strtoupper($currency),
                        ':count_in_currency' => $count,
                    ];
                    $result = Yii::$app->db->createCommand($sql, $qParams)->execute();
                    if($result !== false){
                        $query = new Query();
                        $result = $query->select('id')
                            ->from('orders')
                            ->where(['tg_user_id' => $params['userId']])
                            ->orderBy(['id' => SORT_DESC])
                            ->limit(1)
                            ->scalar();
                        if($result !== false){
                            $invId = $result;
                            $result = $pk->createOrder(
                                $countInCurrrency,
                                $system,
                                $currency,
                                $invId,
                                $params['shop'] . ' ' . $name . ' ' .$params['hash'],
                            );
                            if($result['error']){
                                Yii::error('Method pay не получилось создать заказ в PayKassa: ' . json_encode($result['message']), 'payment');
                            }
                            else{
                                Yii::$app->getResponse()->redirect($result["data"]["url"])->send();
                                exit(0);                  
                            }
                        }
                        else{
                            Yii::error('Method pay db SELECT, не получилось извлечь ID: ' . Yii::$app->db->getSchema()->errorInfo(), 'payment');
                        }
                    }
                    else{
                        Yii::error('Method pay db, не получилось записать: ' . Yii::$app->db->getSchema()->errorInfo(), 'payment');
                    }
                }
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
            catch(\Exception|\Throwable $e){
                Yii::error('Ошибка в PaymentController Route PayKassa: ' . $e->getMessage(), 'payment');
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif($params['method'] == 'FreeKassa'){//FreeKassa start
            $sql = "INSERT INTO orders (tg_user_id, count, method, shop, position_name, access_days, created_time, is_test, web_app_query_id, currency, count_in_currency)
            VALUES (:user, :count, :method, :shop, :position_name, :access_days, CURRENT_TIMESTAMP(), :is_test, :web_app, :currency, :count_in_currency)";
            $qParams = [
                ':user' => $params['userId'],
                ':count' => $count,
                ':method' => $params['method'],
                ':shop' => $params['shop'],
                ':position_name' => $name,
                ':access_days' => $params['days'],
                ':is_test' => $isTest,
                ':web_app' => $params['webApp'],
                ':currency' => 'RUB',
                ':count_in_currency' => $count,
            ];
            try{
                $result = Yii::$app->db->createCommand($sql, $qParams)->execute();
                if($result !== false){
                    $query = new Query();
                    $result = $query->select('id')
                        ->from('orders')
                        ->where(['tg_user_id' => $params['userId']])
                        ->orderBy(['id' => SORT_DESC])
                        ->limit(1)
                        ->scalar();
                    if($result !== false){
                        $invId = $result;
                        $sign = md5($config['merchant_id'] . ':' . $count . ':' . $config['secret'][0] . ':' . 'RUB' . ':' . $invId);
                        $url = 'https://pay.freekassa.ru/?m=' . $config['merchant_id'] . '&oa=' . $count . '&currency=' . 'RUB' . '&o=' . $invId . '&s=' . $sign;
                        Yii::$app->getResponse()->redirect($url)->send();
                        exit(0);
                    }
                    else{
                        Yii::error('Method free db SELECT, не получилось извлечь ID: ' . Yii::$app->db->getSchema()->errorInfo(), 'payment');
                    }
                }
                else{
                    Yii::error('Method free db, не получилось записать: ' . Yii::$app->db->getSchema()->errorInfo(), 'payment');
                }
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
            catch(\Exception|\Throwable $e){
                Yii::error('Ошибка в PaymentController Route FreeKassa: ' . $e->getMessage(), 'payment');
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif($params['method'] == 'PayPall'){//PayPal start
            $client = new Client();
            $url = 'https://api-m.paypal.com/v1/oauth2/token';//For test - https://api-m.sandbox.paypal.com/v1/oauth2/token
            $headers = [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . base64_encode($config['client_id'] . ':' . $config['secret'])
            ];
            $data = [
                'grant_type' => 'client_credentials',
            ];
            $response = $client->createRequest()// Отправка POST-запроса на получение токена доступа
            ->setMethod('POST')
            ->setUrl($url)
            ->setHeaders($headers)
            ->setData($data)
            ->send();
            if($response->isOk){// Проверка успешности запроса
                $accessToken = $response->data['access_token'];
                $sql = "INSERT INTO orders (tg_user_id, count, method, shop, position_name, access_days, created_time, is_test, web_app_query_id, currency, count_in_currency) 
                VALUES (:user, :count, :method, :shop, :position_name, :access_days, CURRENT_TIMESTAMP(), :is_test, :web_app, :currency, :count_in_currency)";
                $qParams = [
                    ':user' => $params['userId'],
                    ':count' => $count,
                    ':method' => $params['method'],
                    ':shop' => $params['shop'],
                    ':position_name' => $name,
                    ':access_days' => $params['days'],
                    ':is_test' => $isTest,
                    ':web_app' => $params['webApp'],
                    ':currency' => 'RUB',
                    ':count_in_currency' => $count
                ];
                try{
                    $result = Yii::$app->db->createCommand($sql, $qParams)->execute();
                    if($result != false){
                        $query = new Query();
                        $result = $query->select('id')
                            ->from('orders')
                            ->where(['tg_user_id' => $params['userId']])
                            ->orderBy(['id' => SORT_DESC])
                            ->limit(1)
                            ->scalar();
                        if($result != false){
                            $invId = $result;
                            $url = 'https://api-m.paypal.com/v2/checkout/orders';//For test - https://api-m.sandbox.paypal.com/v2/checkout/orders
                            $headers = [
                                'Content-Type' => 'application/json',
                                'Authorization' => 'Bearer ' . $accessToken
                            ];
                            $data = [
                                "intent" => "CAPTURE",// Формируем данные для запроса
                                "purchase_units" => [
                                    [
                                        "reference_id" => $invId,//ID платежа УНИКАЛЬНЫЙ
                                        "amount" => [
                                            "currency_code" => "RUB",//Валюта платежа
                                            "value" => $count// $count Сумма платежа
                                        ]
                                    ]
                                ],
                                "payment_source" => [
                                    "paypal" => [
                                        "experience_context" => [
                                            "payment_method_preference" => "IMMEDIATE_PAYMENT_REQUIRED",
                                            "payment_method_selected" => "PAYPAL", //PAYPAL
                                            "brand_name" => $params['shop'], //Название магазина
                                            "locale" => "en-US",//ru-Ru
                                            "landing_page" => "LOGIN",
                                            "shipping_preference" => "NO_SHIPPING",//без доставки
                                            "user_action" => "PAY_NOW",//оплата сразу
                                            "return_url" => "https://diamondapi.space/success.php",//Страница успешной оплаты
                                            "cancel_url" => "https://diamondapi.space/fail.php"//Страница fail
                                        ]
                                    ]
                                ]
                            ];
                            $response = $client->createRequest()// Отправка POST-запроса на создание платежа
                            ->setMethod('POST')
                            ->setUrl($url)
                            ->setHeaders($headers)
                            ->setContent(json_encode($data))
                            ->send();
                            if($response->isOk){
                                $ppid = explode('/', $response->getData()['links'][0]['href'])[6];
                                if(isset($ppid) && $ppid != ''){
                                    $sql = "UPDATE orders SET paypal_order_id = :ppid WHERE id = :order_id;";
                                    $result = Yii::$app->db->createCommand($sql)
                                        ->bindValue(':ppid', $ppid)
                                        ->bindValue(':order_id', $invId)
                                        ->execute();
                                    if($result !== false){
                                        Yii::$app->getResponse()->redirect($response->getData()['links'][1]['href'])->send();
                                        exit(0);
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
                        Yii::error('Method paypal db, не получилось записать INSERT: ' . Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params), 'payment');
                    }
                }
                catch(\Exception|\Throwable $e){
                    Yii::error('Ошибка в PaymentController paypal: ' . $e->getMessage() . PHP_EOL . ' Параметры: ' . json_encode($params), 'payment');
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
    public function actionConfirmation() : void{
        $params = Yii::$app->request->get();
        if(isset($params['token']) && isset($params['PayerID'])){
            try{
                $sql = "SELECT id, status, shop FROM orders WHERE paypal_order_id = :ppid ORDER BY id DESC limit 1";
                $result = Yii::$app->db->createCommand($sql)
                    ->bindValue(':ppid', $params['token'])
                    ->queryOne();
                if($result != false){
                    if(isset($result['id']) && isset($result['status']) && isset($result['shop']) && $result['status'] == 0){
                        $config = Yii::$app->params['shops'][$result['shop']]['PayPal'];
                        $base64Token = base64_encode($config['clientId'] . ':' . $config['secret']);
                        if(isset($config)){
                            $client = new Client();
                            $url = 'https://api-m.paypal.com/v1/oauth2/token';//For test https://api-m.sandbox.paypal.com/v1/oauth2/token
                            $headers = [
                                'Content-Type' => 'application/x-www-form-urlencoded',
                                'Authorization' => 'Basic ' . $base64Token
                            ];
                            $data = [
                                'grant_type' => 'client_credentials',
                            ];
                            $response = $client->createRequest()// Отправка POST-запроса на получение токена доступа
                            ->setMethod('POST')
                            ->setUrl($url)
                            ->setHeaders($headers)
                            ->setData($data)
                            ->send();
                            if($response->isOk){
                                $accessToken = $response->data['access_token'];
                                $request = $client->createRequest()
                                ->setMethod('GET')
                                ->setUrl('https://api-m.paypal.com/v2/checkout/orders/' . $params['token'])//For test https://api-m.sandbox.paypal.com/v2/checkout/orders/
                                ->addHeaders([
                                    'Content-Type' => 'application/json',
                                    'Authorization' => 'Bearer ' . $accessToken
                                ]);
                                $response = $request->send();
                                if($response->isOk){
                                    $response = $response->getData();
                                    if(isset($response['status']) && isset($response['payer']['payer_id']) && $response['status'] == 'APPROVED' && $response['payer']['payer_id'] == $params['PayerID']){
                                        $id = $response['id'];
                                        $paymentId = $response['purchase_units'][0]['reference_id'];
                                        if($id == $params['token']){
                                            $sql = "UPDATE orders SET status = :status_bool, resulted_time = NOW() WHERE id = :order_id; AND paypal_order_id = :ppd";
                                            $result = Yii::$app->db->createCommand($sql)
                                                ->bindValue(':status_bool', 1)
                                                ->bindValue(':order_id', $paymentId)
                                                ->bindValue(':ppd', $id)
                                                ->execute();
                                            if($result !== false){//Todo сделать транзакцию с момента апдейта и до конца
                                                $url = 'https://api-m.paypal.com/v2/checkout/orders/' . $id . '/capture';//For test https://api-m.sandbox.paypal.com/v2/checkout/orders/' . $id . '/capture'
                                                $headers = [
                                                    'Content-Type' => 'application/json',
                                                    'Authorization' => 'Basic ' . $base64Token
                                                ];
                                                $response = $client->createRequest()
                                                ->setMethod('POST')
                                                ->setUrl($url)
                                                ->setHeaders($headers)
                                                ->send();
                                                if($response->isOk){
                                                    echo $id . '|success';//Todo если тг отправить ответ answer web app query
                                                    AppController::debug($response->getData(), 1);
                                                    exit(0);
                                                }
                                                else{//Todo ЕСТЬ ТАКАЯ МЫСЛЬ, ЧТО ЕСЛИ ДОЛГО НЕ ПОДТВЕРЖДАТЬ, PAYPAL ШЛЕТ НАС НАХУЙ НАДО ТЕСТАНУТЬ
                                                    AppController::debug($response->getData(), 1);
                                                    throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);//Todo rollback + message
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
                    throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
            }
            catch(\Exception|\Throwable $e){
                Yii::error('Ошибка в PaymentController Confirmation: ' . $e->getMessage(), 'payment');
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        else{
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
    }
    
    // public function actionDisput() : void{
    //     $params = Yii::$app->request->get();
    //     $config = AppController::getConfig('club-dimitriev')['paypall'];;
    //     $client = new Client();
    //     $url = 'https://api-m.paypal.com/v1/oauth2/token';//For test https://api-m.sandbox.paypal.com/v1/oauth2/token
    //     $headers = [
    //         'Content-Type' => 'application/x-www-form-urlencoded',
    //         'Authorization' => 'Basic ' . base64_encode($config['client_id'] . ':' . $config['secret']),
    //     ];
    //     $data = [
    //         'grant_type' => 'client_credentials',
    //     ];
    //     $response = $client->createRequest()// Отправка POST-запроса на получение токена доступа
    //     ->setMethod('POST')
    //     ->setUrl($url)
    //     ->setHeaders($headers)
    //     ->setData($data)
    //     ->send();
    //     $accessToken = $response->data['access_token'];
    //     $request = $client->createRequest()
    //     ->setMethod('GET')
    //     ->setUrl('https://api-m.paypal.com/v1/customer/disputes/')//For test https://api-m.sandbox.paypal.com/v2/checkout/orders/
    //     ->addHeaders([
    //         'Authorization' => 'Bearer ' . $accessToken,
    //     ]);
    //     $response = $request->send();
    //     AppController::debug($response->getData(), 1);
    // }


    public function actionSuccess() : void{//todo дописать для PayPall
        $params = Yii::$app->request->post();
        if(isset($params['SignatureValue']) && isset($params['InvId']) && isset($params['OutSum'])){//RoboKassa start
            $invId = $params['InvId'];
            $sql = "SELECT `status`, shop, access_days, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
            if($result !== false){
                $shop = $result['shop'];
                $accessDays = $result['access_days'];
                $webAppQueryId = $result['web_app_query_id'];
                if(isset($params['isTest']) && $params['isTest'] == 1){
                    $crc = md5($params['OutSum'] . ':' . $invId . ':' . AppController::getConfig($shop, false, 'robokassa')['robokassa'][2]);
                }
                else{
                    $crc= md5($params['OutSum'] . ':' . $invId . ':' . AppController::getConfig($shop, false, 'robokassa')['robokassa'][0]);
                }
                if($params['SignatureValue'] != $crc){//Валидация crc
                    Yii::error('Method RoboKassa|Success crc: ' . json_encode($params), 'payment');
                    throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
                if($result['status'] == 1){//Todo написать JS закрытия окна если доступен webapp
                    echo 'Заказ: ' . $invId . ' Успешно оплачен. ' . PHP_EOL . 'Данную страницу можно закрывать.';
                    exit(0);
                }
                else{
                    $sql = "UPDATE orders SET `status` = 1 WHERE id = :order_id;";
                    $result = Yii::$app->db->createCommand($sql)
                    ->bindValue(':order_id', $invId)
                    ->execute();
                    if($result !== false){
                        echo 'Заказ: ' . $invId . ' Успешно оплачен. ' . PHP_EOL . 'Данную страницу можно закрывать.';
                        $data = [
                            'web_app_query_id' => $webAppQueryId,
                            'result' => '{"type":"article","id":"1","title":"success","input_message_content":{"message_text":"Success' . $accessDays . '"}}',
                        ];
                        AppController::curlSendMessage($data, $shop, '/answerWebAppQuery');
                    }
                    else{
                        echo $invId . '|Fail';
                        Yii::error('Method RoboKassa|Success db, не получилось изменить статус заказа: ' . Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params), 'payment');
                        $data = [
                            'web_app_query_id' => $webAppQueryId,
                            'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                        ];
                        AppController::curlSendMessage($data, $shop, '/answerWebAppQuery');
                    }
                    exit(0);
                }
            }
            else{
                Yii::error('Method RoboKassa|Success db, не получилось извлечь заказ: ' . Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params), 'payment');
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif(isset($params['sign']) && isset($params['order_id']) && isset($params['amount']) && isset($params['status'])){//PayKassa start
            $invId = $params['order_id'];
            $sql = "SELECT `status`, shop, access_days, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
            if($result !== false){
                $shop = $result['shop'];
                $accessDays = $result['access_days'];
                $webAppQueryId = $result['web_app_query_id'];
                $config = AppController::getConfig($shop, false, 'paykassa')['paykassa'];
                $crc = array($params['amount'], $config['merchant_id'], $params['order_id'], $params['status'], $config['merchant_password']);
                if(md5(implode(':', $crc) != $params['sign'])){
                    Yii::error('Method PayKassa|Success crc: ' . json_encode($params), 'payment');
                    throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
                if($result['status'] == 1){//Todo написать JS закрытия окна если доступен webapp
                    echo 'Заказ: ' . $invId . ' Успешно оплачен. ' . PHP_EOL . 'Данную страницу можно закрывать.';
                    exit(0);
                }
                else{
                    $sql = "UPDATE orders SET `status` = 1 WHERE id = :order_id;";
                    $result = Yii::$app->db->createCommand($sql)
                    ->bindValue(':order_id', $invId)
                    ->execute();
                    if($result !== false){
                        echo 'Заказ: ' . $invId . ' Успешно оплачен. ' . PHP_EOL . 'Данную страницу можно закрывать.';
                        $data = [
                            'web_app_query_id' => $webAppQueryId,
                            'result' => '{"type":"article","id":"1","title":"success","input_message_content":{"message_text":"Success' . $accessDays . '"}}',
                        ];
                        AppController::curlSendMessage($data, $shop, '/answerWebAppQuery');
                    }
                    else{
                        echo $invId . '|Fail';
                        Yii::error('Method PayKassa|Success db, не получилось изменить статус заказа: ' . Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params), 'payment');
                        $data = [
                            'web_app_query_id' => $webAppQueryId,
                            'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                        ];
                        AppController::curlSendMessage($data, $shop, '/answerWebAppQuery');
                    }
                    exit(0);
                }
            }
            else{
                Yii::error('Method PayKassa|Success db, не получилось извлечь заказ: ' . Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params), 'payment');
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif(isset($params['MERCHANT_ORDER_ID']) && isset($params['intid'])){//FreeKassa start
            $invId = $params['MERCHANT_ORDER_ID'];
            $sql = "SELECT `status`, shop, access_days, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";//TODO НАПИСАТЬ ВАЛИДАЦИЮ CRC
            $result = Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
            if($result !== false){
                $shop = $result['shop'];
                $accessDays = $result['access_days'];
                $webAppQueryId = $result['web_app_query_id'];
                //TODO НАПИСАТЬ ВАЛИДАЦИЮ CRC
                if($result['status'] == 1){//Todo написать JS закрытия окна если доступен webapp
                    echo 'Заказ: ' . $invId . ' Успешно оплачен. ' . PHP_EOL . 'Данную страницу можно закрывать.';
                    exit(0);
                }
                else{
                    $sql = "UPDATE orders SET `status` = 1 WHERE id = :order_id;";
                    $result = Yii::$app->db->createCommand($sql)
                    ->bindValue(':order_id', $invId)
                    ->execute();
                    if($result !== false){
                        echo 'Заказ: ' . $invId . ' Успешно оплачен. ' . PHP_EOL . 'Данную страницу можно закрывать.';
                        $data = [
                            'web_app_query_id' => $webAppQueryId,
                            'result' => '{"type":"article","id":"1","title":"success","input_message_content":{"message_text":"Success' . $accessDays . '"}}',
                        ];
                        AppController::curlSendMessage($data, $shop, '/answerWebAppQuery');
                    }
                    else{
                        echo $invId . '|Fail';
                        Yii::error('Method FreeKassa|Success db, не получилось изменить статус заказа: ' . Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params), 'payment');
                        $data = [
                            'web_app_query_id' => $webAppQueryId,
                            'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                        ];
                        AppController::curlSendMessage($data, $shop, '/answerWebAppQuery');
                    }
                    exit(0);
                }
            }
            else{
                Yii::error('Method FreeKassa|Success db, не получилось извлечь заказ: ' . Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params), 'payment');
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif(isset($params['НАПИСАТЬ ДЛЯ PAYPALL'])){//TODO ДОПИСАТЬ ДЛЯ PAYPALL
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
        else{
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
    }

    public function actionResult() : void{//TODO Дописать RESULT для PAYPALL
        $params = Yii::$app->request->post();
        if(isset($params['InvId']) && isset($params['OutSum']) && isset($params['crc'])){//RoboKassa start
            $invId = $params['InvId'];
            $sql = "SELECT tg_user_id, `status`, access_days, method, shop, is_test FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
            $count = intval($result['count']);
            $shop = $result['shop'];
            $isTest = $result['is_test'];
            if($result !== false && $result['status'] == 0 && $result['method'] == 'RoboKassa'){
                $days = $result['access_days'];
                $userId = $result['tg_user_id'];
                if(isset($params['isTest']) && $params['isTest'] = 1){
                    $crc = strtoupper(md5($params['OutSum'] . ':' . $invId . ':' . AppController::getConfig($shop, false, 'robokassa')['robokassa'][3]));
                }
                else{
                    $crc = strtoupper(md5($params['OutSum'] . ':' . $invId . ':' . AppController::getConfig($shop, false, 'robokassa')['robokassa'][1]));
                }
                if($params['crc'] != $crc){//Валидация crc
                    echo 'OK' . $invId . '\n';
                    Yii::error('Method RoboKassa|Result crc: ' . json_encode($params), 'payment');
                    throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
                else{
                    $sql = "UPDATE orders SET `status` = 1 WHERE id = :order_id;";
                    $result = Yii::$app->db->createCommand($sql)
                    ->bindValue(':order_id', $invId)
                    ->execute();
                    if($result !== false){
                        if(!$isTest){
                            self::orderComplete($invId, $shop, $count, 'RoboKassa', $params['Fee'], $params['IncCurrLabel']);
                        }
                        echo 'OK' . $invId . '\n';
                        AppController::curlSendMessage(self::getResultButton($userId, $days), $shop);
                    }
                    else{
                        Yii::error('Method RoboKassa|Result db, не получилось изменить статус заказа: ' . Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params), 'payment');
                    }
                }
                exit(0);
            }
            elseif($result !== false && $result['status'] == 1 && $result['method'] == 'RoboKassa'){
                if(!$isTest){
                    self::orderComplete($invId, $shop, $count, 'RoboKassa', $params['Fee'], $params['IncCurrLabel']);
                }
                echo 'OK' . $invId . '\n';
            }
            else{
                echo 'OK' . $invId . '\n';
            }
            exit(0);
        }
        elseif(isset($params['private_hash']) && isset($params['system']) && isset($params['currency']) && isset($params['order_id']) && isset($params['type'])){//PayKassa start
            $invId = intval($params['order_id']);
            $sql = "SELECT tg_user_id, `status`, count, access_days, method, shop, is_test FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
            $count = intval($result['count']);
            $shop = $result['shop'];
            $isTest = $result['is_test'];
            if($result !== false && $result['status'] == 0 && $result['method'] == 'PayKassa'){
                $userId = $result['tg_user_id'];
                $days = $result['access_days'];
                $config = AppController::getConfig($shop, false, 'paykassa')['paykassa'];
                $paykassa = new PaykassaSCI($config['merchant_id'], $config['merchant_password'], $config['is_test']);
                $result = $paykassa->checkOrderIpn($params['private_hash']);
                if($result['error']){
                    echo $invId . '|success';
                    Yii::error('Method PayKassa|Result crc: ' . json_encode($result['message']), 'payment');
                    exit(0);
                }
                else{
                    $sql = "UPDATE orders SET `status` = 1 WHERE id = :order_id;";
                    $result = Yii::$app->db->createCommand($sql)
                    ->bindValue(':order_id', $invId)
                    ->execute();
                    if($result !== false){
                        if(!$isTest){
                            self::orderComplete($invId, $shop, $count, 'PayKassa', 0);
                        }
                        echo $invId . '|success';
                        AppController::curlSendMessage(self::getResultButton($userId, $days), $shop);
                    }
                    else{
                        Yii::error('Method PayKassa|Result db, не получилось изменить статус заказа: ' . Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params), 'payment');
                    }
                }
                exit(0);
            }
            elseif($result !== false && $result['status'] == 1 && $result['method'] == 'PayKassa'){
                if(!$isTest){
                    self::orderComplete($invId, $shop, $count, 'PayKassa', 0);
                }
                echo $invId . '|success';
            }
            else{
                echo $invId . '|success';
            }
            exit(0);
        }
        elseif(isset($params['MERCHANT_ID']) && isset($params['AMOUNT']) && isset($params['intid']) && isset($params['MERCHANT_ORDER_ID']) && isset($params['SIGN']) && isset($params['commission'])){//FreeKassa start
            if(!in_array(self::getIP(), array('168.119.157.136', '168.119.60.227', '138.201.88.124', '178.154.197.79'))){
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
            $invId = intval($params['MERCHANT_ORDER_ID']);
            $sql = "SELECT tg_user_id, `status`, count, access_days, method, shop, is_test FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
            $count = intval($result['count']);
            $shop = $result['shop'];
            $isTest = $result['is_test'];
            if($result !== false && $result['status'] == 0 && $result['method'] == 'FreeKassa'){
                $userId = $result['tg_user_id'];
                $days = $result['access_days'];
                if($params['SIGN'] != md5($params['MERCHANT_ID'] . ':' . $params['AMOUNT'] . ':' . AppController::getConfig($shop, false, 'freekassa')['freekassa']['secret'][1] . ':' . $params['MERCHANT_ORDER_ID'])){//CRC
                    echo 'OK' . $invId . '\n';
                    Yii::error('Method FreeKassa|Result crc: ' . json_encode($params), 'payment');
                    throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
                else{
                    $sql = "UPDATE orders SET `status` = 1 WHERE id = :order_id;";
                    $result = Yii::$app->db->createCommand($sql)
                    ->bindValue(':order_id', $invId)
                    ->execute();
                    if($result !== false){
                        if(!$isTest){
                            self::orderComplete($invId, $shop, $count, 'FreeKassa', round(floatval($params['commission']), 2));
                        }
                        echo 'YES';
                        AppController::curlSendMessage(self::getResultButton($userId, $days), $shop);
                    }
                    else{
                        Yii::error('Method FreeKassa|Result db, не получилось изменить статус заказа: ' . Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params), 'payment');
                    }
                    exit(0);
                }
            }
            elseif($result !== false && $result['status'] == 1 && $result['method'] == 'FreeKassa'){
                if(!$isTest){
                    self::orderComplete($invId, $shop, $count, 'FreeKassa', $params['commission']);
                }
                echo 'YES';
            }
            else{
                echo 'YES';
            }
            exit(0);
        }
        elseif(isset($params['НАПИСАТЬ ДЛЯ PAYPALL'])){//TODO ДОПИСАТЬ ДЛЯ PAYPALL
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
        else{
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
    }

    public function actionFail() : void{
        $params = Yii::$app->request->post();
        if(isset($params["InvId"])){//RoboKassa start
            $invId = $params["InvId"];
            echo $invId . '|Fail';
            $sql = "SELECT shop, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
            if($result !== false){
                $shop = $result['shop'];
                $data = [
                    'web_app_query_id' => $result['web_app_query_id'],
                    'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                ];
                AppController::curlSendMessage($data, $shop, '/answerWebAppQuery');
            }
            else{
                Yii::error('Method robo|Fail db SELECT, не получилось извлечь shop, web_app_query_id: ' . Yii::$app->db->getSchema()->errorInfo(), 'payment');
            }
            exit(0);
        }
        elseif(isset($_GET['order_id'])){//PayKassa start
            $invId = $_GET['order_id'];
            echo $invId . '|Fail';
            $sql = "SELECT shop, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
            if($result !== false){
                $shop = $result['shop'];
                $data = [
                    'web_app_query_id' => $result['web_app_query_id'],
                    'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                ];
                AppController::curlSendMessage($data, $shop, '/answerWebAppQuery');
            }
            else{
                Yii::error('Method pay|Fail db SELECT, не получилось извлечь shop, web_app_query_id: ' . Yii::$app->db->getSchema()->errorInfo(), 'payment');
            }
            exit(0);
        }
        else{
            Yii::debug('Отладка Payment|Fail POST: ' . json_encode($_POST) . ' GET: ' . json_encode($_GET), 'payment');
            echo 'Fail';
            exit(0);
        }
    }

    private static function getResultButton(int $userId, int $days){
        return [
            'chat_id' => $userId,
            'text' => 'Оплата прошла успешно',
            'reply_markup' => [
                'inline_keyboard' => [
                    [  
                        [
                            'text' => 'Нажмите, чтобы активировать услугу',
                            'callback_data' => 'Success' . $days,
                        ],
                    ]
                ],
                'resize_keyboard' => true,
            ],
        ];
    }

    private static function orderComplete(int $invId, string $shop, int $count, string $method, float $fee, string|null $paymentMethod = null){
        $sql = "SELECT COUNT(*) FROM orders_complete WHERE order_id = :order_id";
        $result = Yii::$app->db->createCommand($sql)
        ->bindValue(':order_id', $invId)
        ->queryOne();
        if($result['COUNT(*)'] === 0){
            $sql = "INSERT INTO orders_complete (shop, count, method, payment_method, fee, order_id) 
            VALUES (:shop, :count, :method, :payment_method, :fee, :order_id)";
            $result = Yii::$app->db->createCommand($sql)
            ->bindValue(':shop', $shop)
            ->bindValue(':count', $count)
            ->bindValue(':method', $method)
            ->bindValue(':payment_method', $paymentMethod)
            ->bindValue(':fee', round(floatval($fee), 2))
            ->bindValue(':order_id', $invId)
            ->execute();
            if($result === false){
                Yii::error('Method ' . $method . '|orderComplete db, Ошибка записи| ' . ' Параметры: ' . json_encode([$invId, $shop, $count, $method, $fee, $paymentMethod], 1), 'test');
            }
        }
        else{
            Yii::error('Method ' . $method . '|orderComplete db, повторное срабатывание| ' . ' Параметры: ' . json_encode([$invId, $shop, $count, $method, $fee, $paymentMethod], 1), 'test');
        }
    }

    private static function getIP(){
        if(isset($_SERVER['HTTP_X_REAL_IP'])) return $_SERVER['HTTP_X_REAL_IP'];
        return $_SERVER['REMOTE_ADDR'];
    }

}