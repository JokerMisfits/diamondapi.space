<?php
/** @var yii\web\View $this */
/** @var app\models\Orders $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="orders-form">

    <?php $form = yii\widgets\ActiveForm::begin(); ?>

    <?= $form->field($model, 'tg_user_id')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'status')->textInput(); ?>

    <?= $form->field($model, 'count')->textInput(); ?>

    <?= $form->field($model, 'method')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'shop')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'position_name')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'access_days')->textInput(); ?>

    <?= $form->field($model, 'created_time')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'resulted_time')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'is_test')->textInput(); ?>

    <?= $form->field($model, 'web_app_query_id')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'currency')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'count_in_currency')->textInput(); ?>

    <?= $form->field($model, 'commission')->textInput(); ?>

    <?= $form->field($model, 'paypal_order_id')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'client_id')->textInput(); ?>

    <div class="form-group">
        <?= yii\helpers\Html::submitButton('Сохранить', ['class' => 'btn btn-success']); ?>
    </div>

    <?php yii\widgets\ActiveForm::end(); ?>

</div>