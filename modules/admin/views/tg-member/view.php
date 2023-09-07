<?php
/** @var yii\web\View $this */
/** @var app\models\TgMembers $model */

$this->title = $model->tg_user_id;
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tg-members-view">

    <h1><?= yii\helpers\Html::encode($this->title); ?></h1>

    <p>
        <?= yii\helpers\Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?= yii\helpers\Html::a('Заполнить(ДОДЕЛАТЬ)', ['update', 'id' => $model->id], ['class' => 'btn btn-success']); ?>
    </p>

    <?= yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'Клиент?',
                'value' => function(app\models\TgMembers $model){
                    $count = $model->getClients()->count();
                    return $count > 0 ? \yii\helpers\Html::a('Да(' . $count . ')', \yii\helpers\Url::to(['/admin/client', 'ClientsSearch' => ['tg_member_id' => $model->id]]), ['class' => 'fw-bold link-success', 'title' => 'Перейти', 'target' => '_self']) : '<span class="fw-bold text-danger">Нет</span>';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'Платежи',
                'value' => function(app\models\TgMembers $model){
                    $count  = $model->getOrders()->where(['status' => 1, 'is_test' => 0])->count();
                    return $count > 0 ? \yii\helpers\Html::a($count, \yii\helpers\Url::to(['/admin/order', 'OrdersSearch' => ['tg_member_id' => $model->id, 'status' => 1, 'is_test' => 0]]), ['class' => 'fw-bold link-success', 'title' => 'Перейти', 'target' => '_self']) : '<span class="fw-bold text-danger">Нет</span>';
                },
                'format' => 'raw'
            ],
            'tg_user_id',
            'tg_username',
            'tg_first_name',
            'tg_last_name',
            'tg_bio',
            'tg_type',
            [
                'attribute' => 'is_filed',
                'value' => $model->is_filed ? 'Да' : 'Нет' 
            ],
            [
                'attribute' => 'last_change',
                'value' => function($model){
                    $dateTime = new DateTime($model->last_change, null);
                    return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                }
            ]
        ]
    ]);
    ?>

</div>