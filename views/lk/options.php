<?php
/** @var yii\web\View $this */
$this->title = 'Настройки';
?>
<h1><?= $this->title; ?></h1>

<script>
    let link = document.getElementById('sideBarOptionLink');
    let button = document.getElementById('sideBarOptionBtn');
    link.href = '#';
    button.disabled = true;
</script>