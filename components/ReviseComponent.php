<?php

namespace app\components;

class ReviseComponent extends \yii\base\Component{

    private static array $state = [
        'robokassa' => [
            'url' => 'https://auth.robokassa.ru/Merchant/WebService/Service.asmx/OpStateExt',
            'Result' => [
                'Code' => [
                    0 => 'Запрос обработан успешно',
                    1 => 'Неверная цифровая подпись запроса',
                    2 => 'Информация о магазине с таким MerchantLogin не найдена или магазин не активирован',
                    3 => 'Информация об операции с таким InvoiceID не найдена.',
                    4 => 'Найдено две операции с таким InvoiceID. Такая ошибка возникает когда есть тестовая оплата с тем же InvoiceID.',
                    1000 => 'Внутренняя ошибка сервиса RoboKassa'
                ]
            ],
            'State' => [
                'Code' => [
                    5 => 'Операция только инициализирована, деньги от покупателя не получены. Или от пользователя ещё не поступила оплата по выставленному ему счёту или платёжная система, через которую пользователь совершает оплату, ещё не подтвердила факт оплаты.',
                    10 => 'Операция отменена, деньги от покупателя не были получены. Оплата не была произведена. Покупатель отказался от оплаты или не совершил платеж, и операция отменилась по истечении времени ожидания. Либо платёж был совершён после истечения времени ожидания. В случае возникновения спорных моментов по запросу от продавца или покупателя, операция будет перепроверена службой поддержки, и в зависимости от результата может быть переведена в другое состояние.',
                    50 => 'Деньги от покупателя получены, производится зачисление денег на счет магазина. Операция перешла в состояние зачисления средств на баланс продавца. В этом статусе платёж может задержаться на некоторое время. Если платёж «висит» в этом состоянии уже долго (более 20 минут), это значит, что возникла проблема с зачислением средств продавцу.',
                    60 => 'Деньги после получения были возвращены покупателю. Полученные от покупателя средства возвращены на его счёт (кошелёк), с которого совершалась оплата.',
                    80 => 'Исполнение операции приостановлено. Внештатная остановка. Произошла внештатная ситуация в процессе совершения операции (недоступны платежные интерфейсы в системе, из которой/в которую совершался платёж и т.д.) Или операция была приостановлена системой безопасности. Операции, находящиеся в этом состоянии, разбираются нашей службой поддержки в ручном режиме.',
                    100 => 'Платёж проведён успешно, деньги зачислены на баланс продавца, уведомление об успешном платеже отправлено'
                ]
            ],
            'commissions' => [
                'BankCardPSR' => 3.9, //3,9% 3,3% 2,9% 2,7% 2,5%
                'BankCardPSBR' => 3.9, //3,9% 3,3% 2,9% 2,7% 2,5%
                'SBPPSR' => 2, //1,8% 1,8% 1,8% 1,65% 1,5%
                'SBPPSBR' => 2, //1,8% 1,8% 1,8% 1,65% 1,5%
                'YandexPayPSR' => 3.9, //3,9% 3,3% 2,9% 2,7% 2,5%
                'YandexPayPSBR' => 3.9, //3,9% 3,3% 2,9% 2,7% 2,5%
                'Podeli30R' => 5.5, //5,5%
                'MtsPayPSR' => 3.9, //3,9% 3,3% 2,9% 2,7% 2,5%
                'MirPayPSR' => 3.9, //3,9% 3,3% 2,9% 2,7% 2,5%
                'BankCardForeignPSR' => 9.9, //9,9%
                'BankCardForeignPSBR' => 9.9, //9,9%
                'Qiwi40PS' => 7 //6-7% 5,5-6,5% 4,5-5,5%
            ]
        ],
        'freekassa' => [
            'url' => 'https://api.freekassa.ru/v1/orders',
            'orders' => [
                'status' => [
                    0 => 'Новый',
                    1 => 'Оплачен',
                    8 => 'Ошибка',
                    9 => 'Отмена'
                ]
            ]
        ]
    ];

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
     */
    public function run() : void{
        // Обязательный метод, в котором выполняется основная логика компонента
        // В этом методе должна быть основная логика компонента
    }

    /**
     * Gets invoise status
     *
     * @param string $method shop name
     * @param array $data
     * @param bool $mode
     * @return array|null
     */
    public function invoise(string $method, array $data, bool $mode = true) : array|null{
        if($method === 'robokassa'){
            return self::invoiceRobo($data, $mode);
        }
        elseif($method === 'paykassa'){
            return null;
        }
        elseif($method === 'freekassa'){
            return self::invoiceFree($data, $mode);
        }
        elseif($method === 'paypall'){
            return null;
        }
        elseif($method === 'paypalych'){
            return null;
        }
        return null;
    }

