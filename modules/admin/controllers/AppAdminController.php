<?php

namespace app\modules\admin\controllers;

class AppAdminController extends \yii\web\Controller{

    /**
     * {@inheritdoc}
     *
     * @return array
     */
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

    /**
     * {@inheritdoc}
     *
     * @param mixed $data data
     * @param bool $mode mode
     * @return void
     */
    protected static function debug(mixed $data, bool $mode = false) : void{
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
        if($mode){exit(0);}
    }

    /**
     * {@inheritdoc}
     *
     * @param string $shop shop
     * @param bool $checkEnable check enable
     * @param string $method method
     * @param bool $skipEnable skip enable check
     * @return array|false
     */
    protected static function getConfig(string $shop, bool $checkEnable = false, string $method = null, bool $skipEnable = false) : array|false{
        $sql = "SELECT config_version, payment_alias, bot_token, robokassa, paykassa, freekassa, paypall FROM clients WHERE shop = :shop ORDER BY id DESC limit 1";
        $result = \Yii::$app->db->createCommand($sql)
        ->bindValue(':shop', $shop)
        ->queryOne();
        if($result !== false){
            $security = new \yii\base\Security;
            $key = $_SERVER['API_KEY_0'];
            $key1 = $_SERVER['API_KEY_1'];
            if($result['config_version'] !== null){
                $return['config_version'] = $security->decryptByPassword(base64_decode($result['config_version']), $key);
            }
            else{
                $return['config_version'] = null;
            }
            if($result['payment_alias'] !== null){
                $return['payment_alias'] = $security->decryptByPassword(base64_decode($result['payment_alias']), $key);
            }
            else{
                $return['payment_alias'] = null;
            }
            if($result['bot_token'] !== null){
                $return['bot_token'] = $security->decryptByPassword(base64_decode($result['bot_token']), $key);
            }
            else{
                $return['bot_token'] = null;
            }
            if($method != null && $method == 'bot_token'){
                return $return;
            }
            if(($method == null || $method == 'robokassa') && $result['robokassa'] !== null){
                $robo = json_decode($security->decryptByPassword(base64_decode($result['robokassa']), $key1), true);
            }
            if(($method == null || $method == 'paykassa') && $result['paykassa'] !== null){
                $pay = json_decode($security->decryptByPassword(base64_decode($result['paykassa']), $key1), true);
            }
            if(($method == null || $method == 'freekassa') && $result['freekassa'] !== null){
                $free = json_decode($security->decryptByPassword(base64_decode($result['freekassa']), $key1), true);
            }
            if(($method == null || $method == 'paypall') && $result['paypall'] !== null){
                $pp = json_decode($security->decryptByPassword(base64_decode($result['paypall']), $key1), true);
            }
            if(isset($robo['enable']) && ($robo['enable'] === true || $skipEnable) && ($method == null || $method == 'robokassa')){
                $return['robokassa']['enable'] = $robo['enable'];
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
            if(isset($pay['enable']) && ($pay['enable'] === true || $skipEnable) && ($method == null || $method == 'paykassa')){
                $return['paykassa']['enable'] = $pay['enable'];
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
            if(isset($free['enable']) && ($free['enable'] === true || $skipEnable) && ($method == null || $method == 'freekassa')){
                $return['freekassa']['enable'] = $free['enable'];
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
            if(isset($pp['enable']) && ($pp['enable'] === true || $skipEnable) && ($method == null || $method == 'paypall')){
                $return['paypall']['enable'] = $pp['enable'];
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
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @param int|null $paymentAlias payment alias
     * @param string|null $method payment method
     * @return array|null
     */
    protected static function generateConfig(int|null $paymentAlias = null, string|null $method = null) : array|null{

        if($paymentAlias === null){
            $paymentAlias = $_SERVER['CURRENT_PAYMENT_ALIAS'];
        }

        if($method === null){
            $result['robo'] = [
                'enable' => true,
                'is_test' => false,
                'shop' => $_SERVER['PARAMS_SHOP_' . $paymentAlias . '_0'],
                0 => $_SERVER['PARAMS_ROBOKASSA_PASSWORD_' . $paymentAlias . '_0'],
                1 => $_SERVER['PARAMS_ROBOKASSA_PASSWORD_' . $paymentAlias . '_1'],
                2 => $_SERVER['PARAMS_ROBOKASSA_PASSWORD_' . $paymentAlias . '_2'],
                3 => $_SERVER['PARAMS_ROBOKASSA_PASSWORD_' . $paymentAlias . '_3']
            ];
            $result['pay'] = [
                'enable' => true,
                'is_test' => false,
                'merchant_id' => $_SERVER['PARAMS_PAYKASSA_MERCHANT_' . $paymentAlias . '_0'],
                'merchant_password' => $_SERVER['PARAMS_PAYKASSA_MERCHANT_PASSWORD_' . $paymentAlias . '_0'],
                'api_id' => $_SERVER['PARAMS_PAYKASSA_API_' . $paymentAlias . '_0'],
                'api_password' => $_SERVER['PARAMS_PAYKASSA_API_PASSWORD_' . $paymentAlias . '_0']
            ];
            $result['free'] = [
                'enable' => true,
                'is_test' => false,
                'secret' => [
                    0 => $_SERVER['PARAMS_FREEKASSA_PASSWORD_' . $paymentAlias . '_0'],
                    1 => $_SERVER['PARAMS_FREEKASSA_PASSWORD_' . $paymentAlias . '_1']
                ],
                'api_key' => $_SERVER['PARAMS_FREEKASSA_APIKEY_' . $paymentAlias . '_0'],
                'merchant_id' => $_SERVER['PARAMS_FREEKASSA_MERCHANT_' . $paymentAlias . '_0']
            ];
            $result['pp'] = [
                'enable' => true,
                'is_test' => false,
                'clientId' => $_SERVER['PARAMS_PAYPAL_CLIENT_' . $paymentAlias . '_0'],
                'testClientId' => $_SERVER['PARAMS_PAYPAL_CLIENT_' . $paymentAlias . '_1'],
                'secret' => $_SERVER['PARAMS_PAYPAL_SECRET_' . $paymentAlias . '_0'],
                'testSecret' => $_SERVER['PARAMS_PAYPAL_SECRET_' . $paymentAlias . '_1']
            ];
            return $result;
        }
        if($method === 'robokassa'){
            return
            [
                'enable' => true,
                'is_test' => false,
                'shop' => $_SERVER['PARAMS_SHOP_' . $paymentAlias . '_0'],
                0 => $_SERVER['PARAMS_ROBOKASSA_PASSWORD_' . $paymentAlias . '_0'],
                1 => $_SERVER['PARAMS_ROBOKASSA_PASSWORD_' . $paymentAlias . '_1'],
                2 => $_SERVER['PARAMS_ROBOKASSA_PASSWORD_' . $paymentAlias . '_2'],
                3 => $_SERVER['PARAMS_ROBOKASSA_PASSWORD_' . $paymentAlias . '_3']
            ];
        }
        elseif($method === 'paykassa'){
            return
            [
                'enable' => true,
                'is_test' => false,
                'merchant_id' => $_SERVER['PARAMS_PAYKASSA_MERCHANT_' . $paymentAlias . '_0'],
                'merchant_password' => $_SERVER['PARAMS_PAYKASSA_MERCHANT_PASSWORD_' . $paymentAlias . '_0'],
                'api_id' => $_SERVER['PARAMS_PAYKASSA_API_' . $paymentAlias . '_0'],
                'api_password' => $_SERVER['PARAMS_PAYKASSA_API_PASSWORD_' . $paymentAlias . '_0']
            ];
        }
        elseif($method === 'freekassa'){
            return
            [
                'enable' => true,
                'is_test' => false,
                'secret' => [
                    0 => $_SERVER['PARAMS_FREEKASSA_PASSWORD_' . $paymentAlias . '_0'],
                    1 => $_SERVER['PARAMS_FREEKASSA_PASSWORD_' . $paymentAlias . '_1']
                ],
                'api_key' => $_SERVER['PARAMS_FREEKASSA_APIKEY_' . $paymentAlias . '_0'],
                'merchant_id' => $_SERVER['PARAMS_FREEKASSA_MERCHANT_' . $paymentAlias . '_0']
            ];
        }
        elseif($method === 'paypall'){
            return
            [
                'enable' => true,
                'is_test' => false,
                'clientId' => $_SERVER['PARAMS_PAYPAL_CLIENT_' . $paymentAlias . '_0'],
                'testClientId' => $_SERVER['PARAMS_PAYPAL_CLIENT_' . $paymentAlias . '_1'],
                'secret' => $_SERVER['PARAMS_PAYPAL_SECRET_' . $paymentAlias . '_0'],
                'testSecret' => $_SERVER['PARAMS_PAYPAL_SECRET_' . $paymentAlias . '_1']
            ];
        }
        return null;
    }  

    /**
     * {@inheritdoc}
     *
     * @param int|null $configVersion config version
     * @param int|null $paymentAlias payment alias
     * @param string $botToken bot token
     * @param array $robo robokassa
     * @param array $pay paykassa
     * @param array $free freekassa
     * @param array $pp paypall
     * @return array|string
     */
    protected static function generateEncryptedConfig(int|null $configVersion = null, int|null $paymentAlias = null, string|null $botToken = null, array|null $robo = null, array|null $pay = null, array|null $free = null, array|null $pp = null) : array|string{
        $security = new \yii\base\Security;
        $key = $_SERVER['API_KEY_0'];
        $key1 = $_SERVER['API_KEY_1'];
        if($configVersion !== null){
            $return['config_version'] = base64_encode($security->encryptByPassword($configVersion, $key));
        }
        else{
            $return['config_version'] = base64_encode($security->encryptByPassword($_SERVER['CONFIG_VERSION'], $key));
        }
        if($paymentAlias !== null){
            $return['payment_alias'] = base64_encode($security->encryptByPassword($paymentAlias, $key));
        }
        else{
            $return['payment_alias'] = base64_encode($security->encryptByPassword($_SERVER['CURRENT_PAYMENT_ALIAS'], $key));
        }
        if($botToken !== null){
            $return['bot_token'] = base64_encode($security->encryptByPassword($botToken, $key));
        }
        else{
            $return['bot_token'] = base64_encode($security->encryptByPassword($_SERVER['PARAMS_BOTTOKEN_' . $_SERVER['CURRENT_PAYMENT_ALIAS'] . '_0'], $key));
        }
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
            $return['paypall']['client_id'] = base64_encode($security->encryptByPassword($pp['client_id'], $key));
            $return['paypall']['test_client_id'] = base64_encode($security->encryptByPassword($pp['test_client_id'], $key));
            $return['paypall']['secret'] = base64_encode($security->encryptByPassword($pp['secret'], $key));
            $return['paypall']['test_secret'] = base64_encode($security->encryptByPassword($pp['test_secret'], $key));
        }
        else{
            $return['paypall']['enable'] = false;
        }
        $return['paypall'] = base64_encode($security->encryptByPassword(json_encode($return['paypall']), $key1));
        return $return;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $config config
     * @return array
     */
    protected static function decryptConfig(array $config) : array{
        $security = new \yii\base\Security;
        $key = $_SERVER['API_KEY_0'];
        $key1 = $_SERVER['API_KEY_1'];
        $config['config_version'] = $security->decryptByPassword(base64_decode($config['config_version']), $key);
        $config['payment_alias'] = $security->decryptByPassword(base64_decode($config['payment_alias']), $key);
        $config['bot_token'] = $security->decryptByPassword(base64_decode($config['bot_token']), $key);
        $config['robokassa'] = json_decode($security->decryptByPassword(base64_decode($config['robokassa']), $key1), true);
        $config['paykassa'] = json_decode($security->decryptByPassword(base64_decode($config['paykassa']), $key1), true);
        $config['freekassa'] = json_decode($security->decryptByPassword(base64_decode($config['freekassa']), $key1), true);
        $config['paypall'] = json_decode($security->decryptByPassword(base64_decode($config['paypall']), $key1), true);
        return $config;
    }

}