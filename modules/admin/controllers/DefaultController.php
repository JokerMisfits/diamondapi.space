<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\ForbiddenHttpException;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends AppAdminController{

    public function beforeAction($action){
        if($action->id == 'test'){
            if(YII_ENV_DEV){
                return parent::beforeAction($action);
            }
            else{
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        }
        else{
            return parent::beforeAction($action);
        }
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex(){
        return $this->render('index');
    }

    public function actionTest(){

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

        // $robo = [
        //     'enable' => true,
        //     'is_test' => true,
        //     0 => 'Txaty8jx93J1Rmcqr3aj',
        //     1 => 'QXyMK7biL0H61RqGv1dp',
        //     2 => 'gTAK3nmko5UF9ZL2T3yq',
        //     3 => 'GZWdm6thx6Mq818NuVtE'
        // ];
        // $pay = [
        //     'enable' => true,
        //     'is_test' => true,
        //     'merchant_id' => 21868,
        //     'merchant_password' => 'HTTEEGFVfy97ynEAMTLpT3sMrsgnFkVr',
        //     'api_id' => 23578,
        //     'api_password' => 'ZMhAahpuYny3WYtGCDYCvxNa45R8kCfp'
        // ];
        // $free = [
        //     'enable' => true,
        //     'is_test' => true,
        //     'secret' => [
        //         0 => '@o5l9/,pqvwV]n-',
        //         1 => '.M7Ees2_nPtWc^8',
        //     ],
        //     'api_key' => 'ff17fbf05287ab387018031f60e8b26e',
        //     'merchant_id' => 34422
        // ];
        // $pp = [
        //     'enable' => true,
        //     'is_test' => true,
        //     'clientId' => 'AdUKh28XXb7FqlbBX2puLlOxB9hFnL4BL3qTVV7i235rxi-3fWBOE4qElAaxYylPGKcXXblfV2B6LwB7',
        //     'testClientId' => 'AefNr1ARcJHVqGXfmQx6DZV1fjqD6T5EtVIfMN1jNaRRXgO1Zt9rmwtb9mVNpiGptPsiqOEkigY33vEs',
        //     'secret' => 'EOlktyLebJWYNDd1pOABxCZvYRlQjnkNPLrUXbIqDv0WKAFL_OYrioTM0himgBq5GLFItCXs4Yd41i5M',
        //     'testSecret' => 'EN0JUOa2DlNpMiJr6eJYflWdbg9_sRxyq3FGyPwmweQpyM1vJVRifuPQkn_Vt-VES5WmyDU6o5F9EbWt'
        // ];
        // AppAdminController::debug(AppAdminController::generateEncryptedConfig('5751056136:AAEE9KsOAq95R5xzAlqBUGaZxB8HS2eGRWA', $robo, $pay, $free, $pp), 1);

    }
}