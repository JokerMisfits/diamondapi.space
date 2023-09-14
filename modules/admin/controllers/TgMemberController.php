<?php

namespace app\modules\admin\controllers;

use app\models\TgMembers;
use app\modules\admin\models\TgMembersSearch;

/**
 * TgMemberController implements the CRUD actions for TgMembers model.
 */
class TgMemberController extends AppAdminController{

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
     * Lists all TgMembers models.
     *
     * @return string
     */
    public function actionIndex() : string{
        $searchModel = new TgMembersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single TgMembers model.
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
     * Creates a new TgMembers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate() : string|\yii\web\Response{
        $model = new TgMembers();

        if($this->request->isPost){
            if($model->load($this->request->post()) && $model->save()){
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
     * Updates an existing TgMembers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id) : string|\yii\web\Response{
        $model = $this->findModel($id);
        if($this->request->isPost && $model->load($this->request->post()) && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing TgMembers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id) : \yii\web\Response{
        \Yii::$app->session->setFlash('warning', 'Удаление пользователей отключено.');
        return $this->redirect(['view', 'id' => $id]);
        // $this->findModel($id)->delete();
        // return $this->redirect(['index']);
    }

    /**
     * Fill the TgMembers model.
     *
     * @param int $id ID
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException|\yii\web\ForbiddenHttpException if the model cannot be found
     */
    public function actionFill(int $id = null) : string|\yii\web\Response{
        if($id === null){
            $count = TgMembers::find()->where(['is_filled' => 0])->count();
            if($count > 0){
                $models = TgMembers::find()->where(['is_filled' => 0])->all();
                if(count($models) !== $count){
                    $count = count($models);
                }
                for($i = 0; $i < $count; $i++){
                    try{
                        $this->fillModel($models[$i]);
                    }
                    catch(\Exception $e){
                        \Yii::$app->session->addFlash('error', 'Не удалось обновить запись №' . $models[$i]->id . ' ' . mb_substr(strstr($e->getMessage(), '):'), 2));
                        $models[$i]->is_filled = 1;
                        $models[$i]->last_change = date("Y-m-d H:i:s");
                        $models[$i]->update();
                    }
                    sleep(1);
                }
            }
            else{
                \Yii::$app->session->setFlash('warning', 'Все записи уже заполнены.');
            }
            return $this->redirect('index');
        }
        else{
            $model = TgMembers::findOne(['tg_user_id' => $id]);
            if($model === null){
                throw new \yii\web\NotFoundHttpException('Страница не найдена.');
            }
            elseif(strtotime($model->last_change) + 86400 > time()){
                throw new \yii\web\ForbiddenHttpException('Доступ запрещен.');
            }
            else{
                try{
                    $this->fillModel($model);
                }
                catch(\Exception $e){
                    \Yii::$app->session->setFlash('error', 'Не удалось обновить запись №' . $model->id . ' ' . mb_substr(strstr($e->getMessage(), '):'), 2));
                    $model->is_filled = 1;
                    $model->last_change = date("Y-m-d H:i:s");
                    $model->update();
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }


    /**
     * Fill the TgMembers model.
     *
     * @param TgMembers $model TgMembers model
     * @throws \Exception if request failed
     */
    private function fillModel(TgMembers $model) : void{
        $result = json_decode(file_get_contents('https://api.telegram.org/bot' . $_SERVER['PARAMS_BOTTOKEN_1_0'] . '/getChat?chat_id=' . $model->tg_user_id));

        if($result->ok){
            if(isset($result->result->username)){
                if($model->tg_username !== $result->result->username && $result->result->username != null){
                    $model->tg_username = $result->result->username;
                }
            }
            if(isset($result->result->first_name)){
                if($model->tg_first_name !== $result->result->first_name && $result->result->first_name != null){
                    $model->tg_first_name = $result->result->first_name;
                }
            }
            if(isset($result->result->last_name)){
                if($model->tg_last_name !== $result->result->last_name && $result->result->last_name != null){
                    $model->tg_last_name = $result->result->last_name;
                }
            }
            if(isset($result->result->bio)){
                if($model->tg_bio !== $result->result->bio && $result->result->bio != null){
                    $model->tg_bio = $result->result->bio;
                }
            }
            if(isset($result->result->type)){
                if($model->tg_type !== $result->result->type && $result->result->type != null){
                    $model->tg_type = $result->result->type;
                }
            }
            $model->is_filled = 1;
            $model->last_change = date("Y-m-d H:i:s");
            $model->update();
        }
    }

    /**
     * Finds the TgMembers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TgMembers the loaded model
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id) : TgMembers{
        if(($model = TgMembers::findOne(['id' => $id])) !== null){
            return $model;
        }
        throw new \yii\web\NotFoundHttpException('Страница не найдена.');
    }
}