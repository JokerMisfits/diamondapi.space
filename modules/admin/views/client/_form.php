<?php

use app\models\TgMembers;

/** @var yii\web\View $this */
/** @var app\models\Clients $model */
/** @var yii\widgets\ActiveForm $form */
/** @var string $from */
?>

<div class="clients-form">

    <?php $form = yii\widgets\ActiveForm::begin(); ?>

    <?= $form->field($model, 'tg_user_id')->textInput(['maxlength' => true])->label('ID владельца в telegram')->hint('Или выберите из списка ниже'); ?>

    <?= $form->field($model, 'tg_member_id')->dropDownList(yii\helpers\ArrayHelper::map(TgMembers::find()->select(['id', 'tg_user_id'])->groupBy('tg_user_id')->all(), 'id', 'tg_user_id'), ['prompt' => 'Выберите пользователя', 'class' => 'form-control', 'style' => 'cursor: pointer;'])->label('Лиды'); ?>

    <?= $form->field($model, 'shop')->textInput(['maxlength' => true]); ?>

    <?php
        if($from === 'create'){
            echo $form->field($model, 'cost')->textInput();
        }
    ?>

    <?= $form->field($model, 'commission')->textInput(); ?>

    <?= $form->field($model, 'min_count_withdrawal')->textInput(); ?>

    <?= $form->field($model, 'robokassa')->radio([], false)->label('Подключить RoboKassa'); ?>

    <?= $form->field($model, 'paykassa')->radio([], false)->label('Подключить PayKassa'); ?>

    <?= $form->field($model, 'freekassa')->radio([], false)->label('Подключить FreeKassa'); ?>

    <?= $form->field($model, 'paypall')->radio([], false)->label('Подключить PayPall'); ?>

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