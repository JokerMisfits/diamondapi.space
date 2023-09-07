<?php
/** @var yii\web\View $this */
/** @var app\models\Users $model */
/** @var yii\widgets\ActiveForm $form */
$this->title = 'Регистрация';
?>

<div class="site-signup container col-12 col-md-6 col-lg-4 offset-md-3 offset-lg-4 mt-2 p-2 rounded text-dark bg-light border">
    <?php 
        $form = yii\widgets\ActiveForm::begin([
            'action' => ['site/signup'],
            'method' => 'post',
            'options' => [
                'autocomplete' => 'off'
            ]
        ]);
        echo '<legend>Регистрация</legend>';
        echo '<hr class="mt-0 mb-4">';
        echo $form->field($model, 'username')->textInput(['minlength' => 5, 'maxlength' => 32, 'placeholder' => 'Введите ваш логин', 'class' => 'form-control']);
        echo $form->field($model, 'password')->passwordInput(['minlength' => 6, 'maxlength' => 64, 'placeholder' => 'Введите ваш пароль', 'class' => 'form-control']);
        echo $form->field($model, 'password_repeat')->passwordInput(['minlength' => 6, 'maxlength' => 64, 'placeholder' => 'Введите ваш пароль повторно', 'class' => 'form-control']);
        echo yii\helpers\Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-dark col-12 mt-0 mb-1']);
        yii\widgets\ActiveForm::end();
    ?>
</div>