<?php

use yii\helpers\Html;
use yii\web\ForbiddenHttpException;

/** @var yii\web\View $this */

$this->title = 'Verify ' . $target;
?>

<div class="lk-verify">
    <h1 class="text-center">Подтверждение <?= $target ?></h1>

    <hr class="my-2">

    <div class="text-dark mt-2 mb-2 p-2 bg-light rounded col-12 col-lg-8 offset-lg-2 border">
        <?php
            if($target == 'telegram'){
                echo '<script>blockSidebarButtons(false);</script>';
                echo '<span>1. Узнайте ваш telegram id при помощи бота </span><a href="https://t.me/my_id_bot" class="btn btn-warning" target="_blank">Перейти <i class="fas fa-external-link-alt"></i></a>';
                echo Html::beginForm(['/lk/confirmation'], 'post');
                echo '<span>2. Введите ваш telegram id </span>' . Html::Input('string', 'tg_user_id', null, ['class' => 'form-control mb-1', 'Placeholder' => 'Пример: 233529539']);
                echo '<span class="mb-2">3. Добавьте в описание вашего профиля в telegram: </span>' . '<span class="text-danger" style="font-weight: 600;">' . $token . '</span><br>';
                echo '<span>4. Напишите любое сообщение нашему боту </span><a href="https://t.me/tg_diamondapi_verify_bot" class="btn btn-warning" target="_blank">Перейти <i class="fas fa-external-link-alt"></i></a>';
                echo '<hr class="mt-3 mb-2">';
                echo Html::hiddenInput('target', $target);
                echo Html::hiddenInput('token', $token);
                echo Html::hiddenInput('csrf', $csrf);
                echo Html::submitButton('Подтвердить', ['class' => 'btn btn-primary col-12 col-md-8 col-lg-6 offset-md-2 offset-lg-3 mt-2 mb-2']);
                echo Html::endForm();
            }
            elseif($target == 'email'){
                echo Html::beginForm(['/lk/confirmation'], 'post');
                echo Html::label('Email', 'email');
                echo Html::input('email', 'email', null, ['class' => 'form-control mb-1', 'placeholder' => 'Введите ваш email']);
                echo Html::hiddenInput('target', $target);
                echo Html::hiddenInput('csrf', $csrf);
                echo Html::submitButton('Подтвердить', ['class' => 'btn btn-primary col-12 col-md-8 col-lg-6 offset-md-2 offset-lg-3 mt-2 mb-2']);
                echo Html::endForm();
            }
            elseif($target == 'phone'){
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
            else{
                throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        ?>
    </div>
</div>