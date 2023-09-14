<?php
/** @var yii\web\View $this */
/** @var app\models\TgMembers $model */
/** @var yii\widgets\ActiveForm $form */
/** @var string $from */
?>

<div class="tg-members-form">

    <?php 
        $form = yii\widgets\ActiveForm::begin(); 
        echo $form->errorSummary($model);
    ?>

    <?= $from === 'create' ? $form->field($model, 'tg_user_id')->textInput(['maxlength' => true]) : ''; ?>

    <?= $form->field($model, 'tg_username')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'tg_first_name')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'tg_last_name')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'tg_bio')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'tg_type')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'is_filled')->dropDownList([0 => 'Нет', 1 => 'Да'], ['class' => 'form-control', 'style' => 'cursor: pointer;']); ?>

    <div class="form-group">
        <?= yii\helpers\Html::submitButton('Сохранить', ['class' => 'btn btn-success']); ?>
    </div>

    <?php yii\widgets\ActiveForm::end(); ?>

</div>