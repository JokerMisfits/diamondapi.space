<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Users $model */
/** @var ActiveForm $form */

$this->title = 'Регистрация';
?>
<div class="site-login col-12 col-md-8 offset-md-2">

    <?php $form = ActiveForm::begin([
                'action' => ['site/signup'], // URL-адрес, на который будет отправлены данные формы
                'method' => 'post', // Метод HTTP-запроса
                'options' => [
                    'autocomplete' => 'off',
                ],
                'enableClientValidation' => true, // Включение клиентской валидации формы
                'enableAjaxValidation' => true, // Включение AJAX-валидации формы
                'validateOnBlur' => true, // Валидация поля при потере фокуса
                'validateOnChange' => false, // Валидация поля при изменении его значения
                'validateOnType' => false, // Валидация поля во время его набора текста
                'validateOnSubmit' => true // Валидация формы при отправке
            ]);
    ?>
        <h1 class="text-center mb-1 mt-1"><?= Html::encode($this->title); ?></h1><br>

        <?= $form->field($model, 'username')->textInput(['minlength' => 5, 'maxlength' => 32, 'placeholder' => 'Введите ваш логин', 'class' => 'form-control']); ?>
        <?= $form->field($model, 'password')->passwordInput(['enableAjaxValidation' => false, 'minlength' => 6, 'maxlength' => 64, 'placeholder' => 'Введите ваш пароль', 'class' => 'form-control']); ?>
        <?= $form->field($model, 'password_repeat')->passwordInput(['enableAjaxValidation' => false, 'minlength' => 6, 'maxlength' => 64, 'placeholder' => 'Введите ваш пароль повторно', 'class' => 'form-control']); ?>

        <div class="form-group">
            <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-dark col-12 mt-0']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div>