<?php
namespace app\controllers;

use app\models\Users;

class SiteController extends AppController{

    /**
     * {@inheritdoc}
     *
     * @return bool
     * @throws \yii\web\BadRequestHttpException|\yii\web\ForbiddenHttpException
     */
    public function beforeAction($action) : bool{
        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function actions() : array{
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ]
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     *
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionIndex() : string{
        if(\Yii::$app->user->can('admin')){//ЗАГЛУШКА
            return $this->render('index');
        }
        throw new \yii\web\ForbiddenHttpException('Доступ запрещен.', 403);
    }

    /**
     * Login action.
     *
     * @return string|\yii\web\Response
     */
    public function actionLogin() : string|\yii\web\Response{
        if(!\Yii::$app->user->isGuest){
            return $this->goHome();
        }
        $model = new Users(['scenario' => 'login']);
        if($model->load(\Yii::$app->request->post())){
            if($model->validate()){
                if($model->login()){
                    return $this->goBack();
                }
                else{
                    $model->password = '';
                    return $this->render('login', [
                        'model' => $model
                    ]);
                }
            }
            else{
                $model->password = '';
                return $this->render('login', [
                    'model' => $model
                ]);
            }
        }

        return $this->render('login', [
            'model' => $model
        ]);
    }

    /**
     * Logout action.
     *
     * @return \yii\web\Response
     */
    public function actionLogout() : \yii\web\Response{
        \Yii::$app->user->logout();
        return $this->goHome();
    }  

    /**
     * Displays signup page.
     *
     * @return string|\yii\web\Response
     */
    public function actionSignup() : string|\yii\web\Response{
        if(!\Yii::$app->user->can('admin')){//ЗАГЛУШКА
            return $this->render('index');
        }
        if(!\Yii::$app->user->isGuest){
            return $this->goHome();
        }
        $model = new Users(['scenario' => 'signup']);
        if($model->load(\Yii::$app->request->post())){
            if(isset(\Yii::$app->request->post('Users')['password_repeat']) && \Yii::$app->request->post('Users')['password_repeat'] === $model->password){
                $model->auth_key = \Yii::$app->security->generateRandomString(64);
                if($model->validate()){
                    $model->password =  \Yii::$app->security->generatePasswordHash($model->password);
                    if($model->save()){
                        \Yii::$app->authManager->assign(\Yii::$app->authManager->getRole('user'), $model->id);
                        $login = new Users();
                        $login->username = $model->username;
                        $login->rememberMe = true;
                        return $this->render('login', [
                            'model' => $login
                        ]);
                    }
                    else{
                        \Yii::$app->session->addFlash('error', 'Произошла ошибка при регистрации');                 
                        $model->password = '';
                        $model->password_repeat = '';
                        return $this->render('signup', [
                            'model' => $model
                        ]);
                    }
                }
                else{
                    $model = new Users(['scenario' => 'signup']);
                    return $this->render('signup', [
                        'model' => $model
                    ]);
                }
            }
            else{
                \Yii::$app->session->addFlash('error', 'Пароли должны совпадать');
                $model->password = '';
                $model->password_repeat = '';
                $model->auth_key = '';
                return $this->render('signup', [
                    'model' => $model
                ]);
            }
        }
        
        return $this->render('signup', [
            'model' => $model
        ]);
    }
}