    /**
     * Gets invoise status robokassa
     *
     * @param array $data
     * @param bool $mode
     * @return array|null
     */
    private static function invoiceRobo(array $data, bool $mode) : array|null{
        $crc = self::getSignature('robokassa', $data);
        $http = new \yii\httpclient\Client();
        $params = [
            'MerchantLogin' => $data['shop'],
            'InvoiceID' => $data['id'],
            'Signature' => $crc
        ];
        $response = $http->createRequest()
        ->setMethod('POST')
        ->setUrl(self::$state['robokassa']['url'])
        ->setData($params)
        ->send();
        if($response->isOk){
            $data = $response->data;
            if(isset($data['Result']['Code']) && $data['Result']['Code'] == 0){
                $data['State']['CodeDescription'] = self::$state['robokassa']['State']['Code'][$data['State']['Code']];
                if(isset($data['Info']['IncCurrLabel'])){
                    if(isset(self::$state['robokassa']['commissions'][$data['Info']['IncCurrLabel']])){
                        $data['CommssionPercent'] = self::$state['robokassa']['commissions'][$data['Info']['IncCurrLabel']];
                        if(isset($data['Info']['IncSum'])){
                            $data['Commission'] = round(($data['CommssionPercent'] / 100) * $data['Info']['IncSum'], 2);
                        }
                    }
                }
                return $data;
            }
            else{
                if($mode){
                    \Yii::$app->session->setFlash('error', self::$state['robokassa']['Result']['Code'][$data['Result']['Code']]);
                }
                return null;
            }
        }
        else{
            if($mode){
                \Yii::$app->session->setFlash('error', 'Произошла ошибка при выполнении запроса: ' . $response->statusText);
            }
            return null;
        }
    }

    /**
     * Gets invoise status paykassa
     *
     * @param array $data
     * @param bool $mode
     * @return array|null
     */
    private static function invoicePay(array $data, bool $mode) : array|null{
        return null;
    }

    /**
     * Gets invoise status fleekassa
     *
     * @param array $data
     * @param bool $mode
     * @return array|null
     */
    private static function invoiceFree(array $data, bool $mode) : array|null{
        $crc = self::getSignature('freekassa', $data);
        $nonce = $crc[1];
        $crc = $crc[0];

        $http = new \yii\httpclient\Client();
        $params = [
            'shopId' => (int)$data['shopId'],
            'nonce' => $nonce,
            'paymentId' => $data['id']
        ];
        ksort($params);
        $params['signature'] = $crc;

        $response = $http->createRequest()
        ->setMethod('GET')
        ->setUrl(self::$state['freekassa']['url'])
        ->setOptions([
            'timeout' => 10,
        ])
        ->setHeaders([
            'Content-Type' => 'application/json',
        ])
        ->setData(json_encode($params))
        ->send();

        if($response->isOk){
            return $response->data;
        }

        // $statusCode = $response->statusCode;
        // $content = $response->content;
        // $headers = $response->headers;
    
        // // Вывести информацию об ошибке
        // echo "HTTP Status Code: $statusCode<br>";
        // echo "Response Content: $content<br>";
        
        // // Вывести заголовки ответа
        // echo "Response Headers:<pre>";
        // print_r($headers);
        // echo "</pre>";

        return null;
    }

    /**
     * Gets invoise status paypall
     *
     * @param array $data
     * @param bool $mode
     * @return array|null
     */
    private static function invoicePP(array $data, bool $mode) : array|null{
        return null;
    }

    /**
     * Gets invoise status paypalych
     *
     * @param array $data
     * @param bool $mode
     * @return array|null
     */
    private static function invoicePalych(array $data, bool $mode) : array|null{
        return null;
    }

    /**
     * Return signature for payment system
     *
     * @param string $method
     * @param array $data
     * @return string|null
     */
    private static function getSignature(string $method, array $data) : string|null|array{
        if($method === 'robokassa'){
            return md5($data['shop'] . ':' . $data['id'] . ':' . $data['pwd']);
        }
        elseif($method === 'paykassa'){

        }
        elseif($method === 'freekassa'){
            $nonce = time();
            $toHash = [
                'shopId' => (int)$data['shopId'],
                'nonce' => $nonce,
                'paymentId' => $data['id']
            ];
            ksort($toHash);
            return [hash_hmac('sha256', implode('|', $toHash), $data['apiKey']), $nonce];
        }
        elseif($method === 'paypall'){

        }
        elseif($method === 'paypalych'){

        }
        return null;
    }

}