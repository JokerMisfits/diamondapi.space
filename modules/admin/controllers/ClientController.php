<?php

namespace app\modules\admin\controllers;

use app\models\Clients;
use app\modules\admin\models\ClientsSearch;

/**
 * ClientController implements the CRUD actions for Clients model.
 */
class ClientController extends AppAdminController{

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
     * Lists all Clients models.
     *
     * @return string
     */
    public function actionIndex() : string{
        $searchModel = new ClientsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Clients model.
     * @param int $id ID
     * @return string
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id) : string{
        $model = $this->findModel($id);
        unset($model->bot_token);

        $cfg = AppAdminController::getConfig($model->shop, true);
        $model->robokassa = 0;
        $model->paykassa = 0;
        $model->freekassa = 0;
        $model->paypall = 0;

        if(array_key_exists('robokassa', $cfg)){
            $model->robokassa = 1;
        }
        if(array_key_exists('paykassa', $cfg)){
            $model->paykassa = 1;
        }
        if(array_key_exists('freekassa', $cfg)){
            $model->freekassa = 1;
        }
        if(array_key_exists('paypall', $cfg)){
            $model->paypall = 1;
        }

        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * Creates a new Clients model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate() : string|\yii\web\Response{

        \Yii::$app->session->setFlash('warning', 'Добавление клиентов отключено.');
        return $this->redirect('index');

        // $model = new Clients();

        // if($this->request->isPost){
        //     if($model->load($this->request->post()) && $model->save()){
        //         return $this->redirect(['view', 'id' => $model->id]);
        //     }
        // }
        // else{
        //     $model->loadDefaultValues();
        // }

        // return $this->render('create', [
        //     'model' => $model
        // ]);
    }

    /**
     * Updates an existing Clients model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id) : string|\yii\web\Response{

        \Yii::$app->session->setFlash('warning', 'Изменение клиентов отключено.');
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
     * Deletes an existing Clients model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id) : \yii\web\Response{
        \Yii::$app->session->setFlash('warning', 'Удаление клиентов отключено.');
        return $this->redirect(['view', 'id' => $id]);
        //$this->findModel($id)->delete();
        //return $this->redirect(['index']);
    }

    /**
     * Finds the Clients model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Clients the loaded model
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id) : Clients{
        if(($model = Clients::findOne(['id' => $id])) !== null){
            return $model;
        }
        throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
    }
}