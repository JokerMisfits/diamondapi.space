<?php
/** @var yii\web\View $this */
/** @var app\models\Clients $model */
/** @var int $ordersCount */
/** @var int $ordersCountComplete */
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
                'value' => function(app\models\Clients $model){
                    $member = $model->getTgMember()->one();
                    if($member->tg_username !== null || $member->tg_username !== ''){
                        return \yii\helpers\Html::a($member->tg_username, \yii\helpers\Url::to(['/admin/tg-member/view', 'id' => $member->id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']);
                    }
                    return $member->id;
                },
                'format' => 'raw'
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
                'label' => 'Минимальная сумма вывода ДС',
                'value' => $model->min_count_withdrawal . ' RUB'
            ],
            [
                'attribute' => 'last_change',
                'value' => function(app\models\Clients $model){
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
                'value' => $model->robokassa ? '<span class="fw-bold text-success">Подключена(ОТКЛЮЧИТЬ ДОДЕЛАТЬ)</span>' : '<span class="fw-bold text-primary">Подключить(ДОДЕЛАТЬ)</span>',
                'format' => 'raw'
            ],
            [
                'attribute' => 'paykassa',
                'label' => 'PayKassa',
                'value' => $model->paykassa ? '<span class="fw-bold text-success">Подключена(ОТКЛЮЧИТЬ ДОДЕЛАТЬ)</span>' : '<span class="fw-bold text-primary">Подключить(ДОДЕЛАТЬ)</span>',
                'format' => 'raw'
            ],
            [
                'attribute' => 'freekassa',
                'label' => 'FreeKassa',
                'value' => $model->freekassa ? '<span class="fw-bold text-success">Подключена(ОТКЛЮЧИТЬ ДОДЕЛАТЬ)</span>' : '<span class="fw-bold text-primary">Подключить(ДОДЕЛАТЬ)</span>',
                'format' => 'raw'
            ],
            [
                'attribute' => 'paypall',
                'label' => 'PayPall',
                'value' => $model->paypall ? '<span class="fw-bold text-success">Подключена(ОТКЛЮЧИТЬ ДОДЕЛАТЬ)</span>' : '<span class="fw-bold text-primary">Подключить(ДОДЕЛАТЬ)</span>',
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
                'value' => function(app\models\Clients $model){
                    $count = $model->getOrders()->where(['status' => 1, 'is_test' => 0])->count();
                    return $count > 0 ? \yii\helpers\Html::a($count, \yii\helpers\Url::to(['/admin/order', 'OrdersSearch' => ['client_id' => $model->id, 'status' => 1, 'is_test' => 0]]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']) : $count;
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'blocked_balance',
                'label' => 'Заблокированный баланс',
                'value' => $model->blocked_balance . ' RUB'
            ],
            [
                'attribute' => 'total_withdrawal',
                'label' => 'Сумма выведенных ДС',
                'value' => $model->total_withdrawal . ' RUB'
            ],
            [
                'attribute' => 'activeWithdrawals',
                'label' => 'Активные заявки на вывод ДС',
                'value' => function(app\models\Clients $model){
                    $count = $model->getWithdrawals()->where(['status' => 1, 'is_test' => 0])->count();
                    return $count > 0 ? \yii\helpers\Html::a($count, \yii\helpers\Url::to(['/admin/withdrawal', 'WithdrawalsSearch' => ['client_id' => $model->id, 'status' => 1, 'is_test' => 0]]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']) : $count;
                }
            ],
            [
                'attribute' => 'completeWithdrawals',
                'label' => 'Выплаченные заявки на вывод ДС',
                'value' => function(app\models\Clients $model){
                    $count = $model->getWithdrawals()->where(['status' => 3, 'is_test' => 0])->count();
                    return $count > 0 ? \yii\helpers\Html::a($count, \yii\helpers\Url::to(['/admin/withdrawal', 'WithdrawalsSearch' => ['client_id' => $model->id, 'status' => 3, 'is_test' => 0]]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']) : $count;
                }
            ],
            [
                'attribute' => 'Сверка',
                'value' => function(app\models\Clients $model){
                    $count = $model->getOrders()->where(['status' => 1, 'is_test' => 0])->count();
                    if($count === 0){
                        return '<span class="fw-bold text-primary">' . $count . ' Сверка не требуется</span>';
                    }
                    $countComplete = $model->getOrdersCompletes()->where(['not', ['revise' => null]])->count();
                    return $countComplete < $count ? '<span class="fw-bold fs-5 text-danger">' . $countComplete . ' Сверка не пройдена</span> ' . \yii\helpers\Html::a('Перейти', \yii\helpers\Url::to('/admin/revise'), ['class' => 'btn btn-sm btn-warning fw-bold', 'title' => 'Перейти', 'target' => '_self']) : '<span class="fw-bold fs-5 text-success">' . $countComplete . ' Сверка пройдена</span>';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'commissions',
                'label' => 'Комиссии платежных систем',
                'value' => function(app\models\Clients $model){
                    $summ = $model->getOrdersCompletes()->sum('fee');
                    if($summ !== null){
                        return round($summ, 2) . ' RUB';
                    }
                    return '0.00 RUB';
                }
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
            [
                'attribute' => 'test_balance',
                'label' => 'Тестовый баланс',
                'value' => $model->test_balance . ' RUB'
            ],
            [
                'attribute' => 'test_profit',
                'value' => $model->test_profit . ' RUB'
            ],
            [
                'attribute' => 'test_blocked_balance',
                'label' => 'Заблокированный тестовый баланс',
                'value' => $model->test_blocked_balance . ' RUB'
            ],
            [
                'attribute' => 'total_withdrawal_profit_test',
                'label' => 'Cумма выведенных ДС из тестовой прибыли',
                'value' => $model->total_withdrawal_profit_test . ' RUB'
            ],
            [
                'attribute' => 'test_total_withdrawal',
                'value' => $model->test_total_withdrawal . ' RUB'
            ]
        ],
        'options' => [
            'class' => 'table table-striped table-bordered detail-view bg-light text-nowrap'
        ]
    ]);
    ?>

</div>