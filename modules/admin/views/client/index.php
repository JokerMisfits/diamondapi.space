<?php
/** @var yii\web\View $this */
/** @var app\modules\admin\models\ClientsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Клиенты';
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clients-index">

    <div class="mx-1 mx-md-2">
        <p>
            <?= yii\helpers\Html::a('Добавить клиента', ['create'], ['class' => 'btn btn-success mt-1']); ?>
            <button id="client-search-button" class="btn btn-primary mt-1" onclick="showSearch()">Показать расширенный поиск</button>
            <?= yii\helpers\Html::a('Сбросить все фильтры и сортировки', ['/admin/client?sort='], ['class' => 'btn btn-outline-secondary mt-1']); ?>
        </p>
    </div>

    <?php yii\widgets\Pjax::begin(); ?>

    <?php echo '<div id="client-search" style="display: none;">' . $this->render('_search', ['model' => $searchModel]) . '</div>'; ?>

    <?= yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{summary}\n{items}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'shop',
            [
                'attribute' => 'balance',
                'label' => 'Баланс',
                'value' => function(app\models\Clients $model){
                    return $model->balance . ' RUB';
                }
            ],
            [
                'attribute' => 'profit',
                'value' => function(app\models\Clients $model){
                    return $model->profit . ' RUB';
                }
            ],
            [
                'attribute' => 'blocked_balance',
                'label' => 'Ожидает вывода',
                'value' => function(app\models\Clients $model){
                    return $model->blocked_balance . ' RUB';
                }
            ],
            [
                'attribute' => 'cost',
                'value' => function(app\models\Clients $model){
                    return $model->cost . ' RUB';
                }
            ],
            [
                'class' => yii\grid\ActionColumn::class,
                'template' => '{update}',
                'urlCreator' => function($action, app\models\Clients $model){
                    return yii\helpers\Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
        'rowOptions' => function(app\models\Clients $model){
            return [
                'data-href' => \yii\helpers\Url::to(['client/view', 'id' => $model->id]),
                'onclick' => 'window.location.href = "' . \yii\helpers\Url::to(['client/view', 'id' => $model->id]) . '"'
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
        let form = document.getElementById('client-search');
        let button = document.getElementById('client-search-button');
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