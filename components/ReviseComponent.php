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
                'SBPPSBR' => 2, //1,8% 1,8% 1,8% 1,65% 1,5%
                'YandexPayPSR' => 3.9, //3,9% 3,3% 2,9% 2,7% 2,5%
                'MtsPayPSR' => 3.9, //3,9% 3,3% 2,9% 2,7% 2,5%
                'MirPayPSR' => 3.9, //3,9% 3,3% 2,9% 2,7% 2,5%
                'BankCardForeignPSR' => 9.9, //9,9%
                'BankCardPSBR' => 3.9, //3,9% 3,3% 2,9% 2,7% 2,5%
                'Qiwi40PS' => 7 //6-7% 5,5-6,5% 4,5-5,5%
            ]
        ]
    ];

    public function init(){
        parent::init();
    }

    // Обязательный метод, в котором выполняется основная логика компонента
    public function run(){
        // В этом методе должна быть основная логика компонента
    }

    /**
     * Gets invoise status
     * @param string $shop shop name
     * @param int $id ID
     * @param string $pwd shop pwd
     * @return array|null
     */
    public function invoise(string $shop, int $id, string $pwd) : array|null{
        $hash = self::getHash($shop, $id, $pwd);
        $http = new \yii\httpclient\Client();
        $params = [
            'MerchantLogin' => $shop,
            'InvoiceID' => $id,
            'Signature' => $hash
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
                \Yii::$app->session->setFlash('error', self::$state['robokassa']['Result']['Code'][$data['Result']['Code']]);
                return null;
            }
        }
        else{
            \Yii::$app->session->setFlash('error', 'Произошла ошибка при выполнении запроса: ' . $response->statusText);
            return null;
        }
    }

    private static function getHash(string $shop, int $id, string $pwd) : string{
        return md5($shop . ':' . $id . ':' .$pwd);
    }

}