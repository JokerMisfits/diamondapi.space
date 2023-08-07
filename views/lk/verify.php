<?php
/** @var yii\web\View $this */
$this->title = 'Подтверждение ' . $target;
?>

<div class="lk-verify">
    <h1 class="text-center border-bottom border-dark p-0" style="font-family: sans-serif; font-weight: 500; padding-bottom: 3px!important;">Подтверждение <?= $target ?></h1>

    <div class="col-12 col-lg-8 offset-lg-2 my-2 p-2 bg-light text-dark border rounded">
        <?php
            if($target == 'telegram'){
                echo '<script>blockSidebarButtons(false);</script>';
                echo '<span>1. Узнайте ваш telegram id при помощи бота </span><a href="https://t.me/my_id_bot" class="btn btn-warning" target="_blank">Перейти <i class="fas fa-external-link-alt"></i></a>';
                echo yii\helpers\Html::beginForm(['/lk/confirmation'], 'post');
                echo '<span>2. Введите ваш telegram id </span>' . yii\helpers\Html::Input('string', 'tg_user_id', null, ['class' => 'form-control mb-1', 'Placeholder' => 'Пример: 233529539']);
                echo '<span class="mb-2">3. Добавьте в описание вашего профиля в telegram: </span>' . '<span class="text-danger" style="font-weight: 600;">' . $token . '</span><br>';
                echo '<span>4. Напишите любое сообщение нашему боту </span><a href="https://t.me/tg_diamondapi_verify_bot" class="btn btn-warning" target="_blank">Перейти <i class="fas fa-external-link-alt"></i></a>';
                echo '<hr class="mt-3 mb-2">';
                echo yii\helpers\Html::hiddenInput('target', $target);
                echo yii\helpers\Html::hiddenInput('token', $token);
                echo yii\helpers\Html::hiddenInput('csrf', $csrf);
                echo yii\helpers\Html::submitButton('Подтвердить', ['class' => 'btn btn-primary col-12 col-md-8 col-lg-6 offset-md-2 offset-lg-3 mt-2 mb-2']);
                echo yii\helpers\Html::endForm();
            }
            elseif($target == 'email'){
                echo yii\helpers\Html::beginForm(['/lk/confirmation'], 'post');
                echo yii\helpers\Html::label('Email', 'email');
                echo yii\helpers\Html::input('email', 'email', null, ['class' => 'form-control mb-1', 'placeholder' => 'Введите ваш email']);
                echo yii\helpers\Html::hiddenInput('target', $target);
                echo yii\helpers\Html::hiddenInput('csrf', $csrf);
                echo yii\helpers\Html::submitButton('Подтвердить', ['class' => 'btn btn-primary col-12 col-md-8 col-lg-6 offset-md-2 offset-lg-3 mt-2 mb-2']);
                echo yii\helpers\Html::endForm();
            }
            elseif($target == 'phone'){
                throw new yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
            else{
                throw new yii\web\ForbiddenHttpException('You are not allowed to perform this action.', 403);
            }
        ?>
    </div>
</div>