<?php

use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'Profile';
?>

<style>
    .lk-index{
        font-family: sans-serif;
        font-weight: 500;
    }
</style>

<div  class="lk-index">

    <h1 class="text-center mt-2">Добро пожаловать <?= $username ?></h1><hr>

    <?php
        if(!isset($tg_member_id)){
            echo '<script>blockSidebarButtons();</script>';
            echo '<div class="text-center">';
            echo 'Для получения доступа к другим разделам личного кабинета необходимо привязать вашу учетную запись telegram к вашему аккаунту.';
            echo Html::beginForm(['/lk/verify'], 'post');
            echo Html::hiddenInput('target', 'telegram');
            echo Html::hiddenInput('csrf', $csrf);
            echo Html::submitButton('Приступить <i class="fab fa-telegram"></i>', ['class' => 'col-12 col-md-6 col-lg-3 mt-2 btn btn-primary']);
            echo Html::endForm();
            echo '</div><hr>';
        }
    ?>

</div>

<script>
let link = document.getElementById('sideBarProfileLink');
let button = document.getElementById('sideBarProfileBtn');
link.href = '#';
button.disabled = true;
</script>