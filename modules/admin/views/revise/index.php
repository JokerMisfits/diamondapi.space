<?php
/** @var yii\web\View $this */
/** @var app\modules\admin\models\OrdersCompleteSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Сверка';
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-complete-index">

    <div class="mx-1 mx-md-2">
            <button id="revise-search-button" class="btn btn-primary mt-1" onclick="showSearch()">Показать расширенный поиск</button>
            <?= yii\helpers\Html::a('Сбросить все фильтры и сортировки', ['/admin/revise?sort='], ['class' => 'btn btn-outline-secondary mt-1']); ?>
        </p>
    </div>

    <?php yii\widgets\Pjax::begin(); ?>

    <?php echo '<div id="revise-search" style="display: none;">' . $this->render('_search', ['model' => $searchModel]) . '</div>'; ?>

    <?= yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{summary}\n{items}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'order_id',
                'label' => 'Номер платежа'
            ],
            'shop',
            'method',
            'payment_method',
            'fee',
            [
                'attribute' => 'revise',
                'value' => function(app\models\OrdersComplete $model){
                    return $model->revise !== null ? '<span class="fw-bold text-success">Заполнено</span>' : '<span class="fw-bold text-danger">Не заполнено</span>';
                },
                'filter' => '',
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center']
            ]
            //'revise',
            //'client_id',
        ],
        'rowOptions' => function(app\models\OrdersComplete $model){
            return [
                'data-href' => \yii\helpers\Url::to(['revise/view', 'id' => $model->id]),
                'onclick' => 'window.location.href = "' . \yii\helpers\Url::to(['revise/view', 'id' => $model->id]) . '"'
            ];
        },
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
        let form = document.getElementById('revise-search');
        let button = document.getElementById('revise-search-button');
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