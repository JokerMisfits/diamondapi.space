<?php
use app\models\Clients;
use app\models\TgMembers;

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
            <?= yii\helpers\Html::a('Добавить клиента', ['create'], ['class' => 'btn btn-success']); ?>
            <button id="client-search-button" class="btn btn-primary mt-1" onclick="showSearch()">Показать расширенный поиск</button>
            <?= yii\helpers\Html::a('Сбросить все фильтры и сортировки', ['/admin/client?sort='], ['class' => 'btn btn-outline-secondary mt-1']); ?>
        </p>
    </div>

    <?php yii\widgets\Pjax::begin(); ?>

    <?php echo '<div id="client-search" style="display: none;">' . $this->render('_search', ['model' => $searchModel]) . '</div>'; ?>

    <?= yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
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
                'attribute' => 'balance',
                'label' => 'Баланс'
            ],
            'profit',
            'cost',
            [
                'class' => yii\grid\ActionColumn::class,
                'template' => '{update}',
                'urlCreator' => function($action, Clients $model){
                    return yii\helpers\Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
        'rowOptions' => function($model){
            return [
                'data-href' => \yii\helpers\Url::to(['client/view', 'id' => $model->id]),
                'onclick' => 'window.location.href = "' . \yii\helpers\Url::to(['client/view', 'id' => $model->id]) . '"'
            ];
        },
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