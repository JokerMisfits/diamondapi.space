<?php

namespace app\traits;

trait AppTrait{

    /**
     * {@inheritdoc}
     *
     * @param mixed $data data
     * @param bool $mode mode
     * @return void
     */
    protected function debugTrait(mixed $data, bool $mode = false) : void{
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
    protected function getConfigTrait(string $shop, bool $checkEnable = false, string $method = null, bool $skipEnable = false) : array|false{
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

}

?>