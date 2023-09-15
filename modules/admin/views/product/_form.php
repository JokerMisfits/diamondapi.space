<?php
/** @var yii\web\View $this */
/** @var app\models\Products $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="products-form">

    <?php $form = yii\widgets\ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_id')->dropDownList(yii\helpers\ArrayHelper::map(app\models\Clients::find()->all(), 'id', 'shop'))->label('Продавец'); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'access_days')->textInput(); ?>

    <?= $form->field($model, 'discount')->textInput(); ?>

    <div class="form-group">
        <?= yii\helpers\Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php yii\widgets\ActiveForm::end(); ?>

</div>