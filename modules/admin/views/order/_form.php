<?php
/** @var yii\web\View $this */
/** @var app\models\Orders $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="orders-form">

    <?php 
        $form = yii\widgets\ActiveForm::begin();
        echo $model->is_test ? '<span class="fs-5 fw-bold text-danger">Тестовый платеж</span>' : '';
        echo $form->errorSummary($model);
    ?>

    <?= $form->field($model, 'tg_user_id')->textInput(['maxlength' => true, 'readonly' => true]); ?>

    <?= $form->field($model, 'status')->dropDownList([0 => 'Не оплачено', 1 => 'Оплачено'], ['class' => 'form-control', 'style' => 'cursor: pointer;']); ?>

    <?= $form->field($model, 'count')->textInput(); ?>

    <?= $form->field($model, 'access_days')->textInput(); ?>

    <div class="form-group">
        <?= yii\helpers\Html::submitButton('Сохранить', ['class' => 'btn btn-success']); ?>
    </div>

    <?php yii\widgets\ActiveForm::end(); ?>

</div>