<?php

namespace app\components;

use app\traits\AppTrait;
use \Paykassa\PaykassaSCI;
use yii\web\ForbiddenHttpException;

class PaymentComponent extends \yii\base\Component{

    use AppTrait;
    
    public string|null $shop = null;
    public string|null $method = null;
    public bool $is_test = false;
    public string|null $tgUserId = null;
    private array|false $config = false;
    private array $allowedMethods = ['robokassa', 'paykassa', 'freekassa', 'paypall'];
    public array $enabledMethods = [];
    private \app\models\Clients|null $client = null;
    private \yii\db\ActiveQuery|\app\models\ProductsQuery|array|null $products = null;
    public string|bool $error = false;

    /**
     * Init component
     *
     * @return void
     */
    public function init() : void{
        parent::init();
    }

    /**
     * Run component
     *
     * @return void
     * @throws \yii\web\ForbiddenHttpException
     */
    public function run() : void{

        if($this->shop === null){
            throw new ForbiddenHttpException('Доступ запрещен.');
        }
        if($this->method === null){
            $this->config = $this->getConfigTrait($this->shop, true, null, false);
            $this->setEnabledMethods();
            $this->client = \app\models\Clients::find()->where(['shop' => $this->shop])->one();
            $this->products = $this->client->getProducts()->asArray()->all();
            if($this->client->getProducts()->count() === 0){
                $this->error = 'Список товаров пуст.';
            }
        }
        else{
            if(in_array($this->method, $this->allowedMethods)){
                $this->config = $this->getConfigTrait($this->shop, true, $this->method, false);
                $this->debugTrait($this->config, 1);
                //Получить конфиг для метода
                //Сделать запись в БД
                //Создать платежнужю ссылку
                //Редирект
            }
            throw new ForbiddenHttpException('Доступ запрещен.'); 
        }
    }

    /**
     * fill enable methods
     *
     * @return void
     */
    private function setEnabledMethods() : void{
        foreach($this->config as $method => $value){
            if(in_array($method, $this->allowedMethods)){
                if($value['is_test'] === $this->is_test){
                    $this->enabledMethods[$method] = true;
                    if($method === 'paykassa'){
                        $this->config['paykassa']['sci'] = $this->getConfigTrait($this->shop, false, 'paykassa', true)['paykassa'];
                        $this->config['paykassa']['sci'] = new PaykassaSCI($this->config['paykassa']['sci']["merchant_id"], $this->config['paykassa']['sci']["merchant_password"], $this->config['paykassa']['sci']["is_test"]);
                    }
                }                
            }
        }
    }

    /**
     * return safe params
     *
     * @return array
     */
    public function getSafe() : array{
        if($this->error !== false){
            return [
                'error' => $this->error
            ];
        }
        return [
            'client' => $this->client->id,
            'shop' => $this->shop,
            'methods' => $this->enabledMethods,
            'products' => $this->products
        ];
    }

    public function getConfig() : array|false{
        return $this->config;
    }
    
}