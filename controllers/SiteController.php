<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use app\models\Users;
use yii\widgets\ActiveForm;
use yii\web\ForbiddenHttpException;

class SiteController extends AppController{

    /**
     * {@inheritdoc}
     */
    public function actions(){
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => null
            ]
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(){
        //throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
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