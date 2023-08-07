<?php
/** @var yii\web\View $this */
/** @var app\models\Orders|array $model */
$this->title = 'Платежи';
?>

<style>
    #contentInnerDiv{
        padding-left: 0!important;
        padding-right: 0!important;
    }
</style>

<div class="lk-payments">
    <h1 class="border-bottom pt-0 pb-1 mb-0">Ваши Платежи</h1>

    <table class="table table-dark table-striped">
        <thead>
            <tr class="text-center">
                <th scope="col">#</th>
                <th scope="col">Сумма</th>
                <th scope="col">Назначение</th>
                <th scope="col">Метод оплаты</th>
                <th scope="col">Дата</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if(!empty($model)){
                    $countArr = count($model);
                    for($i = 0; $i < $countArr; $i++){
                        echo '<tr class="text-center">';
                        echo '<th scope="row">' . $i+1 . '</th>';
                        echo '<td class="text-nowrap">' . $model[$i]['count'] . '</td>';
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