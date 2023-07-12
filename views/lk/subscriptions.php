<?php
/** @var yii\web\View $this */

$this->title = 'Подписки';
?>
<h1>subscriptions</h1>
<p>Здесь будет отображаться информация о подписках пользователя</p>

<script>
let link = document.getElementById('sideBarSubLink');
let button = document.getElementById('sideBarSubBtn');
link.href = '#';
button.disabled = true;
</script>