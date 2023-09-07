<?php
/** @var yii\web\View $this */
/** @var app\modules\admin\models\WithdrawalsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="withdrawals-search container col-12 col-md-6 offset-md-3 py-2 my-2 border rounded text-dark bg-light">

    <?php $form = yii\widgets\ActiveForm::begin([
        'action' => ['index'],
        'method' => 'GET',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id'); ?>

    <?= $form->field($model, 'is_test')->dropDownList([0 => 'Нет', 1 => 'Да'], ['prompt' => 'Все', 'style' => 'cursor: pointer;']); ?>

    <?= $form->field($model, 'card_number'); ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 2]); ?>

    <div class="form-group">
        <?= yii\helpers\Html::submitButton('Поиск', ['class' => 'btn btn-primary']); ?>
    </div>

    <?php yii\widgets\ActiveForm::end(); ?>

</div>