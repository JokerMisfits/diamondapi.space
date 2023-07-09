<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\base\Security;
use yii\web\Controller;
use yii\filters\AccessControl;

class AppAdminController extends Controller{

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
            ]
        ];
    }
    protected static function debug($data, $mode = false){
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
        if($mode){exit(0);}
    }

    protected static function generateEncryptedConfig(string $botToken, array $robo = null, array $pay = null, array $free = null, array $pp = null) : array|string{
        $security = new Security;
        $key = Yii::$app->params['apikey0'];
        $key1 = Yii::$app->params['apikey1'];
        $return['bot_token'] = base64_encode($security->encryptByPassword($botToken, $key));
        if($robo !== null){
            $return['robokassa']['enable'] = $robo['enable'];
            $return['robokassa']['is_test'] = $robo['is_test'];
            $return['robokassa']['shop'] = $robo['shop'];
            $return['robokassa'][0] = base64_encode($security->encryptByPassword($robo[0], $key));
            $return['robokassa'][1] = base64_encode($security->encryptByPassword($robo[1], $key));
            $return['robokassa'][2] = base64_encode($security->encryptByPassword($robo[2], $key));
            $return['robokassa'][3] = base64_encode($security->encryptByPassword($robo[3], $key));
        }
        else{
            $return['robokassa']['enable'] = false;
        }
        $return['robokassa'] = base64_encode($security->encryptByPassword(json_encode($return['robokassa']), $key1));
        if($pay !== null){
            $return['paykassa']['enable'] = $pay['enable'];
            $return['paykassa']['is_test'] = $pay['is_test'];
            $return['paykassa']['merchant_id'] = base64_encode($security->encryptByPassword($pay['merchant_id'], $key));
            $return['paykassa']['merchant_password'] = base64_encode($security->encryptByPassword($pay['merchant_password'], $key));
            $return['paykassa']['api_id'] = base64_encode($security->encryptByPassword($pay['api_id'], $key));
            $return['paykassa']['api_password'] = base64_encode($security->encryptByPassword($pay['api_password'], $key));
        }
        else{
            $return['paykassa']['enable'] = false;
        }
        $return['paykassa'] = base64_encode($security->encryptByPassword(json_encode($return['paykassa']), $key1));
        if($free !== null){
            $return['freekassa']['enable'] = $free['enable'];
            $return['freekassa']['is_test'] = $free['is_test'];
            $return['freekassa']['secret'][0] = base64_encode($security->encryptByPassword($free['secret'][0], $key));
            $return['freekassa']['secret'][1] = base64_encode($security->encryptByPassword($free['secret'][1], $key));
            $return['freekassa']['api_key'] = base64_encode($security->encryptByPassword($free['api_key'], $key));
            $return['freekassa']['merchant_id'] = base64_encode($security->encryptByPassword($free['merchant_id'], $key));
        }
        else{
            $return['freekassa']['enable'] = false;
        }
        $return['freekassa'] = base64_encode($security->encryptByPassword(json_encode($return['freekassa']), $key1));
        if($pp !== null){
            $return['paypall']['enable'] = $pp['enable'];
            $return['paypall']['is_test'] = $pp['is_test'];
            $return['paypall']['client_id'] = base64_encode($security->encryptByPassword($pp['clientId'], $key));
            $return['paypall']['test_client_id'] = base64_encode($security->encryptByPassword($pp['testClientId'], $key));
            $return['paypall']['secret'] = base64_encode($security->encryptByPassword($pp['secret'], $key));
            $return['paypall']['test_secret'] = base64_encode($security->encryptByPassword($pp['testSecret'], $key));
        }
        else{
            $return['paypall']['enable'] = false;
        }
        $return['paypall'] = base64_encode($security->encryptByPassword(json_encode($return['paypall']), $key1));
        return $return;
    }
    // public static function AccessDenied(){
    //     Yii::$app->getSession()->setFlash('error', 'Доступ запрещен!');
    // }

    // public function beforeAction($action) : bool{
    //     if(!Yii::$app->user->isGuest){
    //         $id = Yii::$app->user->identity->id;
    //         Yii::$app->db->createCommand()
    //             ->update('accounts', ['last_activity_timestamp' => time()], "id = $id")
    //             ->execute();
    //     }
    //     return true;
    // }

}