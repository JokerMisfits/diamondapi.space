<?php

//Todo написать JS закрытия окна если доступен webapp
//TODO НАПИСАТЬ ВАЛИДАЦИЮ CRC freekassa|success
//TODO НАПИСАТЬ PAYPALL|SUCCESS
//TODO ПРОПИСАТЬ TRY CATCH ДЛЯ SUCCESS RESULT

namespace app\controllers;

class PaymentController extends AppController{

    /**
     * {@inheritdoc}
     * @return bool
     * @throws \yii\web\BadRequestHttpException|\yii\web\ForbiddenHttpException
     */
    public function beforeAction($action) : bool{
        $params = \Yii::$app->request->get();
        if($action->id == 'index'){
            if(array_key_exists('shop', $params) && array_key_exists('count', $params) && array_key_exists('name', $params)  && array_key_exists('userId', $params) && array_key_exists('days', $params) && array_key_exists('hash', $params)){
                $params['count'] = intval($params['count']);
                if(md5($_SERVER['API_KEY_0'] . $params['count'] . $params['userId'] . $params['shop'] . $params['days'] . $_SERVER['API_KEY_1']) == $params['hash'] && $params['count'] > 0){
                    if(\Yii::$app->session->has('csrf')){
                        return parent::beforeAction($action);
                    }
                    else{
                        \Yii::$app->session->set('csrf', md5(uniqid(rand(), true)));
                        return parent::beforeAction($action);
                    }
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }
        elseif($action->id == 'route'){
            if(array_key_exists('webApp', $params) && $params['webApp'] != ''){
                return parent::beforeAction($action);
            }
            else{
                return false;
            }
        }
        elseif($action->id == 'confirmation'){//Todo удалить после теста ЭТО ДЛЯ PAYPALL ПОДТВЕРЖДЕНИЕ ВЫПОЛНЕНИЯ ЗАКАЗА
            if(array_key_exists('token', $params)){
                return false;
            }
            else{
                return false;
            }
        }
        elseif($action->id === 'success'){
            $this->enableCsrfValidation = false;
            return parent::beforeAction($action); 
        }
        elseif($action->id === 'result'){
            if(\Yii::$app->request->isPost){
                $this->enableCsrfValidation = false;
                sleep(10);
                return parent::beforeAction($action); 
            }
            else{
                return false;
            }
        }
        elseif($action->id === 'fail'){
            if(\Yii::$app->request->isPost){
                $this->enableCsrfValidation = false;
                return parent::beforeAction($action);
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    /**
     * {@inheritdoc}
     * @return string
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionIndex() : string{
        $params = \Yii::$app->request->get();
        $config = AppController::getConfig($params['shop'], true);
        if(isset($config['paykassa'])){
            $pk = AppController::getConfig($params['shop'], false, 'paykassa');
            $params['pk'] = new \Paykassa\PaykassaSCI($pk['paykassa']["merchant_id"], $pk['paykassa']["merchant_password"], $pk['paykassa']["is_test"]);
        }
        return $this->render('index', ['params' => $params, 'csrf' => \Yii::$app->session->get('csrf'), 'config' => $config]);
    }

    /**
     * {@inheritdoc}
     * @return void
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionRoute() : void{
        $params = \Yii::$app->request->get();
        $name = $params['name'];
        $count = intval($params['count']);
        if($params['method'] == 'RoboKassa'){//RoboKassa start
            $config = AppController::getConfig($params['shop'], false, 'robokassa')['robokassa'];
            if(!empty($config)){
                $isTest = $config['is_test'];
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
                    $result = \Yii::$app->db->createCommand($sql, $qParams)->execute();
                    if($result !== false){
                        $query = new \yii\db\Query();
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
                            \Yii::$app->getResponse()->redirect($url)->send();
                            exit(0);
                        }
                        else{
                            \Yii::error('Method robo db SELECT, не получилось извлечь ID: ' . \Yii::$app->db->getSchema()->errorInfo());
                        }
                    }
                    else{
                        \Yii::error('Method robo db, не получилось записать: ' . \Yii::$app->db->getSchema()->errorInfo());
                    }
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
                catch(\Exception|\Throwable $e){
                    \Yii::error('Ошибка в PaymentController Route RoboKassa: ' . $e->getMessage());
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
            }
            else{
                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif($params['method'] == 'PayKassa'){//PayKassa start
            $config = AppController::getConfig($params['shop'], false, 'paykassa')['paykassa'];
            if(!empty($config)){
                $isTest = $config['is_test'];
                $pk = new \Paykassa\PaykassaSCI($config['merchant_id'], $config['merchant_password'], $isTest);
                @list($system, $currency) = preg_split('~_(?=[^_]*$)~', $_POST["pscur"]);
                $pairs = 'RUB_' . strtoupper($currency);
                try{
                    $result = \Paykassa\PaykassaCurrency::getCurrencyPairs([$pairs]);
                    if($result['error']){
                        \Yii::error('Method pay не получилось получить курс валютной пары: ' . json_encode($result['message']));
                        throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
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
                            ':count_in_currency' => $count
                        ];
                        $result = \Yii::$app->db->createCommand($sql, $qParams)->execute();
                        if($result !== false){
                            $query = new \yii\db\Query();
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
                                    $params['shop'] . ' ' . $name . ' ' . $params['hash'],
                                );
                                if($result['error']){
                                    \Yii::error('Method pay не получилось создать заказ в PayKassa: ' . json_encode($result['message']));
                                }
                                else{
                                    \Yii::$app->getResponse()->redirect($result['data']['url'])->send();
                                    exit(0);                  
                                }
                            }
                            else{
                                \Yii::error('Method pay db SELECT, не получилось извлечь ID: ' . \Yii::$app->db->getSchema()->errorInfo());
                            }
                        }
                        else{
                            \Yii::error('Method pay db, не получилось записать: ' . \Yii::$app->db->getSchema()->errorInfo());
                        }
                    }
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
                catch(\Exception|\Throwable $e){
                    \Yii::error('Ошибка в PaymentController Route PayKassa: ' . $e->getMessage());
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
            }
            else{
                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif($params['method'] == 'FreeKassa'){//FreeKassa start
            $config = AppController::getConfig($params['shop'], false, 'freekassa')['freekassa'];
            if(!empty($config)){
                $isTest = $config['is_test'];
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
                    $result = \Yii::$app->db->createCommand($sql, $qParams)->execute();
                    if($result !== false){
                        $query = new \yii\db\Query();
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
                            \Yii::$app->getResponse()->redirect($url)->send();
                            exit(0);
                        }
                        else{
                            \Yii::error('Method free db SELECT, не получилось извлечь ID: ' . \Yii::$app->db->getSchema()->errorInfo());
                        }
                    }
                    else{
                        \Yii::error('Method free db, не получилось записать: ' . \Yii::$app->db->getSchema()->errorInfo());
                    }
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
                catch(\Exception|\Throwable $e){
                    \Yii::error('Ошибка в PaymentController Route FreeKassa: ' . $e->getMessage());
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
            }
            else{
                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif($params['method'] == 'PayPall'){//PayPal start
            $config = AppController::getConfig($params['shop'], false, 'paypall')['paypall'];
            if(!empty($config)){
                $isTest = $config['is_test'];
                $client = new \yii\httpclient\Client();
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
                        $result = \Yii::$app->db->createCommand($sql, $qParams)->execute();
                        if($result != false){
                            $query = new \yii\db\Query();
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
                                    "intent" => 'CAPTURE',
                                    "purchase_units" => [
                                        [
                                            "reference_id" => $invId,
                                            "amount" => [
                                                "currency_code" => 'RUB',
                                                "value" => $count
                                            ]
                                        ]
                                    ],
                                    "payment_source" => [
                                        "paypal" => [
                                            "experience_context" => [
                                                "payment_method_preference" => 'IMMEDIATE_PAYMENT_REQUIRED',
                                                "payment_method_selected" => 'PAYPAL',
                                                "brand_name" => $params['shop'],
                                                "locale" => 'en-US',//ru-Ru
                                                "landing_page" => 'LOGIN',
                                                "shipping_preference" => 'NO_SHIPPING',
                                                "user_action" => 'PAY_NOW',
                                                "return_url" => \Yii::$app->params['host'] . 'success.php',
                                                "cancel_url" => \Yii::$app->params['host'] . '/fail.php'
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
                                        $result = \Yii::$app->db->createCommand($sql)
                                            ->bindValue(':ppid', $ppid)
                                            ->bindValue(':order_id', $invId)
                                            ->execute();
                                        if($result !== false){
                                            \Yii::$app->getResponse()->redirect($response->getData()['links'][1]['href'])->send();
                                            exit(0);
                                        }
                                        else{
                                            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                                        }
                                    }
                                    else{
                                        throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                                    }
                                }
                                else{
                                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                                }
                            }
                            else{
                                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                            }
                        }
                        else{
                            \Yii::error('Method paypal db, не получилось записать INSERT: ' . \Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params));
                        }
                    }
                    catch(\Exception|\Throwable $e){
                        \Yii::error('Ошибка в PaymentController paypal: ' . $e->getMessage() . PHP_EOL . ' Параметры: ' . json_encode($params));
                        throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                    }
                }
                else{
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
            }
            else{
                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        else{
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
    }

    /**
     * {@inheritdoc}
     * @return void
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionConfirmation() : void{
        $params = \Yii::$app->request->get();
        if(isset($params['token']) && isset($params['PayerID'])){
            try{
                $sql = "SELECT id, status, shop FROM orders WHERE paypal_order_id = :ppid ORDER BY id DESC limit 1";
                $result = \Yii::$app->db->createCommand($sql)
                    ->bindValue(':ppid', $params['token'])
                    ->queryOne();
                if($result != false){
                    if(isset($result['id']) && isset($result['status']) && isset($result['shop']) && $result['status'] == 0){
                        $config = \Yii::$app->params['shops'][$result['shop']]['PayPal'];
                        $base64Token = base64_encode($config['clientId'] . ':' . $config['secret']);
                        if(isset($config)){
                            $client = new \yii\httpclient\Client();
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
                                            $result = \Yii::$app->db->createCommand($sql)
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
                                                    exit(0);
                                                }
                                                else{//Todo ЕСТЬ ТАКАЯ МЫСЛЬ, ЧТО ЕСЛИ ДОЛГО НЕ ПОДТВЕРЖДАТЬ, PAYPAL ШЛЕТ НАС
                                                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);//Todo rollback + message
                                                }
                                            }
                                            else{
                                                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                                            }  
                                        }
                                        else{
                                            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                                        }
                                    }
                                    else{
                                        throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                                    }
                                }
                                else{
                                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                                }
                            }
                            else{
                                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                            }
                        }
                        else{
                            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                        }
                    }
                    else{
                        throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                    }
                }
                else{
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
            }
            catch(\Exception|\Throwable $e){
                \Yii::error('Ошибка в PaymentController Confirmation: ' . $e->getMessage());
                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        else{
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
    }

    /**
     * {@inheritdoc}
     * @return void
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionSuccess() : void{//todo дописать для PayPall
        $params = \Yii::$app->request->post();
        if(isset($params['SignatureValue']) && isset($params['InvId']) && isset($params['OutSum'])){//RoboKassa start
            $invId = $params['InvId'];
            $sql = "SELECT `status`, shop, access_days, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = \Yii::$app->db->createCommand($sql)
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
                    \Yii::error('Method RoboKassa|Success crc: ' . json_encode($params));
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
                if($result['status'] == 1){//Todo написать JS закрытия окна если доступен webapp
                    echo 'Заказ: ' . $invId . ' Успешно оплачен. ' . PHP_EOL . 'Данную страницу можно закрывать.';
                    exit(0);
                }
                else{
                    $sql = "UPDATE orders SET `status` = 1 WHERE id = :order_id;";
                    $result = \Yii::$app->db->createCommand($sql)
                    ->bindValue(':order_id', $invId)
                    ->execute();
                    if($result !== false){
                        echo 'Заказ: ' . $invId . ' Успешно оплачен. ' . PHP_EOL . 'Данную страницу можно закрывать.';
                        $data = [
                            'web_app_query_id' => $webAppQueryId,
                            'result' => '{"type":"article","id":"1","title":"success","input_message_content":{"message_text":"Success' . $accessDays . '"}}',
                        ];
                        AppController::curlSendData($data, $shop, '/answerWebAppQuery');
                    }
                    else{
                        echo $invId . '|Fail';
                        \Yii::error('Method RoboKassa|Success db, не получилось изменить статус заказа: ' . \Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params));
                        $data = [
                            'web_app_query_id' => $webAppQueryId,
                            'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                        ];
                        AppController::curlSendData($data, $shop, '/answerWebAppQuery');
                    }
                    exit(0);
                }
            }
            else{
                \Yii::error('Method RoboKassa|Success db, не получилось извлечь заказ: ' . \Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params));
                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif(isset($params['sign']) && isset($params['order_id']) && isset($params['amount']) && isset($params['status'])){//PayKassa start
            $invId = $params['order_id'];
            $sql = "SELECT `status`, shop, access_days, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = \Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
            if($result !== false){
                $shop = $result['shop'];
                $accessDays = $result['access_days'];
                $webAppQueryId = $result['web_app_query_id'];
                $config = AppController::getConfig($shop, false, 'paykassa')['paykassa'];
                $crc = array($params['amount'], $config['merchant_id'], $params['order_id'], $params['status'], $config['merchant_password']);
                if(md5(implode(':', $crc) != $params['sign'])){
                    \Yii::error('Method PayKassa|Success crc: ' . json_encode($params));
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
                if($result['status'] == 1){//Todo написать JS закрытия окна если доступен webapp
                    echo 'Заказ: ' . $invId . ' Успешно оплачен. ' . PHP_EOL . 'Данную страницу можно закрывать.';
                    exit(0);
                }
                else{
                    $sql = "UPDATE orders SET `status` = 1 WHERE id = :order_id;";
                    $result = \Yii::$app->db->createCommand($sql)
                    ->bindValue(':order_id', $invId)
                    ->execute();
                    if($result !== false){
                        echo 'Заказ: ' . $invId . ' Успешно оплачен. ' . PHP_EOL . 'Данную страницу можно закрывать.';
                        $data = [
                            'web_app_query_id' => $webAppQueryId,
                            'result' => '{"type":"article","id":"1","title":"success","input_message_content":{"message_text":"Success' . $accessDays . '"}}',
                        ];
                        AppController::curlSendData($data, $shop, '/answerWebAppQuery');
                    }
                    else{
                        echo $invId . '|Fail';
                        \Yii::error('Method PayKassa|Success db, не получилось изменить статус заказа: ' . \Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params));
                        $data = [
                            'web_app_query_id' => $webAppQueryId,
                            'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                        ];
                        AppController::curlSendData($data, $shop, '/answerWebAppQuery');
                    }
                    exit(0);
                }
            }
            else{
                \Yii::error('Method PayKassa|Success db, не получилось извлечь заказ: ' . \Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params));
                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif(isset($params['MERCHANT_ORDER_ID']) && isset($params['intid'])){//FreeKassa start
            $invId = $params['MERCHANT_ORDER_ID'];
            $sql = "SELECT `status`, shop, access_days, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";//TODO НАПИСАТЬ ВАЛИДАЦИЮ CRC
            $result = \Yii::$app->db->createCommand($sql)
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
                    $result = \Yii::$app->db->createCommand($sql)
                    ->bindValue(':order_id', $invId)
                    ->execute();
                    if($result !== false){
                        echo 'Заказ: ' . $invId . ' Успешно оплачен. ' . PHP_EOL . 'Данную страницу можно закрывать.';
                        $data = [
                            'web_app_query_id' => $webAppQueryId,
                            'result' => '{"type":"article","id":"1","title":"success","input_message_content":{"message_text":"Success' . $accessDays . '"}}',
                        ];
                        AppController::curlSendData($data, $shop, '/answerWebAppQuery');
                    }
                    else{
                        echo $invId . '|Fail';
                        \Yii::error('Method FreeKassa|Success db, не получилось изменить статус заказа: ' . \Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params));
                        $data = [
                            'web_app_query_id' => $webAppQueryId,
                            'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                        ];
                        AppController::curlSendData($data, $shop, '/answerWebAppQuery');
                    }
                    exit(0);
                }
            }
            else{
                \Yii::error('Method FreeKassa|Success db, не получилось извлечь заказ: ' . \Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params));
                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        elseif(isset($params['НАПИСАТЬ ДЛЯ PAYPALL'])){//TODO ДОПИСАТЬ ДЛЯ PAYPALL
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
        else{
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
    }

    /**
     * {@inheritdoc}
     * @return void
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionResult() : void{//TODO Дописать RESULT для PAYPALL
        $params = \Yii::$app->request->post();
        if(isset($params['InvId']) && isset($params['OutSum']) && isset($params['crc'])){//RoboKassa start
            $invId = $params['InvId'];
            $sql = "SELECT tg_user_id, `status`, access_days, method, shop, is_test FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = \Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
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
                    \Yii::error('Method RoboKassa|Result crc: ' . json_encode($params));
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
                else{
                    $sql = "UPDATE orders SET `status` = 1 WHERE id = :order_id;";
                    $result = \Yii::$app->db->createCommand($sql)
                    ->bindValue(':order_id', $invId)
                    ->execute();
                    if($result !== false){
                        if(!$isTest){
                            self::orderComplete($invId, $shop, 'RoboKassa', $params['Fee'], $params['IncCurrLabel']);
                        }
                        echo 'OK' . $invId . '\n';
                        AppController::curlSendData(self::getResultButton($userId, $days), $shop);
                    }
                    else{
                        \Yii::error('Method RoboKassa|Result db, не получилось изменить статус заказа: ' . \Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params));
                    }
                }
                exit(0);
            }
            elseif($result !== false && $result['status'] == 1 && $result['method'] == 'RoboKassa'){
                if(!$isTest){
                    self::orderComplete($invId, $shop, 'RoboKassa', $params['Fee'], $params['IncCurrLabel']);
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
            $result = \Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
            $shop = $result['shop'];
            $isTest = $result['is_test'];
            if($result !== false && $result['status'] == 0 && $result['method'] == 'PayKassa'){
                $userId = $result['tg_user_id'];
                $days = $result['access_days'];
                $config = AppController::getConfig($shop, false, 'paykassa')['paykassa'];
                $paykassa = new \Paykassa\PaykassaSCI($config['merchant_id'], $config['merchant_password'], $config['is_test']);
                $result = $paykassa->checkOrderIpn($params['private_hash']);
                if($result['error']){
                    echo $invId . '|success';
                    \Yii::error('Method PayKassa|Result crc: ' . json_encode($result['message']));
                    exit(0);
                }
                else{
                    $sql = "UPDATE orders SET `status` = 1 WHERE id = :order_id;";
                    $result = \Yii::$app->db->createCommand($sql)
                    ->bindValue(':order_id', $invId)
                    ->execute();
                    if($result !== false){
                        if(!$isTest){
                            self::orderComplete($invId, $shop, 'PayKassa', 0);
                        }
                        echo $invId . '|success';
                        AppController::curlSendData(self::getResultButton($userId, $days), $shop);
                    }
                    else{
                        \Yii::error('Method PayKassa|Result db, не получилось изменить статус заказа: ' . \Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params));
                    }
                }
                exit(0);
            }
            elseif($result !== false && $result['status'] == 1 && $result['method'] == 'PayKassa'){
                if(!$isTest){
                    self::orderComplete($invId, $shop, 'PayKassa', 0);
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
                throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
            $invId = intval($params['MERCHANT_ORDER_ID']);
            $sql = "SELECT tg_user_id, `status`, count, access_days, method, shop, is_test FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = \Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
            $shop = $result['shop'];
            $isTest = $result['is_test'];
            if($result !== false && $result['status'] == 0 && $result['method'] == 'FreeKassa'){
                $userId = $result['tg_user_id'];
                $days = $result['access_days'];
                if($params['SIGN'] != md5($params['MERCHANT_ID'] . ':' . $params['AMOUNT'] . ':' . AppController::getConfig($shop, false, 'freekassa')['freekassa']['secret'][1] . ':' . $params['MERCHANT_ORDER_ID'])){//CRC
                    echo 'OK' . $invId . '\n';
                    \Yii::error('Method FreeKassa|Result crc: ' . json_encode($params));
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
                }
                else{
                    $sql = "UPDATE orders SET `status` = 1 WHERE id = :order_id;";
                    $result = \Yii::$app->db->createCommand($sql)
                    ->bindValue(':order_id', $invId)
                    ->execute();
                    if($result !== false){
                        if(!$isTest){
                            self::orderComplete($invId, $shop, 'FreeKassa', $params['commission']);
                        }
                        echo 'YES';
                        AppController::curlSendData(self::getResultButton($userId, $days), $shop);
                    }
                    else{
                        \Yii::error('Method FreeKassa|Result db, не получилось изменить статус заказа: ' . \Yii::$app->db->getSchema()->errorInfo() . PHP_EOL . ' Параметры: ' . json_encode($params));
                    }
                    exit(0);
                }
            }
            elseif($result !== false && $result['status'] == 1 && $result['method'] == 'FreeKassa'){
                if(!$isTest){
                    self::orderComplete($invId, $shop, 'FreeKassa', $params['commission']);
                }
                echo 'YES';
            }
            else{
                echo 'YES';
            }
            exit(0);
        }
        elseif(isset($params['НАПИСАТЬ ДЛЯ PAYPALL'])){//TODO ДОПИСАТЬ ДЛЯ PAYPALL
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
        else{
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
    }

    /**
     * {@inheritdoc}
     * @return void
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionFail() : void{
        $params = \Yii::$app->request->post();
        if(array_key_exists('InvId', $params)){//RoboKassa start
            $invId = $params['InvId'];
            echo $invId . '|Fail';
            $sql = "SELECT shop, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = \Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
            if($result !== false){
                $shop = $result['shop'];
                $data = [
                    'web_app_query_id' => $result['web_app_query_id'],
                    'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                ];
                AppController::curlSendData($data, $shop, '/answerWebAppQuery');
            }
            else{
                \Yii::error('Method robo|Fail db SELECT, не получилось извлечь shop, web_app_query_id: ' . \Yii::$app->db->getSchema()->errorInfo());
            }
            exit(0);
        }
        elseif(array_key_exists('order_id', $_GET)){//PayKassa start
            $invId = $_GET['order_id'];
            echo $invId . '|Fail';
            $sql = "SELECT shop, web_app_query_id FROM orders WHERE id = :order_id ORDER BY id DESC limit 1";
            $result = \Yii::$app->db->createCommand($sql)
            ->bindValue(':order_id', $invId)
            ->queryOne();
            if($result !== false){
                $shop = $result['shop'];
                $data = [
                    'web_app_query_id' => $result['web_app_query_id'],
                    'result' => '{"type":"article","id":"1","title":"fail","input_message_content":{"message_text":"Fail"}}',
                ];
                AppController::curlSendData($data, $shop, '/answerWebAppQuery');
            }
            else{
                \Yii::error('Method pay|Fail db SELECT, не получилось извлечь shop, web_app_query_id: ' . \Yii::$app->db->getSchema()->errorInfo());
            }
            exit(0);
        }
        else{
            \Yii::error('Отладка Payment|Fail POST: ' . json_encode($_POST) . ' GET: ' . json_encode($_GET));
            echo 'Fail';
            exit(0);
        }
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    private static function getResultButton(int $userId, int $days) : array{
        return [
            'chat_id' => $userId,
            'text' => 'Оплата прошла успешно',
            'reply_markup' => [
                'inline_keyboard' => [
                    [  
                        [
                            'text' => 'Нажмите, чтобы активировать услугу',
                            'callback_data' => 'Success' . $days
                        ]
                    ]
                ],
                'resize_keyboard' => true
            ]
        ];
    }

    /**
     * {@inheritdoc}
     * @return void
     */
    private static function orderComplete(int $invId, string $shop, string $method, float $fee, string|null $paymentMethod = null) : void{
        $sql = "SELECT COUNT(*) FROM orders_complete WHERE order_id = :order_id";
        $result = \Yii::$app->db->createCommand($sql)
        ->bindValue(':order_id', $invId)
        ->queryOne();
        if($result['COUNT(*)'] === 0){
            $sql = "INSERT INTO orders_complete (shop, method, payment_method, fee, order_id) 
            VALUES (:shop, :method, :payment_method, :fee, :order_id)";
            $result = \Yii::$app->db->createCommand($sql)
            ->bindValue(':shop', $shop)
            ->bindValue(':method', $method)
            ->bindValue(':payment_method', $paymentMethod)
            ->bindValue(':fee', round(floatval($fee), 2))
            ->bindValue(':order_id', $invId)
            ->execute();
            if($result === false){
                \Yii::error('Method ' . $method . '|orderComplete db, Ошибка записи| ' . ' Параметры: ' . json_encode([$invId, $shop, $method, $fee, $paymentMethod], 1));
            }
        }
        else{
            \Yii::error('Method ' . $method . '|orderComplete db, повторное срабатывание| ' . ' Параметры: ' . json_encode([$invId, $shop, $method, $fee, $paymentMethod], 1));
        }
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    private static function getIP() : string{
        if(isset($_SERVER['HTTP_X_REAL_IP'])) return $_SERVER['HTTP_X_REAL_IP'];
        return $_SERVER['REMOTE_ADDR'];
    }

}