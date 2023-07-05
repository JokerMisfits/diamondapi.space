<?php
    /** @var yii\web\View $this */

    $this->title = 'Finance';
?>

<h1>Finance</h1>
<p>Страница вывода ДС из магазина</p>

<script>
let link = document.getElementById('sideBarFinLink');
let button = document.getElementById('sideBarFinBtn');
link.href = '#';
button.disabled = true;
</script>