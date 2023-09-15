<?php
/** @var yii\web\View $this */
/** @var app\modules\admin\models\ProductsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Товары';
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="products-index">


    <div class="mx-1 mx-md-2">
        <p>
        <?= yii\helpers\Html::a('Добавить товар', ['create'], ['class' => 'btn btn-success mt-1']); ?>
        <button id="product-search-button" class="btn btn-primary mt-1" onclick="showSearch()">Показать расширенный поиск</button>
        <?= yii\helpers\Html::a('Сбросить все фильтры и сортировки', ['/admin/product?sort='], ['class' => 'btn btn-outline-secondary mt-1']); ?>
        </p>
    </div>

    <?php yii\widgets\Pjax::begin(); ?>

    <?php echo '<div id="product-search" style="display: none;">' . $this->render('_search', ['model' => $searchModel]) . '</div>'; ?>

<div class="table-responsive text-nowrap">
    <?= yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'client_id',
                'label' => 'Продавец',
                'value' => function(app\models\Products $model){
                    return $model->getClient()->one()->shop;
                },
                'filter' => yii\helpers\Html::activeDropDownList($searchModel, 'client_id', yii\helpers\ArrayHelper::map(app\models\Clients::find()->all(), 'id', 'shop'),
                ['class' => 'form-control', 'prompt' => 'Все', 'style' => 'cursor: pointer;'])
            ],
            'name',
            [
                'attribute' => 'price',
                'value' => function(app\models\Products $model){
                    return $model->price . ' RUB';
                }
            ],
            'access_days'
        ],
        'rowOptions' => function(app\models\Products $model){
            return [
                'data-href' => \yii\helpers\Url::to(['product/view', 'id' => $model->id]),
                'onclick' => 'window.location.href = "' . \yii\helpers\Url::to(['product/view', 'id' => $model->id]) . '"'
            ];
        }
    ]);
    ?>
</div>

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
        let form = document.getElementById('product-search');
        let button = document.getElementById('product-search-button');
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