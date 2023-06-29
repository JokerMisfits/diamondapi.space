<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use app\models\Users;
use app\models\ContactForm;
use yii\widgets\ActiveForm;

class SiteController extends AppController{
    /**
     * {@inheritdoc}
     */
    public function actions(){
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(){
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin(){
        if(!Yii::$app->user->isGuest){
            return $this->goHome();
        }
        $model = new Users(['scenario' => 'login']);
        if($model->load(Yii::$app->request->post())){
            if($model->validate()){
                if($model->login()){
                    return $this->goBack();
                }
                else{
                    $model->password = '';
                    return $this->render('login', [
                        'model' => $model,
                    ]);
                }
            }
            else{
                $model->password = '';
                return $this->render('login', [
                    'model' => $model,
                ]);
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(){
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact(){
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout(){
        return $this->render('about');
    }    

        /**
     * Displays signup page.
     *
     * @return Response|string
     */
    public function actionSignup(){
        if(!Yii::$app->user->isGuest){
            return $this->goHome();
        }
        $model = new Users(['scenario' => 'signup']);
        if(Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if($model->load(Yii::$app->request->post())){
            if(isset(Yii::$app->request->post('Users')['password_repeat']) && Yii::$app->request->post('Users')['password_repeat'] === $model->password){
                $model->auth_key = Yii::$app->security->generateRandomString(64);
                if($model->validate()){
                    $model->password =  Yii::$app->security->generatePasswordHash($model->password);
                    $authManager = Yii::$app->authManager;
                    if($model->save()){
                        $authManager->assign($authManager->getRole('user'), $model->id);
                        $login = new Users();
                        $login->username = $model->username;
                        $login->rememberMe = true;
                        return $this->render('login', [
                            'model' => $login,
                        ]);
                    }
                    else{
                        if(YII_ENV_DEV){
                            AppController::debug($model->getErrors(), 1);
                        }
                        else{
                            Yii::$app->session->setFlash('error', 'Произошла ошибка при регистрации');
                        }                    
                        $model->password = '';
                        $model->password_repeat = '';
                        return $this->render('signup', [
                            'model' => $model,
                        ]);
                    }
                }
                else{
                    $model = new Users(['scenario' => 'signup']);
                    return $this->render('signup', [
                        'model' => $model,
                    ]);
                }
            }
            else{
                Yii::$app->session->setFlash('error', 'Пароли должны совпадать');
                $model->password = '';
                $model->password_repeat = '';
                $model->auth_key = '';
                return $this->render('signup', [
                    'model' => $model,
                ]);
            }


        }
        
        return $this->render('signup', [
            'model' => $model,
        ]);
    }
}