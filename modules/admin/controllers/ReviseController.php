<?php

namespace app\modules\admin\controllers;

use app\models\Orders;
use app\models\OrdersComplete;
use app\modules\admin\models\OrdersCompleteSearch;

class ReviseController extends AppAdminController{

    /**
     * @return bool
     * @throws \yii\web\ForbiddenHttpException
     */
    public function beforeAction($action) : bool{
        $referrer = \Yii::$app->request->referrer;
        if($action->id === 'index'){
            return parent::beforeAction($action);
        }
        if($action->id === 'revise'){
            if($referrer && (strpos($referrer, 'order/view') !== false || strpos($referrer, 'order/index') !== false || (strpos($referrer, 'revise/revise') !== false && \Yii::$app->request->isPost) || strpos($referrer, 'revise/view') !== false)){
                return parent::beforeAction($action);
            }
        }
        elseif($action->id === 'view'){
            return parent::beforeAction($action);
        }
        elseif($action->id === 'confirm'){
            if($referrer && strpos($referrer, 'revise/view') !== false){
                return parent::beforeAction($action);
            }
        }
        throw new \yii\web\ForbiddenHttpException('Доступ запрещен.');
    }

    /**
     * Lists all OrdersComplete models.
     *
     * @return string
     */
    public function actionIndex() : string{
        $searchModel = new OrdersCompleteSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single OrderComplete model.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionRevise(int $id) : string|\yii\web\Response{

        $modelCompleteNew = new OrdersComplete();
        if(\Yii::$app->request->isPost){
            if($modelCompleteNew->load($this->request->post())){
                if($modelCompleteNew->validate() && $modelCompleteNew->save()){
                    return $this->redirect(['view', 'id' => $modelCompleteNew->id]);
                }
            }
        }

        if(($model = Orders::findOne(['id' => $id])) !== null){
            if($model->status === 0 || $model->is_test === 1){
                throw new \yii\web\NotFoundHttpException('Страница не найдена.');
            }
            $method = mb_strtolower($model->method);
            $config = AppAdminController::getConfig($model->shop, false, $method);

            if($config === false){
                \Yii::$app->session->setFlash('warning', 'Не удалось извлечь конфигурацию.');
                return \Yii::$app->response->redirect('index');
            }
            elseif($method === 'robokassa'){
                $response = \Yii::$app->get('revise')->invoise($method, ['shop' => $config[$method]['shop'], 'id' => $id, 'pwd' => $config[$method][1]]);
            }
            elseif($method === 'paykassa'){
                $response = null;
            }
            elseif($method === 'freekassa'){
                $response = null;
                //$response = \Yii::$app->get('revise')->invoise($method, ['shopId' => $config[$method]['merchant_id'], 'id' => $id, 'apiKey' => $config[$method]['api_key']]);
            }
            elseif($method === 'paypall'){
                $response = null;
            }
            elseif($method === 'paypalych'){
                $response = null;
            }
            else{
                $response = null;
            }            
            if($response !== null){
                return $this->render('revise', [
                    'model' => $model,
                    'modelCompleteNew' => $modelCompleteNew,
                    'modelCompleteOld' => OrdersComplete::findOne(['order_id' => $id]),
                    'data' => $response
                ]);
            }
            else{
                \Yii::$app->session->setFlash('warning', 'Не удалось получить ответ от платежной системы.');
                return \Yii::$app->response->redirect('index');
            }

        }
        throw new \yii\web\NotFoundHttpException('Страница не найдена.');
    }

    /**
     * Confirm a single OrderComplete model.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionConfirm(int $id, int $orderId) : string|\yii\web\Response{
        if(($model = OrdersComplete::findOne(['id' => $id, 'order_id' => $orderId])) !== null){
            if($model->revise !== null){
                throw new \yii\web\NotFoundHttpException('Страница не найдена.');
            }
            $method = mb_strtolower($model->method);
            $config = AppAdminController::getConfig($model->shop, false, $method);
            $response = \Yii::$app->get('revise')->invoise($method,['shop' => $config[$method]['shop'], 'id' => $orderId, 'pwd' => $config[$method][1]]);
            if($response !== null){
                if($response['State']['Code'] == 100 && isset($response['CommssionPercent']) && isset($response['Commission'])){
                    $response['Revise']['Status'] = true;
                    $response['Revise']['Auditor'] = \Yii::$app->user->identity->id;
                    $model->revise = json_encode($response);
                    if($model->validate() && $model->save()){
                       \Yii::$app->session->setFlash('success', 'Данные успешно внесены.');
                    }
                    return $this->redirect(['view', 'id' => $model->id]);   
                }
                \Yii::$app->session->setFlash('error', 'Необходимо проверить платеж: ' . $response['State']['Code'] . ' ' . $response['State']['CodeDescription']);
                return $this->redirect(['view', 'id' => $model->id]);
            }
            else{
                return \Yii::$app->response->redirect('/admin');
            }
        }
        throw new \yii\web\NotFoundHttpException('Страница не найдена.');
    }

    /**
     * Displays a single OrderComplete model.
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
     * Finds the OrdersComplete model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return OrdersComplete the loaded model
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id) : OrdersComplete{
        if(($model = OrdersComplete::findOne(['id' => $id])) !== null){
            return $model;
        }
        throw new \yii\web\NotFoundHttpException('Страница не найдена.');
    }

}
?>