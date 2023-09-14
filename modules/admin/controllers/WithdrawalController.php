<?php

namespace app\modules\admin\controllers;

use app\models\Withdrawals;
use app\modules\admin\models\WithdrawalsSearch;

/**
 * WithdrawalController implements the CRUD actions for Withdrawals model.
 */
class WithdrawalController extends AppAdminController{

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
     * Lists all Withdrawals models.
     *
     * @return string
     */
    public function actionIndex() : string{
        $searchModel = new WithdrawalsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Withdrawals model.
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
     * Creates a new Withdrawals model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate(int $id = null) : string|\yii\web\Response{
        $model = new Withdrawals();

        if($this->request->isPost){
            if($model->load($this->request->post())){
                if(isset($model->client->id)){
                    $client = $model->getClient()->one();
                    $model->shop = $client->shop;
                    $model->tg_member_id = $client->tg_member_id;
                    $confirmation = 'test';
                    $model->confirmation_link = $confirmation;
                    if($model->validate()){
                        if($model->save()){
                            return $this->redirect(['view', 'id' => $model->id]);
                        }
                    }
                }
            }
        }
        else{
            $model->loadDefaultValues();
            if(isset($id)){
                $model->client_id = $id;
                $model->count = $model->getClient()->one()->min_count_withdrawal;
            }
            else{
                $model->is_test = true;
                $model->client_id = 2;// test
                $model->count = 1000;
                $model->card_number = 321123321123;
            }
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing Withdrawals model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id) : string|\yii\web\Response{
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing Withdrawals model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id) : \yii\web\Response{
        \Yii::$app->session->setFlash('warning', 'Удаление заявок на вывод ДС отключено.');
        return $this->redirect(['view', 'id' => $id]);
        // $this->findModel($id)->delete();
        // return $this->redirect(['index']);
    }

    /**
     * Finds the Withdrawals model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Withdrawals the loaded model
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id) : Withdrawals{
        if (($model = Withdrawals::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new \yii\web\NotFoundHttpException('Страница не найдена.');
    }
}