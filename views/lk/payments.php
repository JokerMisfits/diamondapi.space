<?php
/** @var yii\web\View $this */

$this->title = 'Payments';

?>

<div class="lk-payments">
    <h1 class="mt-2" style="margin-bottom: -20px;">Ваши Платежи</h1><hr class="mb-3">

    <table class="table table-dark table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Сумма платежа</th>
                <th scope="col">Назначение платежа</th>
                <th scope="col">Метод оплаты</th>
                <th scope="col">Дата платежа</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if(isset($model[0])){
                    $countArr = count($model);
                    for($i = 0; $i < $countArr; $i++){
                        echo '<tr>';
                        echo '<th scope="row">' . $i+1 . '</th>';
                        echo '<td>' . $model[$i]['count'] . '</td>';
                        echo '<td>' . $model[$i]['shop'] . '</td>';
                        echo '<td>' . $model[$i]['method'] . '</td>';
                        echo '<td>' . $model[$i]['resulted_time'] . '</td>';
                        echo '</tr>';
                    }
                }
                else{
                    echo '<tr><td colspan="5">Ничего не найдено</td></tr>';
                }

            ?>
        </tbody>
    </table>
</div>

<script>
let link = document.getElementById('sideBarPayLink');
let button = document.getElementById('sideBarPayBtn');
link.href = '#';
button.disabled = true;
</script>