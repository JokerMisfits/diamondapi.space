<?php

namespace app\modules\admin\controllers;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends AppAdminController{

    public function beforeAction($action){
        return parent::beforeAction($action);
    }

    /**
     * Renders the index view for the module
     * @return string|\yii\web\response
     */
    public function actionIndex() : string|\yii\web\response{
        return $this->redirect('/admin/order');
        //return $this->render('index');
    }

    public function actionTest(){

        // $token = '';
        // AppAdminController::debug(AppAdminController::generateEncryptedConfig('6116638534:AAGTB9VxVo8cmTGf3tztNxTvq8ajrmZS9OU'), 1);//getaxe token

        // $query = new Query();
        // $result = $query->select('tg_user_id')
        // ->from('orders')->all();
        // $count = count($result);
        // for($i = 0;$i < $count;$i++){
        //     $new[$result[$i]['tg_user_id']] = $i;
        // }

        // $new = array_keys($new);
        // $count = count($new);

        // for($i = 0; $i < $count; $i++){
        //     try{
        //         $result = json_decode(file_get_contents('https://api.telegram.org/bot' . '5751056136:AAEE9KsOAq95R5xzAlqBUGaZxB8HS2eGRWA' . '/getChat?chat_id=' . $new[$i]));
        //         if($result->ok){
        //             if(!isset($result->result->username)){
        //                 $result->result->user_name = null;
        //             }
                    // if(!isset($result->result->first_name)){
                    //     $result->result->first_name = null;
                    // }
                    // if(!isset($result->result->last_name)){
                    //     $result->result->last_name = null;
                    // }
                    // if(!isset($result->result->bio)){
                    //     $result->result->bio = null;
                    // }
                    // if(!isset($result->result->type)){
                    //     $result->result->type = null;
                    // }

                    // Yii::$app->db->createCommand()
                    // ->update('tg_members', ['tg_username' => $result->result->username], ['tg_user_id' => $new[$i]])
                    // ->execute();

                    // $result = Yii::$app->db->createCommand()->insert('tg_members', [
                    //     'tg_user_id' => $new[$i],
                    //     'tg_username' => $result->result->username,
                    //     'tg_first_name' => $result->result->first_name,
                    //     'tg_last_name' => $result->result->last_name,
                    //     'tg_bio' => $result->result->bio,
                    //     'tg_type' => $result->result->type
                    // ])->execute();
            //     }
            //     else{
            //         exit($i);
            //     }
            // }
            // catch(\Exception|Throwable $e){
            //     echo $e->getMessage() . PHP_EOL;
            // }
            // sleep(1);
        //}

        //Присвоение роли
        // $authManager = Yii::$app->authManager;
        // $authManager->assign($authManager->getRole('tg-verify'), Yii::$app->user->identity->id);

        //Удаление роли
        // $authManager = Yii::$app->authManager;
        // $authManager->revoke($authManager->getRole('tg-verify'), Yii::$app->user->identity->id);

        // $authManager = Yii::$app->authManager;// Создание новой роли
        // $role = $authManager->createRole('tg-verify');
        // $role->description = 'Учетная запись telegram подтверждена';
        // $authManager->add($role);// Добавление роли в authManager

        $robo = [
            'enable' => true,
            'is_test' => false,
            'shop' => 'club-dimitriev',
            0 => 'Txaty8jx93J1Rmcqr3aj',
            1 => 'QXyMK7biL0H61RqGv1dp',
            2 => 'gTAK3nmko5UF9ZL2T3yq',
            3 => 'GZWdm6thx6Mq818NuVtE'
        ];
        $pay = [
            'enable' => false,
            'is_test' => false,
            'merchant_id' => 21868,
            'merchant_password' => 'HTTEEGFVfy97ynEAMTLpT3sMrsgnFkVr',
            'api_id' => 23578,
            'api_password' => 'ZMhAahpuYny3WYtGCDYCvxNa45R8kCfp'
        ];
        $free = [
            'enable' => true,
            'is_test' => false,
            'secret' => [
                0 => '@o5l9/,pqvwV]n-',
                1 => '.M7Ees2_nPtWc^8',
            ],
            'api_key' => 'ff17fbf05287ab387018031f60e8b26e',
            'merchant_id' => 34422
        ];
        $pp = [
            'enable' => false,
            'is_test' => false,
            'clientId' => 'AdUKh28XXb7FqlbBX2puLlOxB9hFnL4BL3qTVV7i235rxi-3fWBOE4qElAaxYylPGKcXXblfV2B6LwB7',
            'testClientId' => 'AefNr1ARcJHVqGXfmQx6DZV1fjqD6T5EtVIfMN1jNaRRXgO1Zt9rmwtb9mVNpiGptPsiqOEkigY33vEs',
            'secret' => 'EOlktyLebJWYNDd1pOABxCZvYRlQjnkNPLrUXbIqDv0WKAFL_OYrioTM0himgBq5GLFItCXs4Yd41i5M',
            'testSecret' => 'EN0JUOa2DlNpMiJr6eJYflWdbg9_sRxyq3FGyPwmweQpyM1vJVRifuPQkn_Vt-VES5WmyDU6o5F9EbWt'
        ];
        AppAdminController::debug(AppAdminController::generateEncryptedConfig('5751056136:AAEE9KsOAq95R5xzAlqBUGaZxB8HS2eGRWA', $robo, $pay, $free, $pp), 1);

    }
}