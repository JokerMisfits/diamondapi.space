<?php
/** @var yii\web\View $this */
/** @var app\modules\admin\models\ClientsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="clients-search container col-12 col-md-6 offset-md-3 py-2 my-2 border rounded text-dark bg-light">

    <?php $form = yii\widgets\ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]);
    ?>

    <?php echo $form->field($model, 'id'); ?>

    <?php echo $form->field($model, 'test_balance'); ?>

    <?php echo $form->field($model, 'test_blocked_balance'); ?>

    <?php echo $form->field($model, 'test_profit'); ?>

    <?php echo $form->field($model, 'test_total_withdrawal'); ?>

    <?php echo $form->field($model, 'total_withdrawal_profit_test'); ?>

    <?php echo $form->field($model, 'min_count_withdrawal'); ?>

    <div class="form-group">
        <?= yii\helpers\Html::submitButton('Поиск', ['class' => 'btn btn-primary']); ?>
    </div>

    <?php yii\widgets\ActiveForm::end(); ?>

</div>