<?php
/** @var yii\web\View $this */
/** @var app\modules\admin\models\TgMembersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tg-members-index">

    <div class="mx-1 mx-md-2">
        <p>
            <?= yii\helpers\Html::a('Добавить пользователя', ['create'], ['class' => 'btn btn-success mt-1']); ?>
            <button id="tg-member-search-button" class="btn btn-primary mt-1" onclick="showSearch()">Показать расширенный поиск</button>
            <?= yii\helpers\Html::a('Сбросить все фильтры и сортировки', ['/admin/tg-member?sort='], ['class' => 'btn btn-outline-secondary mt-1']); ?>
        </p>
    </div>

    <?php yii\widgets\Pjax::begin(); ?>

    <?php echo '<div id="tg-member-search" style="display: none;">' . $this->render('_search', ['model' => $searchModel]) . '</div>'; ?>

    <?= yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{summary}\n{items}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'tg_user_id',
            'tg_username',
            'tg_first_name',
            'tg_last_name',
            [
                'attribute' => 'is_filed',
                'label' => 'Заполнено',
                'value' => function(app\models\TgMembers $model){
                    return $model->is_filed ? '<span class="fw-bold text-success">Да</span>' : '<span class="fw-bold text-danger">Нет</span>';
                },
                'filter' => yii\helpers\Html::activeDropDownList($searchModel, 'is_filed', [1 => 'Да', 0 => 'Нет'],
                ['class' => 'form-control', 'prompt' => 'Все', 'style' => 'cursor: pointer;']),
                'contentOptions' => ['class' => 'text-center'],
                'format' => 'raw'
            ]
        ],
        'rowOptions' => function(app\models\TgMembers $model){
            return [
                'data-href' => \yii\helpers\Url::to(['tg-member/view', 'id' => $model->id]),
                'onclick' => 'window.location.href = "' . \yii\helpers\Url::to(['tg-member/view', 'id' => $model->id]) . '"'
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
        let form = document.getElementById('tg-member-search');
        let button = document.getElementById('tg-member-search-button');
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