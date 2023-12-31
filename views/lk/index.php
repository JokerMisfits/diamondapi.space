<?php
/** @var yii\web\View $this */
/** @var string $email */
$this->title = 'Главная';
?>

<style>
    .lk-index{
        font-family: sans-serif;
        font-weight: 500;
    }
</style>

<div class="lk-index">

    <h1 class="text-center pt-2 pb-2 pt-md-0 pb-md-1 mb-0 border-bottom border-dark" style="margin-top: -1px;">Добро пожаловать <?= Yii::$app->user->identity->username; ?></h1>

    <?php
        if(!isset(Yii::$app->user->identity->tg_member_id)){
            echo '<script>blockSidebarButtons();</script>';
            echo '<div class="text-dark text-center my-2 p-2 bg-light rounded col-12 col-md-8 col-lg-6 offset-md-2 offset-lg-3 border">';
            echo yii\helpers\Html::beginForm(['/lk/verify'], 'post');
            echo '<legend>Для получения доступа к остальным разделам личного кабинета</legend>' . '<br>' . 'Необходимо привязать вашу учетную запись telegram к вашему аккаунту.';
            echo yii\helpers\Html::hiddenInput('target', 'telegram');
            echo yii\helpers\Html::hiddenInput('csrf', $csrf);
            echo yii\helpers\Html::submitButton('Приступить <i class="fab fa-telegram py-1 px-1"></i>', ['class' => 'btn btn-primary col-12 col-md-10 col-lg-8 offset-md-1 offset-lg-2 mt-2 mb-2 d-flex justify-content-center']);
            echo yii\helpers\Html::endForm();
            echo '</div>';
        }
        elseif(!isset(Yii::$app->user->identity->email) && !isset($email)){
            echo '<div class="text-dark text-center my-2 p-2 bg-light rounded col-12 col-lg-8 offset-lg-2 border">';
            echo '<legend>Для получения доступа к выводу ДС</legend>' . '<br>' . 'Необходимо привязать email к вашему аккаунту.';
            echo yii\helpers\Html::beginForm(['/lk/verify'], 'post');
            echo yii\helpers\Html::hiddenInput('target', 'email');
            echo yii\helpers\Html::hiddenInput('csrf', $csrf);
            echo yii\helpers\Html::submitButton('Приступить <i class="far fa-envelope"></i>', ['class' => 'btn btn-primary col-12 col-md-8 col-lg-6 mt-2 mb-2']);
            echo yii\helpers\Html::endForm();
            echo '</div>';
        }
        elseif(!isset(Yii::$app->user->identity->email) && isset($email)){
            echo 'Отправить письмо повторно или изменить почтовый адрес';
        }
        else{
            echo '<div class="text-danger text-center my-2 p-2 bg-dark col-12" style="min-height: 200px">';
            echo 'БЛОК ВЫВОДА ГРАФИКОВ И СТАТИСТИКА';
            echo '</div>';
        }
    ?>
</div>

<script>
    let link = document.getElementById('sideBarProfileLink');
    let button = document.getElementById('sideBarProfileBtn');
    link.href = '#';
    button.disabled = true;
</script>