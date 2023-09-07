<?php
/** @var yii\web\View $this */
/** @var app\models\Withdrawals $model */
/** @var yii\widgets\ActiveForm $form */
/** @var string $from */
?>

<div class="withdrawals-form">

    <?php 
        $form = yii\widgets\ActiveForm::begin(); 
        echo $form->errorSummary($model);
        echo '<span class="fw-bold text-danger">ДОДЕЛАТЬ back + валидацию редактирования</span>';
    ?>

    <?= $from === 'create' ? (isset($_GET['id']) && $_GET['id'] > 0 ? $form->field($model, 'client_id')->dropDownList([$_GET['id'] => app\models\Clients::find()->where(['id' => $_GET['id']])->one()->shop], ['class' => 'form-control', 'style' => 'cursor: pointer;'])->label('Клиент') : $form->field($model, 'client_id')->dropDownList(yii\helpers\ArrayHelper::map(app\models\Clients::find()->all(), 'id', 'shop'), ['class' => 'form-control', 'style' => 'cursor: pointer;'])->label('Клиент')) : ''; ?>

    <?= $from === 'create' ? $form->field($model, 'count')->textInput() : ''; ?>

    <?= $from === 'update' ? $form->field($model, 'status')->dropDownList([0 => 'Ожидает подтверждения с почты', 1 => 'Ожидает вывода денежных средств', 2 => 'Заявка отклонена', 3 => 'Заявка была выплачена', 4 => 'Отказ пользователя'], ['class' => 'form-control', 'style' => 'cursor: pointer;']) : ''; ?>

    <?= $from === 'create' ? $form->field($model, 'is_test')->dropDownList([0 => 'Нет', 1 => 'Да'], ['class' => 'form-control', 'style' => 'cursor: pointer;']) : ''; ?>

    <?= $from === 'create' ? $form->field($model, 'commission')->textInput()->hint('Не помню за что отвечает, надо глянуть') : ''; ?>

    <?= $from === 'create' ? $form->field($model, 'card_number')->textInput(['maxlength' => true]) : ''; ?>

    <?= $from === 'update' ? $form->field($model, 'comment')->textarea(['rows' => 6, 'placeholder' => 'Примеры: ' . "\n" . '//Выполнено' . "\n" . '//Номера карты не существует'])->hint('Комментарий будет отображен клиенту') : ''; ?>

    <div class="form-group">
        <?= yii\helpers\Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php yii\widgets\ActiveForm::end(); ?>

</div>