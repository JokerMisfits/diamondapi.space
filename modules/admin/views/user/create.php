<?php
/** @var yii\web\View $this */
/** @var app\models\Users $model */

$this->title = 'Создать Аккаунт';
$this->params['breadcrumbs'][] = ['label' => 'Аккаунты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-create">

    <h1><?= yii\helpers\Html::encode($this->title); ?></h1>

    <?= $this->render('_form', [
        'model' => $model
    ]);
    ?>

</div>