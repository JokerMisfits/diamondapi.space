<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */

$this->title = 'Платеж №' . $model->id;

$this->params['breadcrumbs'][] = ['label' => 'Админ', 'url' => ['/admin/order']];
$this->params['breadcrumbs'][] = ['label' => 'Оплаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
yii\web\YiiAsset::register($this);

?>
<div class="orders-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php // echo Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?php // echo 
            // Html::a('Удалить', ['delete', 'id' => $model->id], [
            //     'class' => 'btn btn-danger',
            //     'data' => [
            //         'confirm' => 'Are you sure you want to delete this item?',
            //         'method' => 'post',
            //     ],
            // ])
        ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            [
                'attribute' => 'is_test',
                'visible' => $model->is_test == true,
                'value' => '<span class="text-danger fw-bold">ТЕСТОВЫЙ ПЛАТЕЖ</span>',
                'format' => 'raw'
            ],
            'tg_user_id',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->status == 1 ? '<span class="text-success fw-bold">Оплачено</span>' : '<span class="text-danger fw-bold">Не оплачено</span>';
                },
                'format' => 'raw',
            ],
            'count',
            'method',
            'shop',
            'position_name',
            'access_days',
            'created_time',
            'resulted_time',
            //'web_app_query_id',
            'currency',
            'count_in_currency',
            //'commission',
            [
                'attribute' => 'paypal_order_id',
                'visible' => $model->method == 'PayPall'
            ]
            //'client_id',
        ]
    ]) ?>

</div>
