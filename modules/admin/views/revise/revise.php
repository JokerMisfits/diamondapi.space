<?php
/** @var yii\web\View $this */
/** @var app\models\Orders $model */
/** @var app\models\OrdersComplete $modelCompleteNew */
/** @var app\models\OrdersComplete $modelCompleteOld */
/** @var array $data */

$this->title = 'Платеж №' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Сверка', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
yii\web\YiiAsset::register($this);
$data['Revise']['Status'] = true;

?>
<div class="revise-revise table-responsive border rounded">
    <h1><?= yii\helpers\Html::encode($this->title); ?></h1>
    
    <?= yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'client_id',
                'label' => 'Назначение платежа(Клиент)',
                'value' => \yii\helpers\Html::a($model->shop, \yii\helpers\Url::to(['/admin/client/view', 'id' => $model->client_id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']),
                'format' => 'raw'
            ],
            [
                'attribute' => 'tg_member_id',
                'label' => 'Отправитель платежа(Пользователь)',
                'value' => function(app\models\Orders $model){
                    $member = $model->getTgMember()->one();
                    if($member->tg_username !== null && $member->tg_username !== ''){
                        return \yii\helpers\Html::a($member->tg_username, \yii\helpers\Url::to(['/admin/tg-member/view', 'id' => $member->id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']);
                    }
                    return \yii\helpers\Html::a($member->tg_user_id, \yii\helpers\Url::to(['/admin/tg-member/view', 'id' => $member->id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']);
                },
                'format' => 'raw'
            ],
            'method',
            'currency',
            [
                'attribute' => 'commission',
                'label' => 'Наша комисcия платежа(не процент прибыли)',
                'value' => $model->commission . ' ' . $model->currency
            ],
            [
                'attribute' => 'created_time',
                'value' => function(app\models\Orders $model){
                    $dateTime = new DateTime($model->created_time, null);
                    return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                }
            ],
            [
                'attribute' => 'resulted_time',
                'visible' => isset($model->resulted_time),
                'value' => function(app\models\Orders $model){
                    $dateTime = new DateTime($model->resulted_time, null);
                    return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                }
            ],
            [
                'attribute' => 'reviseBlock',
                'label' => 'Сверка',
                'value' => '',
                'captionOptions' => ['class' => 'pt-2 fs-5 fw-bolder bg-dark text-danger border-0'],
                'contentOptions' => [
                    'class' => 'pt-4 bg-dark border-0'
                ]
            ],
            [
                'attribute' => 'status',
                'visible' => $modelCompleteOld === null,
                'label' => 'Статус платежа:',
                'value' => function(app\models\Orders $model) use ($data){
                    if($model->status === 1 && isset($data['State']['Code']) && $data['State']['Code'] == 100){
                        return 'Оплачено <span class="text-success fw-bolder">| ' . $data['State']['CodeDescription'] . ' |</span>';
                    }
                    $data['Revise']['Status'] = false;
                    return 'Сверка не пройдена <span class="text-danger fw-bolder">| ' . $data['State']['CodeDescription'] . ' |</span>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'count',
                'visible' => $modelCompleteOld === null,
                'value' => function(app\models\Orders $model) use ($data){
                    if(isset($data) && isset($data['Info']['IncSum'])){
                        if($data['Info']['IncSum'] == $model->count){
                            return $model->count . ' RUB <span class="fw-bold text-success">| Суммы совпадают | </span>' . round($data['Info']['IncSum'], 2) . ' RUB';
                        }
                        $data['Revise']['Status'] = false;
                        return $model->count . ' RUB <span class="fw-bold text-danger">| Суммы не совпадают | </span>' . round($data['Info']['IncSum'], 2) . ' RUB';
                    }
                    $data['Revise']['Status'] = false;
                    return $model->count . ' RUB <span class="fw-bold text-danger">| Сверка не пройдена!</span>';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'count_in_currency',
                'visible' => $modelCompleteOld === null,
                'value' => function(app\models\Orders $model) use ($data){
                    if(isset($data['Info']['OutSum']) && $data['Info']['OutSum'] == $model->count_in_currency){
                        return $model->count_in_currency . ' ' . $model->currency . ' ' . ' <span class="fw-bold text-success">| Совпало |</span> ' . round($data['Info']['OutSum'], 2) . ' ' . $model->currency;
                    }
                    $data['Revise']['Status'] = false;
                    return $model->count_in_currency . ' ' . $model->currency . ' ' . ' <span class="fw-bold text-danger">| Расхождение |</span> ' . round($data['Info']['OutSum'], 2) . ' ' . $model->currency;
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'paymentCommission',
                'label' => 'Комиссия платежной системы',
                'visible' => $modelCompleteOld === null,
                'value' => function() use ($data){
                    if(isset($data['Commission'])){
                        return $data['Commission'] . ' RUB <span class="fw-bold text-success">| Комиссия автоматически расчитана | </span> (' . $data['CommssionPercent'] . '%)';
                    }
                    return '<span class="fw-bold text-danger">| Комиссия автоматически не расчитана | </span>' . 'Способ оплаты: ' . $data['Info']['IncCurrLabel'];
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'stateDate',
                'label' => 'Дата зачисления ДС',
                'visible' => $modelCompleteOld === null,
                'value' => function() use ($data){
                    if(isset($data['State']['StateDate'])){
                        return Yii::$app->formatter->asDatetime(new DateTime($data['State']['StateDate'], null), 'php:d.m.Y H:i:s') . ' <span class="fw-bold text-success"> | Зачислено |</span>';
                    }
                    $data['Revise']['Status'] = false;
                    return '<span class="fw-bold text-danger">| Сверка не пройдена!</span>';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'oldRevise',
                'label' => 'Сведения о сверке',
                'visible' => isset($modelCompleteOld->revise),
                'value' => function() use($modelCompleteOld){
                    $revise = json_decode($modelCompleteOld->revise, true);
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
        ]
    ]);
    ?>
    
    <?php
        if($data['Revise']['Status'] === true){
            if($modelCompleteOld === null){
                $data['Revise']['Auditor'] = Yii::$app->user->identity->id;
                $form = yii\widgets\ActiveForm::begin();
                echo $form->errorSummary($modelCompleteNew);
                echo $form->field($modelCompleteNew, 'shop')->hiddenInput(['value' => $model->shop])->label(null, ['class' => 'd-none']);
                echo $form->field($modelCompleteNew, 'method')->hiddenInput(['value' => $model->method])->label(null, ['class' => 'd-none']);
                echo $form->field($modelCompleteNew, 'payment_method')->hiddenInput(['value' => $data['Info']['IncCurrLabel']])->label(null, ['class' => 'd-none']);
                if(isset($data['Commission'])){
                    echo $form->field($modelCompleteNew, 'fee')->hiddenInput(['value' => $data['Commission']])->label(null, ['class' => 'd-none']);
                }
                else{
                    echo $form->field($modelCompleteNew, 'fee')->textInput()->hint('Необходимо вычеслить комиссию платежной системы самостоятельно' . "\n" . 'Способ оплаты: ' . $data['Info']['IncCurrLabel']); 
                }
                echo $form->field($modelCompleteNew, 'revise')->hiddenInput(['value' => json_encode($data)])->label(null, ['class' => 'd-none']);
                echo $form->field($modelCompleteNew, 'order_id')->hiddenInput(['value' => $model->id])->label(null, ['class' => 'd-none']);
                echo $form->field($modelCompleteNew, 'client_id')->hiddenInput(['value' => $model->client_id])->label(null, ['class' => 'd-none']);
                echo '<div class="form-group">';
                echo yii\helpers\Html::submitButton('Провести', [
                    'class' => 'btn btn-warning fw-bold',
                    'data' => [
                        'confirm' => 'Отменить проведение будет невозможно, вы уверены?',
                        'method' => 'POST'
                    ]
                ]);
                echo '</div>';
                yii\widgets\ActiveForm::end();
            }
            else{
                echo '<div class="fw-bold fs-4 text-danger text-center">Перепроведение платежа отключено.</div>';
            }
        }
        else{
            echo '<div class="fw-bold fs-4 text-danger text-center">Сверка не пройдена, проведение платежа невозможно.</div>';
        }
    ?>

</div>