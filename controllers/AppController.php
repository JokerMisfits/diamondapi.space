<?php

namespace app\controllers;


class AppController extends \yii\web\Controller{

    /**
     * {@inheritdoc}
     * @return array
     */
    public function behaviors() : array{
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'logout' => ['post']
                ]
            ]
        ];
    }
    
    /**
     * {@inheritdoc}
     * @return bool
     * @throws \yii\web\BadRequestHttpException|\yii\web\ForbiddenHttpException
     */
    public function beforeAction($action) : bool{
        \Yii::$app->session->set('csrf', md5(uniqid(rand(), true)));
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     * @return void
     */
    protected static function debug($data, $mode = false) : void{
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
        if($mode){exit(0);}
    }

    /**
     * {@inheritdoc}
     * @return array|false
     */
    protected static function getConfig(string $shop, bool $checkEnable = false, $method = null) : array|false{
        $sql = "SELECT bot_token, robokassa, paykassa, freekassa, paypall FROM clients WHERE shop = :shop ORDER BY id DESC limit 1";
        $result = \Yii::$app->db->createCommand($sql)
        ->bindValue(':shop', $shop)
        ->queryOne();
        if($result !== false){
            $security = new \yii\base\Security;
            $key = $_SERVER['API_KEY_0'];
            $key1 = $_SERVER['API_KEY_1'];
            $return['bot_token'] = $security->decryptByPassword(base64_decode($result['bot_token']), $key);
            if($method != null && $method == 'bot_token'){
                return $return;
            }
            if($method == null || $method == 'robokassa'){
                $robo = json_decode($security->decryptByPassword(base64_decode($result['robokassa']), $key1), true);
            }
            if($method == null || $method == 'paykassa'){
                $pay = json_decode($security->decryptByPassword(base64_decode($result['paykassa']), $key1), true);
            }
            if($method == null || $method == 'freekassa'){
                $free = json_decode($security->decryptByPassword(base64_decode($result['freekassa']), $key1), true);
            }
            if($method == null || $method == 'paypall'){
                $pp = json_decode($security->decryptByPassword(base64_decode($result['paypall']), $key1), true);
            }
            if(isset($robo['enable']) && $robo['enable'] === true && ($method == null || $method == 'robokassa')){
                $return['robokassa']['is_test'] = $robo['is_test'];
                $return['robokassa']['shop'] = $robo['shop'];
                if(!$checkEnable){
                    $return['robokassa'][0] = $security->decryptByPassword(base64_decode($robo[0]), $key);
                    $return['robokassa'][1] = $security->decryptByPassword(base64_decode($robo[1]), $key);
                    $return['robokassa'][2] = $security->decryptByPassword(base64_decode($robo[2]), $key);
                    $return['robokassa'][3] = $security->decryptByPassword(base64_decode($robo[3]), $key);
                }
                if($method == 'robokassa'){
                    return $return;
                }
                unset($robo);
            }
            if(isset($pay['enable']) && $pay['enable'] === true && ($method == null || $method == 'paykassa')){
                $return['paykassa']['is_test'] = $pay['is_test'];
                if(!$checkEnable){
                    $return['paykassa']['merchant_id'] = $security->decryptByPassword(base64_decode($pay['merchant_id']), $key);
                    $return['paykassa']['merchant_password'] = $security->decryptByPassword(base64_decode($pay['merchant_password']), $key);
                    $return['paykassa']['api_id'] = $security->decryptByPassword(base64_decode($pay['api_id']), $key);
                    $return['paykassa']['api_password'] = $security->decryptByPassword(base64_decode($pay['api_password']), $key);
                }
                if($method == 'paykassa'){
                    return $return;
                }
                unset($pay);
            }
            if(isset($free['enable']) && $free['enable'] === true && ($method == null || $method == 'freekassa')){
                $return['freekassa']['is_test'] = $free['is_test'];
                if(!$checkEnable){
                    $return['freekassa']['secret'][0] = $security->decryptByPassword(base64_decode($free['secret'][0]), $key);
                    $return['freekassa']['secret'][1] = $security->decryptByPassword(base64_decode($free['secret'][1]), $key);
                    $return['freekassa']['api_key'] = $security->decryptByPassword(base64_decode($free['api_key']), $key);
                    $return['freekassa']['merchant_id'] = $security->decryptByPassword(base64_decode($free['merchant_id']), $key);
                }
                if($method == 'freekassa'){
                    return $return;
                }
                unset($free);
            }
            if(isset($pp['enable']) && $pp['enable'] === true && ($method == null || $method == 'paypall')){
                $return['paypall']['is_test'] = $pp['is_test'];
                if(!$checkEnable){
                    $return['paypall']['client_id'] = $security->decryptByPassword(base64_decode($pp['client_id']), $key);
                    $return['paypall']['test_client_id'] = $security->decryptByPassword(base64_decode($pp['test_client_id']), $key);
                    $return['paypall']['secret'] = $security->decryptByPassword(base64_decode($pp['secret']), $key);
                    $return['paypall']['test_secret'] = $security->decryptByPassword(base64_decode($pp['test_secret']), $key);
                }
                if($method == 'paypall'){
                    return $return;
                }
                unset($pp);
            }
            return $return;
        }
        else{
            return false;
        }
    }

    /**
     * {@inheritdoc}
     * @return string|false
     */
    private static function getBotToken(string $shop) : string|false{
        $sql = "SELECT bot_token FROM clients WHERE shop = :shop ORDER BY id DESC limit 1";
        $result = \Yii::$app->db->createCommand($sql)
        ->bindValue(':shop', $shop)
        ->queryOne();
        if($result !== false){
            $security = new \yii\base\Security;
            $key = $_SERVER['API_KEY_0'];
            return $security->decryptByPassword(base64_decode($result['bot_token']), $key);
        }
        return false;
    }

    /**
     * {@inheritdoc}
     * @return string|bool
     */
    protected static function curlSendMessage(array $data, string $shop, string $method = '/sendMessage') : string|bool{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . self::getBotToken($shop) . $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        if($result === false){
            \Yii::error('Method: ' . $method . ' ,shop: ' . $shop . ', Curl ошибка отправки сообщения : ' .  curl_error($ch), 'curl');
        }
        curl_close($ch);
        return $result;
    }

    /**
     * {@inheritdoc}
     * @return bool
     */
    protected static function sendMail(string $to, string $subject, string $message, string $from = 'noreply@diamondapi.space', array $copy = null) : bool{
        $mailSent = mail($to, $subject, $message, self::getHeaders($from));
        return $mailSent;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    private static function getHeaders(string $from = 'noreply@diamondapi.space', array $copy = null) : string{
        if($copy == null){
            return 'From: ' . $from . "\r\n" .
            'Cc: backup@diamondapi.space' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        }
        else{
            $copy = implode(', ', $copy);
            return 'From: ' . $from . "\r\n" .
            'Cc: ' . $copy . ', backup@diamondapi.space' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        }
    }

}