<?php
/** @var yii\web\View $this */
/** @var app\models\Clients $model */

$this->title = 'Изменить клиента: ' . $model->shop;
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Клиенты', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->shop, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="clients-update container pt-0 mb-4 border border-dark rounded bg-light">

    <h1><?= yii\helpers\Html::encode($this->title); ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'from' => 'update'
    ]);
    ?>

</div>