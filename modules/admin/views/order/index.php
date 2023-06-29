<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\models\Orders;
use yii\grid\GridView;
use app\models\Clients;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\LinkPager;

/** @var yii\web\View $this */
/** @var app\modules\admin\models\OrdersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Оплаты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-index">
    <p>
        <?php // echo Html::a('Создать заказ', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
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
                'filter' => Html::activeDropDownList($searchModel, 'client_id', ArrayHelper::map(Clients::find()->all(), 'id', 'shop'), ['class' => 'form-control', 'prompt' => 'Все'])
            ],
            // [
            //     'attribute' => 'currency',
            //     'label' => 'Валюта',
            //     'filter' => ''
            // ],
            [
                'attribute' => 'method',
                'label' => 'Метод оплаты',
                'filter' => Html::activeDropDownList($searchModel, 'method', ['RoboKassa' => 'RoboKassa', 'PayKassa' => 'PayKassa', 'FreeKassa' => 'FreeKassa', 'PayPal' => 'PayPal'],
                ['class' => 'form-control selectpicker', 'data-style' => 'btn-primary', 'prompt' => 'Все'])
            ],
            
            //'position_name',
            //'access_days',
            [
                'attribute' => 'status',
                'label' => 'Статус платежа',
                'value' => function ($model) {
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
                'label' => 'Дата создания',
                'value' => function ($model) {
                    $dateTime = new DateTime($model->created_time, new DateTimeZone('Europe/Moscow'));
                    return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                },
                'filter' => ''
            ],
            //'resulted_time',
            //'is_test',
            //'web_app_query_id',
            
            //'count_in_currency',
            //'commission',
            //'paypal_order_id',
            //'client_id',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Orders $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?= '<div class="col-12 col-sm-10 col-md-8 col-lg-4 offset-0 offset-sm-1 offset-md-2 offset-lg-4">' . LinkPager::widget(['pagination' => $dataProvider->pagination,]) . '</div>' ?>

    <?php Pjax::end(); ?>

</div>