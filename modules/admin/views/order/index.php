<?php
use app\models\Clients;

/** @var yii\web\View $this */
/** @var app\modules\admin\models\OrdersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Платежи';
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-index">

    <div class="mx-1 mx-md-2">
        <p>
            <button id="order-search-button" class="btn btn-primary mt-1" onclick="showSearch()">Показать расширенный поиск</button>
            <?= yii\helpers\Html::a('Сбросить все фильтры и сортировки', ['/admin/order?sort='], ['class' => 'btn btn-outline-secondary mt-1']); ?>
        </p>
    </div>

    <?php yii\widgets\Pjax::begin(); ?>

    <?php echo '<div id="order-search" style="display: none;">' . $this->render('_search', ['model' => $searchModel]) . '</div>'; ?>

    <?= yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{summary}\n{items}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'contentOptions' => ['class' => 'text-center']],
            [
                'attribute' => 'id',
                'label' => 'Номер платежа',
                'filter' => ''
            ],
            [
                'attribute' => 'tg_user_id',
                'label' => 'ID пользователя telegram',
                'filter' => ''
            ],
            [
                'attribute' => 'shop',
                'label' => 'Клиент',
                'filter' => yii\helpers\Html::activeDropDownList($searchModel, 'client_id', yii\helpers\ArrayHelper::map(Clients::find()->all(), 'id', 'shop'), ['class' => 'form-control', 'prompt' => 'Все'])
            ],
            [
                'attribute' => 'method',
                'label' => 'Метод оплаты',
                'filter' => yii\helpers\Html::activeDropDownList($searchModel, 'method', ['RoboKassa' => 'RoboKassa', 'PayKassa' => 'PayKassa', 'FreeKassa' => 'FreeKassa', 'PayPal' => 'PayPal'],
                ['class' => 'form-control selectpicker', 'data-style' => 'btn-primary', 'prompt' => 'Все'])
            ],
            [
                'attribute' => 'status',
                'label' => 'Статус платежа',
                'value' => function($model){
                    return $model->status == 1 ? '<span class="text-success fw-bold">Оплачено</span>' : '<span class="text-danger fw-bold">Не оплачено</span>';
                },
                'filter' => ['1' => 'Оплачено', '0' => 'Не оплачено'],
                'filterInputOptions' => ['class' => 'form-control selectpicker', 'data-style' => 'btn-primary', 'prompt' => 'Все'],
                'format' => 'raw',
                'contentOptions' => ['style' => 'text-align: center;']
            ],
            [
                'attribute' => 'count',
                'label' => 'Сумма в рублях',
                'filter' => ''
            ],
            [
                'attribute' => 'created_time',
                'label' => 'Дата платежа',
                'value' => function($model){
                    if(isset($model->created_time)){
                        $dateTime = new DateTime($model->created_time, null);
                        return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                    }
                    return $model->created_time;
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_time',
                    'dateFormat' => 'php:d.m.Y',
                    'options' => ['class' => 'form-control selectpicker', 'data-style' => 'btn-primary', 'placeholder' => 'Все', 'readonly' => true, 'style' => 'cursor: pointer;']
                ])
            ]
        ],
        'rowOptions' => function($model){
            return [
                'data-href' => \yii\helpers\Url::to(['order/view', 'id' => $model->id]),
                'onclick' => 'window.location.href = "' . \yii\helpers\Url::to(['order/view', 'id' => $model->id]) . '"'
            ];
        },
    ]);
    ?>

    <?php
    echo yii\bootstrap5\LinkPager::widget([
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
        let form = document.getElementById('order-search');
        let button = document.getElementById('order-search-button');
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