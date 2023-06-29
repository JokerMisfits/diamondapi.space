<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\admin\models\OrdersSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="orders-search">

    <?php 
        $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]);
    ?>

    <?php //echo $form->field($model, 'id') ?>

    <?= $form->field($model, 'tg_user_id'); //->label('ID пользователя telegram') ?>

    <?php // $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'count') ?>

    <?php // echo $form->field($model, 'method') ?>

    <?php // echo $form->field($model, 'shop') ?>

    <?php // echo $form->field($model, 'position_name') ?>

    <?php // echo $form->field($model, 'access_days') ?>

    <?php // echo $form->field($model, 'created_time') ?>

    <?php // echo $form->field($model, 'resulted_time') ?>

    <?php // echo $form->field($model, 'is_test') ?>

    <?php // echo $form->field($model, 'web_app_query_id') ?>

    <?php // echo $form->field($model, 'currency') ?>

    <?php // echo $form->field($model, 'count_in_currency') ?>

    <?php // echo $form->field($model, 'commission') ?>

    <?php // echo $form->field($model, 'paypal_order_id') ?> 

    <?php // echo $form->field($model, 'client_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Назад', ['/admin/order'], ['class' => 'btn btn-outline-secondary']); ?>
        <?php // echo Html::resetButton('Сбросить поиск', ['class' => 'btn btn-outline-secondary']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>