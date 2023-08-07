<?php
/** @var yii\web\View $this */
/** @var app\models\Users $model */
/** @var yii\widgets\ActiveForm $form */
$this->title = 'Авторизация';
?>

<div class="site-login col-12 col-md-6 offset-md-3 mt-2 p-2 rounded text-dark bg-light border">
    <?php 
        $form = yii\widgets\ActiveForm::begin([
            'action' => ['site/login'],
            'method' => 'post',
            'options' => [
                'autocomplete' => 'off'
            ],
            'enableClientValidation' => true, // Включение клиентской валидации формы
            'enableAjaxValidation' => false, // Включение AJAX-валидации формы
            'validateOnBlur' => true, // Валидация поля при потере фокуса
            'validateOnChange' => false, // Валидация поля при изменении его значения
            'validateOnType' => false, // Валидация поля во время его набора текста
            'validateOnSubmit' => true // Валидация формы при отправке
        ]);
        echo '<legend>Авторизация</legend>';
        echo '<hr class="mt-0 mb-4">';
        echo $form->field($model, 'username')->textInput(['minlength' => 5, 'maxlength' => 32, 'placeholder' => 'Введите ваш логин', 'class' => 'form-control']);
        echo $form->field($model, 'password')->passwordInput(['enableAjaxValidation' => false, 'minlength' => 6, 'maxlength' => 64, 'placeholder' => 'Введите ваш пароль', 'class' => 'form-control']);
        echo '<div class="form-group">';
        echo yii\helpers\Html::submitButton('Войти', ['class' => 'btn btn-dark col-12 mt-0 mb-0']);
        echo $form->field($model, 'rememberMe',)->checkbox(['class' => 'form-check-input']);
        echo '<a href="/signup" class="btn btn-outline-primary col-12 mt-0 mb-0">Создать аккаунт</a>';
        echo '</div>';
        yii\widgets\ActiveForm::end();
    ?>
</div>