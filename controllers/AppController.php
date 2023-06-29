<?php

namespace app\controllers;

use Yii;
use yii\base\Security;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class AppController extends Controller{

    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::class,
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
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post']
                ]
            ]
        ];
    }

    protected static function debug($data, $mode = false){
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
        if($mode){exit(0);}
    }
    
    public function beforeAction($action){
        Yii::$app->session->set('csrf', md5(uniqid(rand(), true)));
        return parent::beforeAction($action);
    }

    protected static function getConfig(string $shop, bool $checkEnable = false) : array|false {
        $sql = "SELECT bot_token, robokassa, paykassa, freekassa, paypall FROM clients WHERE shop = :shop ORDER BY id DESC limit 1";
        $result = Yii::$app->db->createCommand($sql)
        ->bindValue(':shop', $shop)
        ->queryOne();
        if($result !== false){
            $security = new Security;
            $key = Yii::$app->params['apikey0'];
            $key1 = Yii::$app->params['apikey1'];
            $return['bot_token'] = $security->decryptByPassword(base64_decode($result['bot_token']), $key);
            $robo = json_decode($security->decryptByPassword(base64_decode($result['robokassa']), $key1), true);
            $pay = json_decode($security->decryptByPassword(base64_decode($result['paykassa']), $key1), true);
            $free = json_decode($security->decryptByPassword(base64_decode($result['freekassa']), $key1), true);
            $pp = json_decode($security->decryptByPassword(base64_decode($result['paypall']), $key1), true);
            if(isset($robo['enable']) && $robo['enable'] === true){
                $return['robokassa']['is_test'] = $robo['is_test'];
                if(!$checkEnable){
                    $return['robokassa'][0] = $security->decryptByPassword(base64_decode($robo[0]), $key);
                    $return['robokassa'][1] = $security->decryptByPassword(base64_decode($robo[1]), $key);
                    $return['robokassa'][2] = $security->decryptByPassword(base64_decode($robo[2]), $key);
                    $return['robokassa'][3] = $security->decryptByPassword(base64_decode($robo[3]), $key);
                }
                unset($robo);
            }
            if(isset($pay['enable']) && $pay['enable'] === true){
                $return['paykassa']['is_test'] = $pay['is_test'];
                $return['paykassa']['merchant_id'] = $security->decryptByPassword(base64_decode($pay['merchant_id']), $key);
                $return['paykassa']['merchant_password'] = $security->decryptByPassword(base64_decode($pay['merchant_password']), $key);
                $return['paykassa']['api_id'] = $security->decryptByPassword(base64_decode($pay['api_id']), $key);
                $return['paykassa']['api_password'] = $security->decryptByPassword(base64_decode($pay['api_password']), $key);
                unset($pay);
            }
            if(isset($free['enable']) && $free['enable'] === true){
                $return['freekassa']['is_test'] = $free['is_test'];
                if(!$checkEnable){
                    $return['freekassa']['secret'][0] = $security->decryptByPassword(base64_decode($free['secret'][0]), $key);
                    $return['freekassa']['secret'][1] = $security->decryptByPassword(base64_decode($free['secret'][1]), $key);
                    $return['freekassa']['api_key'] = $security->decryptByPassword(base64_decode($free['api_key']), $key);
                    $return['freekassa']['merchant_id'] = $security->decryptByPassword(base64_decode($free['merchant_id']), $key);
                }
                unset($free);
            }
            if(isset($pp['enable']) && $pp['enable'] === true){
                $return['paypall']['is_test'] = $pp['is_test'];
                if(!$checkEnable){
                    $return['paypall']['client_id'] = $security->decryptByPassword(base64_decode($pp['client_id']), $key);
                    $return['paypall']['test_client_id'] = $security->decryptByPassword(base64_decode($pp['test_client_id']), $key);
                    $return['paypall']['secret'] = $security->decryptByPassword(base64_decode($pp['secret']), $key);
                    $return['paypall']['test_secret'] = $security->decryptByPassword(base64_decode($pp['test_secret']), $key);
                }
                unset($pp);
            }
            return $return;
        }
        else{
            return false;
        }
    }

    private static function getBotToken(string $shop) : string|false{
        $sql = "SELECT bot_token FROM clients WHERE shop = :shop ORDER BY id DESC limit 1";
        $result = Yii::$app->db->createCommand($sql)
        ->bindValue(':shop', $shop)
        ->queryOne();
        if($result !== false){
            $security = new Security;
            $key = Yii::$app->params['apikey0'];
            return $security->decryptByPassword(base64_decode($result['bot_token']), $key);
        }
        return false;
    }

    protected static function curlSendMessage(array $data, string $shop, string $method = '/sendMessage'){
        self::debug(self::getBotToken($shop), 1);//Todo уддаалить после теста
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . self::getBotToken($shop) . $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}