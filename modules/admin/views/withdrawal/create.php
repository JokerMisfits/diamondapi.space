<?php
/** @var yii\web\View $this */
/** @var app\models\Withdrawals $model */

$this->title = 'Создание заявки на вывод ДС';
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Заявки на вывод ДС', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdrawals-create container pt-0 mb-4 border border-dark rounded bg-light">

    <h1><?= yii\helpers\Html::encode($this->title); ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'from' => 'create'
    ]);
    ?>

</div>