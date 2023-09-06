<?php
use app\models\Clients;
use app\models\TgMembers;

/** @var yii\web\View $this */
/** @var app\models\Orders $model */

$this->title = 'Платеж №' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Платежи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
yii\web\YiiAsset::register($this);

?>
<div class="orders-view table-responsive border rounded">

    <h1><?= yii\helpers\Html::encode($this->title); ?></h1>

    <?= yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'is_test',
                'visible' => $model->is_test,
                'value' => '<span class="text-danger fw-bold">ТЕСТОВЫЙ ПЛАТЕЖ</span>',
                'format' => 'raw'
            ],
            [
                'attribute' => 'client_id',
                'label' => 'Назначение платежа(Клиент)',
                'value' => function($model){
                    return Clients::findOne($model->client_id)->shop;
                }
            ],
            [
                'attribute' => 'tg_member_id',
                'label' => 'Отправитель платежа(Плательщик)',
                'value' => function($model){
                    $name = TgMembers::findOne(['tg_user_id' => $model->tg_user_id])->tg_username;
                    return isset($name) ? $name : $model->tg_user_id;
                }
            ],
            'count',
            'method',
            'position_name',
            'access_days',
            [
                'attribute' => 'count',
                'label' => 'Сумма в рублях',
                'value' => $model->count . ' RUB'
            ],
            'currency',
            [
                'attribute' => 'count_in_currency',
                'value' => $model->count_in_currency . ' ' . $model->currency
            ],
            [
                'attribute' => 'commission',
                'value' => $model->commission . ' ' . $model->currency
            ],
            [
                'attribute' => 'paypal_order_id',
                'visible' => $model->method === 'PayPall'
            ],
            'created_time',
            'resulted_time',
            [
                'attribute' => 'status',
                'label' => 'Статус платежа:',
                'value' => function($model){
                    return $model->status === 1 ? '<span class="text-success fw-bolder">Оплачено</span>' : '<span class="text-danger fw-bolder">Не оплачено</span>';
                },
                'format' => 'raw',
            ]
        ],
        'options' => [
            'class' => 'table table-striped table-bordered detail-view bg-light text-nowrap'
        ]
    ]);
    ?>

</div>