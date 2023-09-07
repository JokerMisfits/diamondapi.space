<?php
/** @var yii\web\View $this */
/** @var app\modules\admin\models\WithdrawalsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Заявки на вывод ДС';
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdrawals-index">

    <?= '<span class="fw-bold text-danger">СДЕЛАТЬ СВЕРКУ И БЛОКИРОВКУ ЕСЛИ СВЕРКА НЕ ПРОЙДЕНА</span>'; ?>

    <div class="mx-1 mx-md-2">
        <p>
            <?= yii\helpers\Html::a('Создать заявку', ['create'], ['class' => 'btn btn-success mt-1']); ?>
            <button id="withdrawal-search-button" class="btn btn-primary mt-1" onclick="showSearch()">Показать расширенный поиск</button>
            <?= yii\helpers\Html::a('Сбросить все фильтры и сортировки', ['/admin/withdrawal?sort='], ['class' => 'btn btn-outline-secondary mt-1']); ?>
        </p>
    </div>

    <?php yii\widgets\Pjax::begin(); ?>

    <?php echo '<div id="withdrawal-search" style="display: none;">' . $this->render('_search', ['model' => $searchModel]) . '</div>'; ?>

    <?= yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{summary}\n{items}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'shop',
                'label' => 'Клиент',
                'filter' => yii\helpers\Html::activeDropDownList($searchModel, 'shop', yii\helpers\ArrayHelper::map(app\models\Clients::find()->all(), 'shop', 'shop'),
                ['class' => 'form-control', 'prompt' => 'Все', 'style' => 'cursor: pointer;'])
            ],
            [
                'attribute' => 'count',
                'value' => function(app\models\Withdrawals $model){
                    return $model->count . ' RUB';
                }
            ],
            [
                'attribute' => 'status',
                'value' => function(app\models\Withdrawals $model){
                    return $model->status === 0 ? '<span class="fw-bold text-warning">Ожидает подтверждения с почты</span>' : 
                    ($model->status === 1 ? '<span class="fw-bold text-warning">Ожидает вывода денежных средств</span>' : 
                    ($model->status === 2 ? '<span class="fw-bold text-danger">Заявка отклонена</span>' : 
                    ($model->status === 3 ? '<span class="fw-bold text-success">Заявка выплачена</span>' : 
                    ($model->status === 4 ? '<span class="fw-bold text-danger">Отказ пользователя</span>' : ''))));
                },
                'filter' => yii\helpers\Html::activeDropDownList($searchModel, 'status', [
                    0 => 'Ожидает подтверждения с почты',
                    1 => 'Ожидает вывода денежных средств',
                    2 => 'Заявка отклонена',
                    3 => 'Заявка выплачена',
                    4 => 'Отказ пользователя'
                ], ['class' => 'form-control', 'prompt' => 'Все', 'style' => 'cursor: pointer;']),
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center']
            ],
            [
                'attribute' => 'commission',
                'value' => function(app\models\Withdrawals $model){
                    return $model->commission . ' RUB';
                }
            ],
            [
                'attribute' => 'confirmed_time',
                'visible' => function(app\models\Withdrawals $model){
                    return $model->status === 1;
                },
                'label' => 'Дата подтверждения',
                'value' => function(app\models\Withdrawals $model){
                    if(isset($model->confirmed_time)){
                        $dateTime = new DateTime($model->confirmed_time, null);
                        return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                    }
                    return $model->confirmed_time;
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'confirmed_time',
                    'dateFormat' => 'php:d.m.Y',
                    'options' => ['class' => 'form-control', 'placeholder' => 'Все', 'readonly' => true, 'style' => 'cursor: pointer;']
                ])
            ],
            [
                'attribute' => 'resulted_time',
                'visible' => function(app\models\Withdrawals $model){
                    return $model->status > 1;
                },
                'label' => 'Дата закрытия',
                'value' => function(app\models\Withdrawals $model){
                    if(isset($model->resulted_time)){
                        $dateTime = new DateTime($model->resulted_time, null);
                        return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                    }
                    return $model->resulted_time;
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'resulted_time',
                    'dateFormat' => 'php:d.m.Y',
                    'options' => ['class' => 'form-control', 'placeholder' => 'Все', 'readonly' => true, 'style' => 'cursor: pointer;']
                ])
            ]
        ],
        'rowOptions' => function(app\models\Withdrawals $model){
            return [
                'data-href' => \yii\helpers\Url::to(['withdrawal/view', 'id' => $model->id]),
                'onclick' => 'window.location.href = "' . \yii\helpers\Url::to(['withdrawal/view', 'id' => $model->id]) . '"'
            ];
        }
    ]);
    ?>

    <?= yii\bootstrap5\LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'options' => [
                'class' => 'pagination d-flex justify-content-center',
            ],
            'linkOptions' => [
                'class' => 'page-link page-item',
            ],
            'disableCurrentPageButton' => true,
            'maxButtonCount' => 10
        ]);
    ?>

    <?php yii\widgets\Pjax::end(); ?>

</div>

<script>
    function showSearch(){
        let form = document.getElementById('withdrawal-search');
        let button = document.getElementById('withdrawal-search-button');
        if(form.style.display === 'none'){
            form.style.display = 'block';
            button.innerText = 'Скрыть расширенный поиск';
        }
        else{
            form.style.display = 'none';
            button.innerText = 'Показать расширенный поиск';
        }
    }
</script>