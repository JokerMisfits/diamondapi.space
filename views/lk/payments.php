<?php
/** @var yii\web\View $this */

$this->title = 'Payments';
?>
<h1>Платежи</h1>
<p>Здесь будет отображаться информация о платежах пользователя</p>

<?php
    echo '<pre>';
    var_dump($model);
    echo '</pre>';
?>

<script>
let link = document.getElementById('sideBarPayLink');
let button = document.getElementById('sideBarPayBtn');
link.href = '#';
button.disabled = true;
</script>