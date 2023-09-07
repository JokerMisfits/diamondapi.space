<?php
/** @var yii\web\View $this */
/** @var app\models\Clients $model */
/** @var yii\widgets\ActiveForm $form */
/** @var string $from */
?>

<div class="clients-form">

    <?php 
        $form = yii\widgets\ActiveForm::begin(); 
        echo $form->errorSummary($model);
    ?>

    <?= $form->field($model, 'tg_user_id')->textInput(['maxlength' => true])->label('ID владельца в telegram'); ?>

    <?= $form->field($model, 'shop')->textInput(['maxlength' => true]); ?>

    <?= $from === 'create' ? $form->field($model, 'cost')->textInput() : ''; ?>

    <?= $form->field($model, 'commission')->textInput(); ?>

    <?= $form->field($model, 'min_count_withdrawal')->textInput(); ?>

    <?= $form->field($model, 'robokassa')->checkbox([], false)->label('Подключить RoboKassa'); ?>

    <?= $form->field($model, 'paykassa')->checkbox([], false)->label('Подключить PayKassa'); ?>

    <?= $form->field($model, 'freekassa')->checkbox([], false)->label('Подключить FreeKassa'); ?>

    <?= $form->field($model, 'paypall')->checkbox([], false)->label('Подключить PayPall'); ?>

    <?= $form->field($model, 'tg_chat_id')->textInput()->label('ID чата в telegram'); ?>

    <?= $form->field($model, 'tg_private_chat_id')->textInput()->label('ID приватного чата в telegram'); ?>

    <hr>

    <?= $form->field($model, 'test_balance')->textInput(); ?>

    <?= $form->field($model, 'test_blocked_balance')->textInput(); ?>

    <?= $form->field($model, 'test_profit')->textInput(); ?>

    <?= $form->field($model, 'test_total_withdrawal')->textInput(); ?>

    <?= $form->field($model, 'total_withdrawal_profit_test')->textInput(); ?>

    <div class="form-group">
        <?= yii\helpers\Html::submitButton('Сохранить', ['class' => 'btn btn-success']); ?>
    </div>

    <?php yii\widgets\ActiveForm::end(); ?>

</div>