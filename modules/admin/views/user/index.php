<?php
/** @var yii\web\View $this */
/** @var app\modules\admin\models\UsersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Аккаунты';
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index">

    <div class="mx-1 mx-md-2">
        <p>
            <?= yii\helpers\Html::a('Создать аккаунт', ['create'], ['class' => 'btn btn-success mt-1']); ?>
            <button id="users-search-button" class="btn btn-primary mt-1" onclick="showSearch()">Показать расширенный поиск</button>
            <?= yii\helpers\Html::a('Сбросить все фильтры и сортировки', ['/admin?sort='], ['class' => 'btn btn-outline-secondary mt-1']); ?>
        </p>
    </div>

    <?php yii\widgets\Pjax::begin(); ?>

    <?php echo '<div id="users-search" style="display: none;">' . $this->render('_search', ['model' => $searchModel]) . '</div>'; ?>

    <?= yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{summary}\n{items}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'username',
            [
                'attribute' => 'tg_member_id',
                'label' => 'Telegram',
                'value' => function(app\models\Users $model){
                    $member = $model->getTgMember()->one();
                    if($member === null){
                        return null;
                    }
                    elseif(isset($member->tg_username)){
                        return yii\helpers\Html::a($member->tg_username, \yii\helpers\Url::to(['/admin/tg-member/view', 'id' => $model->tg_member_id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']);
                    }
                    return yii\helpers\Html::a($member->tg_user_id, \yii\helpers\Url::to(['/admin/tg-member/view', 'id' => $model->tg_member_id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'registration_date',
                'label' => 'Дата регистрации',
                'value' => function(app\models\Users $model){
                    if(isset($model->registration_date)){
                        $dateTime = new DateTime($model->registration_date, null);
                        return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                    }
                    return $model->registration_date;
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'registration_date',
                    'dateFormat' => 'php:d.m.Y',
                    'options' => ['class' => 'form-control', 'placeholder' => 'Все', 'readonly' => true, 'style' => 'cursor: pointer;']
                ])
            ]
        ],
        'rowOptions' => function(app\models\Users $model){
            return [
                'data-href' => \yii\helpers\Url::to(['user/view', 'id' => $model->id]),
                'onclick' => 'window.location.href = "' . \yii\helpers\Url::to(['user/view', 'id' => $model->id]) . '"'
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
        let form = document.getElementById('users-search');
        let button = document.getElementById('users-search-button');
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