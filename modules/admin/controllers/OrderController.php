<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Response;
use app\models\Orders;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use app\modules\admin\models\OrdersSearch;

/**
 * OrderController implements the CRUD actions for Orders model.
 */
class OrderController extends AppAdminController{
    /**
     * @inheritDoc
     */
    public function behaviors(){
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Order models.
     *
     * @return string
     */
    public function actionIndex(){
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Order model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id){
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Orders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate(){
        $model = new Orders();
        if(Yii::$app->request->isPost){
            if ($model->load($this->request->post()) && $model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } 
        else{
            $model->loadDefaultValues();
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Orders model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id){
        $model = $this->findModel($id);
        if(Yii::$app->request->isPost && $model->load($this->request->post()) && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Orders model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id){
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id){
        if(($model = Orders::findOne(['id' => $id])) !== null){
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}