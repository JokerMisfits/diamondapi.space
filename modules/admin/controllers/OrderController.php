<?php

namespace app\modules\admin\controllers;

use app\models\Orders;
use app\models\OrdersComplete;
use app\modules\admin\models\OrdersSearch;

/**
 * OrderController implements the CRUD actions for Orders model.
 */
class OrderController extends AppAdminController{

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
     * Lists all Order models.
     * @param bool $revise
     *
     * @return string
     */
    public function actionIndex(bool $revise = false) : string{

        $searchModel = new OrdersSearch();
        if($revise){
            $subquery = OrdersComplete::find()->select('order_id');
            $query = Orders::find()->where(['not in', 'id', $subquery])->andWhere(['status' => 1, 'is_test' => 0]);
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => $query
            ]);
        }
        else{
            $dataProvider = $searchModel->search(\Yii::$app->request->get());
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Order model.
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
     * Creates a new Orders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate() : string|\yii\web\Response{
        $model = new Orders();
        $model->is_test = 1;
        $model->client_id = 2; //test shop
        $model->shop = 'test';
        $model->count = 1000;
        $model->access_days = 1;
        $model->method = 'PayKassa';
        $model->position_name = 'test';
        $model->currency = 'RUB';
        $model->tg_user_id = \app\models\TgMembers::findOne(['id' => \Yii::$app->user->identity->tg_member_id])->tg_user_id;
        if(\Yii::$app->request->isPost){
            if ($model->load($this->request->post()) && $model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } 
        else{
            $model->loadDefaultValues();
        }
        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing Orders model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id) : string|\yii\web\Response{
        $model = $this->findModel($id);
        if(!$model->is_test){
            \Yii::$app->session->setFlash('warning', 'Изменение платежей отключено.');
            return $this->redirect(['view', 'id' => $id]);
        }
        if(\Yii::$app->request->isPost && $model->load($this->request->post()) && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing Orders model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id) : \yii\web\Response{
        \Yii::$app->session->setFlash('warning', 'Удаление платежей отключено.');
        return $this->redirect(['view', 'id' => $id]);
        // $this->findModel($id)->delete();
        // return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Orders model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionRevise(int $id) : \yii\web\Response{
        return $this->redirect(['/admin/revise/revise', 'id' => $id]);
    }

    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Orders the loaded model
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id) : Orders{
        if(($model = Orders::findOne(['id' => $id])) !== null){
            return $model;
        }
        throw new \yii\web\NotFoundHttpException('Страница не найдена.');
    }
}