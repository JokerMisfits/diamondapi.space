<?php

namespace app\modules\admin\controllers;

class AppAdminController extends \yii\web\Controller{

    public function behaviors() : array{
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin']
                    ]
                ]
            ]
        ];
    }
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

    protected static function generateEncryptedConfig(string $botToken, array $robo = null, array $pay = null, array $free = null, array $pp = null) : array|string{
        $security = new \yii\base\Security;
        $key = $_SERVER['API_KEY_0'];
        $key1 = $_SERVER['API_KEY_1'];
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
}