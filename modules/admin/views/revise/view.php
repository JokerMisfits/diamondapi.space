<?php
/** @var yii\web\View $this */
/** @var app\models\OrdersComplete $model */

$this->title = 'Платеж №' . $model->order_id    ;
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Сверка', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
yii\web\YiiAsset::register($this);

?>
<div class="orders-view table-responsive border rounded">

    <h1><?= yii\helpers\Html::encode($this->title); ?></h1>


    <?= yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'client_id',
                'label' => 'Назначение платежа(Клиент)',
                'value' => \yii\helpers\Html::a($model->shop, \yii\helpers\Url::to(['/admin/client/view', 'id' => $model->client_id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']),
                'format' => 'raw'
            ],
            [
                'attribute' => 'order_id',
                'label' => 'Платеж',
                'value' => \yii\helpers\Html::a($model->order_id, \yii\helpers\Url::to(['/admin/order/view', 'id' => $model->order_id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']),
                'format' => 'raw'
            ],
            'method',
            'payment_method',
            [
                'attribute' => 'fee',
                'value' => $model->fee . ' RUB'
            ],
            [
                'attribute' => 'revise',
                'value' => function(app\models\OrdersComplete $model){
                    if($model->revise === null){
                        return '<span class="fw-bold fs-5 text-danger">Данные не подтверждены</span> ' . \yii\helpers\Html::a('Запросить', \yii\helpers\Url::to(['/admin/revise/confirm', 'id' => $model->id, 'orderId' => $model->order_id]), ['class' => 'btn btn-sm btn-warning fw-bold', 'title' => 'Перейти', 'target' => '_self']);
                    }
                    $revise = json_decode($model->revise, true);
                    $result = 'Дата платежа: ' . Yii::$app->formatter->asDatetime(new DateTime($revise['State']['StateDate'], null), 'php:d.m.Y H:i:s') . '<br>';
                    $result .= 'Статус платежа: ' . $revise['State']['CodeDescription'] . '<br>';
                    $result .= 'Способ оплаты: ' . $revise['Info']['IncCurrLabel'] . '<br>';
                    $result .= 'Сумма платежа: ' . round($revise['Info']['OutSum'], 2) . ' RUB<br>';
                    $result .= 'Комиссия платежной системы: ' . $revise['CommssionPercent'] . '%<br>';
                    $result .= 'Комиссия платежной системы: ' . $revise['Commission'] . ' RUB<br>';
                    $result .= 'Аудитор: ' . app\models\Users::findOne(['id' => $revise['Revise']['Auditor']])->username . '<br>';
                    return $result;
                },
                'format' => 'raw'
            ]
        ],
        'options' => [
            'class' => 'table table-striped table-bordered detail-view bg-light text-nowrap'
        ]
    ]);
    ?>
    <?= $model->revise !== null ? \yii\helpers\Html::a('Перейти в режим сверки', \yii\helpers\Url::to(['/admin/revise/revise', 'id' => $model->order_id]), ['class' => 'mb-3 btn btn-warning fw-bold', 'title' => 'Перейти', 'target' => '_self']) : ''; ?>

</div>