<?php
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
        if($action->id == 'confirmation'){//Todo удалить после теста
            if(isset($params['token'])){
                return parent::beforeAction($action);
            }
            else{
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif($action->id == 'disput'){
            return parent::beforeAction($action); 
        }
        elseif($action->id == 'route'){
            if(isset($params['webApp']) && $params['webApp'] != ''){
                return parent::beforeAction($action);
            }
            else{
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif($action->id == 'index'){
            if(isset($params['shop']) && isset($params['count']) && isset($params['name'])  && isset($params['userId']) && isset($params['days']) && isset($params['hash'])){
                $params['count'] = intval($params['count']);
                if(md5($_SERVER['API_KEY_0'] . $params['count'] . $params['userId'] . $params['shop'] . $params['days'] . $_SERVER['API_KEY_1']) == $params['hash'] && $params['count'] > 0){
                    if(Yii::$app->session->has('csrf') && isset($params['csrf'])){
                        if(Yii::$app->session->get('csrf') == $params['csrf']){
                            Yii::$app->session->set('csrf', md5(uniqid(rand(), true)));
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
            }
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
        else{
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
    }

    public function actions(){
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction'
            ],
        ];
    }

    public function actionIndex() : string{
        $params = Yii::$app->request->get();
        $config = AppController::getConfig($params['shop'], 1);
        if(isset($config['paykassa'])){
            $params['pk'] = new PaykassaSCI($config['paykassa']["merchant_id"], $config['paykassa']["merchant_password"], $config['paykassa']["is_test"]);
        }
        return $this->render('index', ['params' => $params, 'csrf' => Yii::$app->session->get('csrf'), 'config' => $config]);
    }

    public function actionRoute() : void{
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
                    $sql = "SELECT id FROM orders WHERE tg_user_id = :user_id ORDER BY id DESC limit 1";
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
                        $login = $params['shop'];
                        if($isTest){
                            $crc = md5($params['shop'] . ':' . $count . ':' . $invId . ':' . $receipt . ':' . $config[2]);
                            $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MrchLogin=$login&OutSum=$count&InvId=$invId&Receipt=$receipt_urlencode&Desc=$inv_desc&SignatureValue=$crc&Istest=1";
                        } 
                        else{
                            $crc = md5($params['shop'] . ':' . $count . ':' . $invId . ':' . $receipt . ':' . $config[0]);
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
                }
                else{
                    throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
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
                    $sql = "SELECT id FROM orders WHERE tg_user_id = :user_id ORDER BY id DESC limit 1";
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
                    $sql = "SELECT id FROM orders WHERE tg_user_id = :user_id ORDER BY id DESC limit 1";
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
                        $sql = "SELECT id FROM orders WHERE tg_user_id = :user_id ORDER BY id DESC limit 1";
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
                                    if($result != false){
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
                        Yii::error('Method paypal db, не получилось записать INSERT: ' . Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . 'Параметры: ' . json_encode($params), 'payment');
                    }
                }
                catch(\Exception|\Throwable $e){
                    Yii::error('Ошибка в PaymentController paypal: ' . $e->getMessage() . PHP_EOL . 'Параметры: ' . json_encode($params), 'payment');
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
    public function actionDisput() : void{
        $config = Yii::$app->params['shops']['club-dimitriev']['PayPal'];
        $client = new Client();
        $url = 'https://api-m.paypal.com/v1/oauth2/token';//For test https://api-m.sandbox.paypal.com/v1/oauth2/token
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . base64_encode($config['clientId'] . ':' . $config['secret']),
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
        $accessToken = $response->data['access_token'];
        $request = $client->createRequest()
        ->setMethod('GET')
        ->setUrl('https://api-m.paypal.com/v1/customer/disputes/')//For test https://api-m.sandbox.paypal.com/v2/checkout/orders/
        ->addHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ]);
        $response = $request->send();
        AppController::debug($response->getData(), 1);
    }

    public function actionSuccess() : void{

    }

    public function actionResult() : void{

    }

    public function actionFail() : void{
        if(isset($_REQUEST["InvId"])){//RoboKassa start
            $invId = $_REQUEST["InvId"];
            echo $invId . '|Fail';
            $sql = "SELECT shop, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $query = new Query();
            $result = $query->select('id')
                ->from('orders')
                ->where(['id' => $invId])
                ->orderBy(['id' => SORT_DESC])
                ->limit(1)
                ->scalar();
            if($result !== false){
                $shop = $result[0]['shop'];
                $data = [
                    'web_app_query_id' => $result[0]['web_app_query_id'],
                    'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                ];
                $result = AppController::curlSendMessage($data, $shop, '/answerWebAppQuery');//Todo
                if($result === false){
                    $from = 'curl';
                    $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                    fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa fail.php: ' . curl_error($ch) . PHP_EOL);
                    fclose($file);
                }
            }
            else{
                $from = 'db';
                $file = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/' . $from . '.log', 'a');
                fwrite($file, date("d.m.Y H:i:s") . ' From RoboKassa fail.php: ' . json_encode($db->errorInfo()) . PHP_EOL);
                fclose($file);
                header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
            }
            exit(0);
        }
    }

}