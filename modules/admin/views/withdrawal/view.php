<?php
/** @var yii\web\View $this */
/** @var app\models\Withdrawals $model */

$this->title = $model->shop;
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Заявки на вывод ДС', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="withdrawals-view">

    <h1><?= yii\helpers\Html::encode($this->title); ?></h1>

    <p>
        <?= yii\helpers\Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
    </p>

    <?= yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'is_test',
                'visible' => $model->is_test,
                'value' => '<span class="fw-bold text-danger">ТЕСТОВАЯ ЗАЯВКА</span>',
                'format' => 'raw'
            ],
            'id',
            [
                'attribute' => 'client_id',
                'label' => 'Клиент',
                'value' => \yii\helpers\Html::a($model->shop, \yii\helpers\Url::to(['/admin/client/view', 'id' => $model->client_id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']),
                'format' => 'raw'
            ],
            [
                'attribute' => 'tg_member_id',
                'label' => 'Запросил вывод',
                'value' => function(app\models\Withdrawals $model){
                    $member = $model->getTgMember()->one();
                    if($member->tg_username !== null || $member->tg_username !== ''){
                        return \yii\helpers\Html::a($member->tg_username, \yii\helpers\Url::to(['/admin/tg-member/view', 'id' => $member->id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']);
                    }
                    return $member->id;
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'count',
                'value' => $model->count . ' RUB'
            ],
            [
                'attribute' => 'commission',
                'value' => $model->commission . ' RUB'
            ],
            [
                'attribute' => 'card_number',
                'label' => 'Номер банковской карты'
            ],
            [
                'attribute' => 'status',
                'value' => $model->status === 0 ? '<span class="fw-bold text-warning">Ожидает подтверждения с почты</span>' : 
                           ($model->status === 1 ? '<span class="fw-bold text-warning">Ожидает вывода денежных средств</span>' : 
                           ($model->status === 2 ? '<span class="fw-bold text-danger">Заявка отклонена</span>' : 
                           ($model->status === 3 ? '<span class="fw-bold text-success">Заявка выплачена</span>' : 
                           ($model->status === 4 ? '<span class="fw-bold text-danger">Отказ пользователя</span>' : '')))),
                'format' => 'raw'
            ],
            'comment:ntext',
            [
                'attribute' => 'created_time',
                'value' => function(app\models\Withdrawals $model){
                    $dateTime = new DateTime($model->created_time, null);
                    return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                }
            ],
            [
                'attribute' => 'confirmed_time',
                'visible' => isset($model->confirmed_time),
                'value' => function(app\models\Withdrawals $model){
                    $dateTime = new DateTime($model->confirmed_time, null);
                    return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                }
            ],
            [
                'attribute' => 'resulted_time',
                'visible' => isset($model->resulted_time),
                'value' => function(app\models\Withdrawals $model){
                    $dateTime = new DateTime($model->resulted_time, null);
                    return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                }
            ]
        ]
    ]);
    ?>

</div>