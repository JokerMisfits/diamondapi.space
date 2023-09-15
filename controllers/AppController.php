<?php

namespace app\controllers;

use app\traits\AppTrait;

class AppController extends \yii\web\Controller{

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
                    'logout' => ['POST']
                ]
            ]
        ];
    }
    
    /**
     * {@inheritdoc}
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException|\yii\web\ForbiddenHttpException
     */
    public function beforeAction($action) : bool{
        \Yii::$app->session->set('csrf', md5(uniqid(rand(), true)));
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $shop shop
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
     *
     * @param array $data data
     * @param string $shop shop
     * @param string $method method
     * @return string|bool
     */
    protected static function curlSendData(array $data, string $shop, string $method = '/sendMessage') : string|bool{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . self::getBotToken($shop) . $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        if($result === false){
            \Yii::error('Method: ' . $method . ' ,shop: ' . $shop . ', Curl ошибка отправки сообщения : ' .  curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    protected static function sendMail(string $to, string $subject, string $message, string $from = 'noreply@diamondapi.space', array $copy = null) : bool{
        $mailSent = mail($to, $subject, $message, self::getHeaders($from));
        return $mailSent;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $from from
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