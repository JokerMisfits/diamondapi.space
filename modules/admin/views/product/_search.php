<?php
/** @var yii\web\View $this */
/** @var app\modules\admin\models\ProductsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="products-search container col-12 col-md-6 offset-md-3 py-2 my-2 border rounded text-dark bg-light">

    <?php $form = yii\widgets\ActiveForm::begin([
        'action' => ['index'],
        'method' => 'GET',
        'options' => [
            'data-pjax' => 1
        ]
    ]);
    ?>

    <?= $form->field($model, 'id'); ?>

    <?= $form->field($model, 'name'); ?>

    <div class="form-group">
        <?= yii\helpers\Html::submitButton('Поиск', ['class' => 'btn btn-primary']); ?>
    </div>

    <?php yii\widgets\ActiveForm::end(); ?>

</div>