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
        <?= strtotime($model->last_change) + 86400 < time() ? yii\helpers\Html::a('Заполнить', ['fill', 'id' => $model->tg_user_id], ['class' => 'btn btn-success']) : ''; ?>
    </p>

    <?= yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'account',
                'label' => 'Учетная запись',
                'value' => function(app\models\TgMembers $model){
                    $user = $model->getUsers()->one();
                    if($user === null){
                        return null;
                    }
                    return yii\helpers\Html::a($user->username, \yii\helpers\Url::to(['/admin/user/view', 'id' => $user->id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']);
                },
                'format' => 'raw'
            ],
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
                'attribute' => 'is_filled',
                'value' => $model->is_filled ? 'Да' : 'Нет' 
            ],
            [
                'attribute' => 'last_change',
                'label' => 'Дата последнего обновления',
                'value' => function($model){
                    $dateTime = new DateTime($model->last_change, null);
                    return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                }
            ]
        ]
    ]);
    ?>

</div>