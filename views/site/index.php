<?php
/** @var yii\web\View $this */

$this->context->layout = 'main';
$this->title = Yii::$app->name;
?>
<div class="site-index">

    <div class="position-absolute top-50 start-50 translate-middle text-center text-light">
        <h1>SOON</h2>
    </div>

    <div class="d-flex justify-content-center align-items-center">
        <img src="<?= Yii::getAlias('@web') ?>/images/landing.jpg" class="img-fluid" alt="SOON" style="height: calc(100vh - 113px); width: 100vw;">
    </div>

</div>