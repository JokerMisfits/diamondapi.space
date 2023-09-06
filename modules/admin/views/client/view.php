<?php
use app\models\TgMembers;

/** @var yii\web\View $this */
/** @var app\models\Clients $model */
/** @var int $ordersCount */
/** @var int $commissionsCount */

$this->title = $model->shop;
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="clients-view table-responsive border rounded">

    <h1><?= yii\helpers\Html::encode($this->title); ?></h1>

    <p>
        <?= yii\helpers\Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
    </p>

    <?= yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'generalBlock',
                'label' => 'Общая информация',
                'value' => '',
                'captionOptions' => [
                    'class' => 'pt-4 bg-dark text-light border-0'
                ],
                'contentOptions' => [
                    'class' => 'pt-4 bg-dark border-0'
                ]
            ],
            'id',
            [
                'attribute' => 'tg_member_id',
                'label' => 'Владелец',
                'value' => function($model){
                    $name = TgMembers::findOne(['tg_user_id' => $model->tg_user_id])->tg_username;
                    return isset($name) ? $name : $model->tg_user_id;
                }
            ],
            'shop',
            [
                'attribute' => 'tg_chat_id',
                'label' => 'ID чата в telegram'
            ],
            [
                'attribute' => 'tg_private_chat_id',
                'label' => 'ID приватного чата в telegram'
            ],
            [
                'attribute' => 'min_count_withdrawal',
                'value' => $model->min_count_withdrawal . ' RUB'
            ],
            [
                'attribute' => 'last_change',
                'value' => function($model){
                    $dateTime = new DateTime($model->last_change, null);
                    return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                }
            ],
            [
                'attribute' => 'paymentBlock',
                'label' => 'Платежные системы',
                'value' => '',
                'captionOptions' => [
                    'class' => 'pt-4 bg-dark text-light border-0'
                ],
                'contentOptions' => [
                    'class' => 'pt-4 bg-dark border-0'
                ]
            ],
            [
                'attribute' => 'robokassa',
                'label' => 'RoboKassa',
                'value' => $model->robokassa ? '<span class="text-success">Подключена</span>' : '<span class="text-primary">Подключить(ДОДЕЛАТЬ)</span>',
                'format' => 'raw'
            ],
            [
                'attribute' => 'paykassa',
                'label' => 'PayKassa',
                'value' => $model->paykassa ? '<span class="text-success">Подключена</span>' : '<span class="text-primary">Подключить(ДОДЕЛАТЬ)</span>',
                'format' => 'raw'
            ],
            [
                'attribute' => 'freekassa',
                'label' => 'FreeKassa',
                'value' => $model->freekassa ? '<span class="text-success">Подключена</span>' : '<span class="text-primary">Подключить(ДОДЕЛАТЬ)</span>',
                'format' => 'raw'
            ],
            [
                'attribute' => 'paypall',
                'label' => 'PayPall',
                'value' => $model->paypall ? '<span class="text-success">Подключена</span>' : '<span class="text-primary">Подключить(ДОДЕЛАТЬ)</span>',
                'format' => 'raw'
            ],
            [
                'attribute' => 'financeBlock',
                'label' => 'Финансы',
                'value' => '',
                'captionOptions' => [
                    'class' => 'pt-4 bg-dark text-light border-0'
                ],
                'contentOptions' => [
                    'class' => 'pt-4 bg-dark border-0'
                ]
            ],
            [
                'attribute' => 'cost',
                'value' => $model->cost . ' RUB'
            ],
            [
                'attribute' => 'balance',
                'value' => $model->balance . ' RUB'    
            ],
            [
                'attribute' => 'Количество платежей',
                'value' => $ordersCount > 0 ? \yii\helpers\Html::a($ordersCount, \yii\helpers\Url::to(['/admin/order', 'OrdersSearch' => ['client_id' => $model->id, 'status' => 1, 'is_test' => 0]]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']) 
                : $ordersCount,
                'format' => 'raw'
            ],
            [
                'attribute' => 'blocked_balance',
                'label' => 'Заблокированный баланс',
                'value' => $model->blocked_balance . ' RUB'
            ],
            [
                'attribute' => 'total_withdrawal',
                'value' => $model->total_withdrawal . ' RUB'
            ],
            [
                'attribute' => 'commissions',
                'label' => 'Комиссии платежных систем',
                'value' => $commissionsCount . ' RUB'
            ],
            [
                'attribute' => 'statisticBlock',
                'label' => 'Статистика',
                'value' => '',
                'captionOptions' => [
                    'class' => 'pt-4 bg-dark text-light border-0'
                ],
                'contentOptions' => [
                    'class' => 'pt-4 bg-dark border-0'
                ]
            ],
            [
                'attribute' => 'commission',
                'value' => $model->commission . ' %'
            ],
            [
                'attribute' => 'profit',
                'value' => $model->profit . ' RUB'
            ],
            [
                'attribute' => 'total_withdrawal_profit',
                'label' => 'Cумма выведенных ДС из прибыли',
                'value' => $model->total_withdrawal_profit . ' RUB'
            ],
            [
                'attribute' => 'can_withdrawal_profit',
                'label' => 'Доступная сумма к выводу',
                'value' => $model->profit - $model->total_withdrawal_profit . ' RUB'
            ],
            [
                'attribute' => 'testBlock',
                'label' => 'Тестовый блок',
                'value' => '',
                'captionOptions' => [
                    'class' => 'pt-4 bg-dark text-danger border-0'
                ],
                'contentOptions' => [
                    'class' => 'pt-4 bg-dark border-0'
                ]
            ],
            'test_balance',
            'test_profit',
            'test_blocked_balance',
            'total_withdrawal_profit_test',
            'test_total_withdrawal',
        ],
        'options' => [
            'class' => 'table table-striped table-bordered detail-view bg-light text-nowrap'
        ]
    ]);
    ?>

</div>