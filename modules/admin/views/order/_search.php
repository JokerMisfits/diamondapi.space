<?php
/** @var yii\web\View $this */
/** @var app\modules\admin\models\OrdersSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="orders-search container col-12 col-md-6 offset-md-3 py-2 my-2 border rounded text-dark bg-light">

    <?php $form = yii\widgets\ActiveForm::begin([
            'action' => ['index'],
            'method' => 'GET',
            'options' => [
                'data-pjax' => 1
            ]
        ]);
    ?>

    <?= $form->field($model, 'is_test')->dropDownList([0 => 'Нет', 1 => 'Да'], ['class' => 'form-control', 'prompt' => 'Все', 'style' => 'cursor: pointer;']); ?>

    <div class="form-group">
        <?= yii\helpers\Html::submitButton('Найти', ['class' => 'btn btn-primary']); ?>
    </div>

    <?php yii\widgets\ActiveForm::end(); ?>

</div>