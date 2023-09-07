<?php
/** @var yii\web\View $this */
/** @var app\models\Withdrawals $model */

$this->title = 'Изменить Заявку №' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Заявки на вывод ДС', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->shop, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="withdrawals-update container pt-0 mb-4 border border-dark rounded bg-light">

    <h1><?= yii\helpers\Html::encode($this->title); ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'from' => 'update'
    ]);
    ?>

</div>