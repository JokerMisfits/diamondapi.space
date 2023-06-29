<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Users $model */
/** @var ActiveForm $form */

$this->title = 'Авторизация';
?>
<div class="site-login col-12 col-md-8 offset-md-2">

    <?php $form = ActiveForm::begin([
                'action' => ['site/login'], // URL-адрес, на который будет отправлены данные формы
                'method' => 'post', // Метод HTTP-запроса
                'options' => [
                    'autocomplete' => 'off',
                ],
                'enableClientValidation' => true, // Включение клиентской валидации формы
                'enableAjaxValidation' => false, // Включение AJAX-валидации формы
                'validateOnBlur' => true, // Валидация поля при потере фокуса
                'validateOnChange' => false, // Валидация поля при изменении его значения
                'validateOnType' => false, // Валидация поля во время его набора текста
                'validateOnSubmit' => true // Валидация формы при отправке
            ]);
     ?>
        <h1 class="text-center mb-1 mt-1"><?= Html::encode($this->title); ?></h1><br>

        <?= $form->field($model, 'username')->textInput(['minlength' => 5, 'maxlength' => 32, 'placeholder' => 'Введите ваш логин', 'class' => 'form-control']); ?>
        <?= $form->field($model, 'password')->passwordInput(['enableAjaxValidation' => false, 'minlength' => 6, 'maxlength' => 64, 'placeholder' => 'Введите ваш пароль', 'class' => 'form-control']); ?>
    
        <div class="form-group">
            <?= Html::submitButton('Войти', ['class' => 'btn btn-dark col-12 mt-0 mb-0']); ?>
            <?= $form->field($model, 'rememberMe',)->checkbox(['class' => 'form-check-input']); ?>
            <a href="/signup" class="btn btn-outline-primary col-12 mt-0 mb-0">Создать аккаунт</a>
        </div>
    <?php ActiveForm::end(); ?>

</div>