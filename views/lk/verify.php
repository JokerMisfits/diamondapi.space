<?php

use yii\helpers\Html;
use yii\web\ForbiddenHttpException;

/** @var yii\web\View $this */

$this->title = 'Verify ' . $target;
?>
<h1 class="text-center mt-2">Подтверждение <?= $target ?></h1><hr>

<div class="lk-verify col-12 offset-lg-3 col-lg-6">
    <?php
        if($target == 'telegram'){
            echo '<span>1. Узнайте ваш telegram id при помощи бота </span><a href="https://t.me/my_id_bot" class="btn btn-warning" target="_blank">Перейти <i class="fas fa-external-link-alt"></i></a>';
            echo Html::beginForm(['/lk/confirmation'], 'post');
            echo '<span>2. Введите ваш telegram id </span>' . Html::Input('string', 'tg_user_id', null, ['class' => 'form-control mb-3']);
            echo '<span class="mb-2">3. Добавьте в описание вашего профиля в telegram: </span>' . '<span class="text-danger" style="font-weight: 600;">' . $token . '</span><br>';
            echo '<span>4. Напишите любое сообщение нашему боту </span><a href="https://t.me/tg_diamondapi_verify_bot" class="btn btn-warning" target="_blank">Перейти <i class="fas fa-external-link-alt"></i></a>';
            echo Html::hiddenInput('target', 'telegram');
            echo Html::hiddenInput('token', $token);
            echo Html::hiddenInput('csrf', $csrf);
            echo Html::submitButton('Подтвердить', ['class' => 'btn btn-primary mt-2 col-12']);
            echo Html::endForm();
        }
        elseif($target == 'email'){
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
        elseif($target == 'phone'){
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
        else{
            throw new ForbiddenHttpException('You are not allowed to perform this action.', 403);
        }
    ?>

</div>