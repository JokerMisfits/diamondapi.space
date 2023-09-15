<?php

namespace app\modules\admin\controllers;

use app\traits\AppTrait;

class AppAdminController extends \yii\web\Controller{

    use AppTrait;

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