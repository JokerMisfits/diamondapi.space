<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Users $model */
/** @var ActiveForm $form */

$this->title = 'Регистрация';
?>

<div class="site-signup col-12 col-md-6 offset-md-3 mt-2 p-2 rounded text-dark bg-light border">
    <?php 
        $form = ActiveForm::begin([
            'action' => ['site/signup'],
            'method' => 'post',
            'options' => [
                'autocomplete' => 'off'
            ],
            'enableClientValidation' => true, // Включение клиентской валидации формы
            'enableAjaxValidation' => true, // Включение AJAX-валидации формы
            'validateOnBlur' => true, // Валидация поля при потере фокуса
            'validateOnChange' => false, // Валидация поля при изменении его значения
            'validateOnType' => false, // Валидация поля во время его набора текста
            'validateOnSubmit' => true // Валидация формы при отправке
        ]);
        echo '<legend>Регистрация</legend>';
        echo '<hr class="mt-0 mb-4">';
        echo $form->field($model, 'username')->textInput(['minlength' => 5, 'maxlength' => 32, 'placeholder' => 'Введите ваш логин', 'class' => 'form-control']);
        echo $form->field($model, 'password')->passwordInput(['enableAjaxValidation' => false, 'minlength' => 6, 'maxlength' => 64, 'placeholder' => 'Введите ваш пароль', 'class' => 'form-control']);
        echo $form->field($model, 'password_repeat')->passwordInput(['enableAjaxValidation' => false, 'minlength' => 6, 'maxlength' => 64, 'placeholder' => 'Введите ваш пароль повторно', 'class' => 'form-control']);
        echo Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-dark col-12 mt-0 mb-1']);
        ActiveForm::end();
    ?>
</div>