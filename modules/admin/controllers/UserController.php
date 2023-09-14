<?php

namespace app\modules\admin\controllers;

use app\models\Users;
use app\modules\admin\models\UsersSearch;

/**
 * DefaultController implements the CRUD actions for Users model.
 */
class UserController extends AppAdminController{

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function behaviors() : array{
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => \yii\filters\VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST']
                    ]
                ]
            ]
        );
    }

    /**
     * Lists all Users models.
     *
     * @return string
     */
    public function actionIndex() : string{
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->sort->attributes['tg_member_id'] = null;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Users model.
     * @param int $id ID
     * @return string
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id) : string{
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate() : string|\yii\web\Response{
        return $this->redirect('/signup');
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id) : string|\yii\web\Response{

        \Yii::$app->session->setFlash('warning', 'Изменение аккаунтов отключено.');
        return $this->redirect(['view', 'id' => $id]);

        // $model = $this->findModel($id);

        // if($this->request->isPost && $model->load($this->request->post()) && $model->save()){
        //     return $this->redirect(['view', 'id' => $model->id]);
        // }

        // return $this->render('update', [
        //     'model' => $model
        // ]);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id) : \yii\web\Response{
        \Yii::$app->session->setFlash('warning', 'Удаление аккаунтов отключено.');
        return $this->redirect(['view', 'id' => $id]);
        //$this->findModel($id)->delete();
        //return $this->redirect(['index']);
    }

    /**
     * Block an existing Users model.
     * If block is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException|\yii\web\ForbiddenHttpException if the model cannot be found or forbidden
     */
    public function actionBlock(int $id) : \yii\web\response{
        if(\Yii::$app->user->can('userManage')){
            if($id == \Yii::$app->user->identity->id){
                \Yii::$app->session->setFlash('warning', 'Запрещено блокировать свою учетную запись.');
            }
            elseif(\Yii::$app->authManager->checkAccess($id, 'user')){
                \Yii::$app->authManager->revoke(\Yii::$app->authManager->getRole('user'), $id);
                \Yii::$app->session->setFlash('success', 'Сотрудник успешно заблокирован.');
            }
            else{
                \Yii::$app->authManager->assign(\Yii::$app->authManager->getRole('user'), $id);
                \Yii::$app->session->setFlash('success', 'Сотрудник успешно разблокирован.');
            }
        }
        else{
            throw new \yii\web\ForbiddenHttpException('Доступ запрещен.');
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Reset password an existing Users model.
     * If reset is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException|\yii\web\ForbiddenHttpException if the model cannot be found or forbidden
     */
    public function actionReset(int $id) : \yii\web\response{
        if(\Yii::$app->user->can('userManage')){
            if($id == \Yii::$app->user->identity->id){
                \Yii::$app->session->setFlash('warning', 'Запрещено сбрасывать пароль у своейй учетной записи.');
            }
            else{
                $model = $this->findModel($id);
                $password = \Yii::$app->security->generateRandomString(12);
                $model->updateAttributes(['password' => \Yii::$app->security->generatePasswordHash($password)]);
                \Yii::$app->session->setFlash('success', 'Пароль успешно сброшен | Новый пароль: ' . $password);
            }
        }
        else{
            throw new \yii\web\ForbiddenHttpException('Доступ запрещен.');
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Users the loaded model
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id) : Users{
        if(($model = Users::findOne(['id' => $id])) !== null){
            return $model;
        }
        throw new \yii\web\NotFoundHttpException('Страница не найдена.');
    }
}