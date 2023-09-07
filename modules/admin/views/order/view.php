<?php
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
                'value' => '<span class="fw-bold text-danger">ТЕСТОВЫЙ ПЛАТЕЖ</span>',
                'format' => 'raw'
            ],
            'id',
            [
                'attribute' => 'client_id',
                'label' => 'Назначение платежа(Клиент)',
                'value' => \yii\helpers\Html::a($model->shop, \yii\helpers\Url::to(['/admin/client/view', 'id' => $model->client_id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']),
                'format' => 'raw'
            ],
            [
                'attribute' => 'tg_member_id',
                'label' => 'Отправитель платежа(Плательщик)',
                'value' => function(app\models\Orders $model){
                    $member = $model->getTgMember()->one();
                    if($member->tg_username !== null && $member->tg_username !== ''){
                        return \yii\helpers\Html::a($member->tg_username, \yii\helpers\Url::to(['/admin/tg-member/view', 'id' => $member->id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']);
                    }
                    return \yii\helpers\Html::a($member->tg_user_id, \yii\helpers\Url::to(['/admin/tg-member/view', 'id' => $member->id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'count',
                'value' => $model->count . '<span class="fw-bold text-danger">(ПРОВЕРИТЬ КАК ВЕДЕТ СЕБЯ СИСТЕМА С ДРУГОЙ ВАЛЮТОЙ, ПРАВИЛЬНО ЛИ НАЧИСЛЯЕТСЯ БАЛАНС???)</span>',
                'format' => 'raw'
            ],
            'method',
            'position_name',
            'access_days',
            [
                'attribute' => 'count',
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
                'value' => function(app\models\Orders $model){
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