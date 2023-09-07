<?php
/** @var yii\web\View $this */
/** @var app\models\TgMembers $model */

$this->title = 'Добавить пользователя';
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Пользаватели', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tg-members-create container pt-0 mb-4 border border-dark rounded bg-light">

    <h1><?= yii\helpers\Html::encode($this->title); ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'from' => 'create'
    ]);
    ?>

</div>