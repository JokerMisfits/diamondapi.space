<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = 'Finance';

$allWithdrawals = false;

if(isset($_GET['allWithdrawals']) && $_GET['allWithdrawals'] == 1){
    $allWithdrawals = true;
}

?>

<div class="lk-channels">
    <h1 class="mt-2" style="margin-bottom: -20px;">Ваши финансы:</h1><hr class="mb-2">

    <table class="table caption-top table-dark table-striped">
        <thead>
            <caption class="text-dark bg-light">Ваши каналы
                <?php
                    if(isset($admin) && $admin && (!isset($_GET['showTestClients']) || $_GET['showTestClients'] == 0) ){
                        echo ' <a href="' . Url::current(['showTestClients' => 1]) . '" class="btn btn-sm btn-danger r-0 text-dark">Показать тестовые <i class="fas fa-fighter-jet"></i></a>';
                    }
                    elseif(isset($admin) && $admin && isset($_GET['showTestClients']) && $_GET['showTestClients'] == 1){
                        echo ' <a href="' . Url::current(['showTestClients' => 0]) . '" class="btn btn-sm btn-danger r-0 text-dark">Вернуть обычные <i class="fas fa-fighter-jet"></i></a>';
                    }
                ?>
            </caption>
            <tr>
                <?php
                    if(isset($admin) && $admin && isset($_GET['showTestClients']) && $_GET['showTestClients'] == 1){
                        echo '<th scope="col" class="text-danger fw-bold">#</th>';
                        echo '<th scope="col" class="text-danger fw-bold">Канал</th>';
                        echo '<th scope="col" class="text-danger fw-bold">Баланс</th>';
                        echo '<th scope="col" class="text-danger fw-bold">Выведено</th>';
                        echo '<th scope="col" class="text-danger fw-bold">Ожидает вывода</th>';
                    }
                    else{
                        echo '<th scope="col">#</th>';
                        echo '<th scope="col">Канал</th>';
                        echo '<th scope="col">Баланс</th>';
                        echo '<th scope="col">Выведено</th>';
                        echo '<th scope="col">Ожидает вывода</th>';
                    }
                ?>
            </tr>
        </thead>
        <tbody>
        <?php
            if(!empty($clients)){
                $countArr = count($clients);
                if(isset($admin) && $admin && isset($_GET['showTestClients']) && $_GET['showTestClients'] == 1){
                    for($i = 0; $i < $countArr; $i++){
                        echo '<tr>';
                        echo '<th scope="row">' . $i+1 . '</th>';
                        echo '<td>' . $clients[$i]['shop'] . '</td>';
                        echo '<td>' . $clients[$i]['test_balance'] . ' рублей</td>';
                        echo '<td>' . $clients[$i]['test_total_withdrawal'] . ' рублей</td>';
                        echo '<td>' . $clients[$i]['test_blocked_balance'] . ' рублей</td>';
                        echo '</tr>';
                    }
                }
                else{
                    for($i = 0; $i < $countArr; $i++){
                        echo '<tr>';
                        echo '<th scope="row">' . $i+1 . '</th>';
                        echo '<td>' . $clients[$i]['shop'] . '</td>';
                        echo '<td>' . $clients[$i]['balance'] . ' рублей</td>';
                        echo '<td>' . $clients[$i]['total_withdrawal'] . ' рублей</td>';
                        echo '<td>' . $clients[$i]['blocked_balance'] . ' рублей</td>';
                        echo '</tr>';
                    }
                }
                if($clientsCount > 5){
                    echo '<tr><td colspan=5>';
                    $countButtons = ceil($clientsCount / 5);
                    echo '<nav aria-label="..."><ul class="pagination justify-content-center mb-0">';
                    if(!isset($_GET['clientsPage'])){
                        $currentPage = 1;
                    }
                    else{
                        $currentPage = $_GET['clientsPage'];
                    }
                    $startI = 0;
                    $endI = $countButtons;
                    if($countButtons > 10){
                        $startI = max($currentPage - 2, 1);
                        $endI = min($startI + 4, $countButtons);
                    }
                    if($currentPage > 1){
                        echo '<li class="page-item"><a class="page-link" href="' . Url::current(['clientsPage' => $currentPage - 1]) . '" aria-label="<"><span aria-hidden="true">&laquo;</span></a></li>';
                    }
                    for($i = 0; $i < $countButtons; $i++){
                        if($currentPage == $i + 1){
                            echo '<li class="page-item active" aria-current="page"><span class="page-link">' . $i+1 .'</span></li>';
                        }
                        else{
                            echo '<li class="page-item"><a class="page-link" href="' . Url::current(['clientsPage' => $i + 1]) . '">' . $i+1 . '</a></li>';
                        }
                    }
                    if($currentPage < $countButtons){
                        echo '<li class="page-item"><a class="page-link" href="' . Url::current(['clientsPage' => $currentPage + 1]) . '" aria-label=">"><span aria-hidden="true">&raquo;</span></a></li>';
                    }
                    echo '</ul></nav>';
                    echo '</td></tr>';
                }
            }
            else{
                echo '<tr><td colspan=5>Ничего не найдено</td></tr>';
            }
        ?>
        </tbody>
    </table>

    <hr class="mt-4 mb-4">

    <table class="table caption-top table-dark table-striped">
        <thead>
            <caption class="text-dark bg-light">
                <?php
                    if($allWithdrawals){
                        echo 'Ваши заявки на вывод денежных средств ';
                        echo '<a href="' . Url::current(['allWithdrawals' => 0]) . '" class="btn btn-sm btn-warning r-0">Показать активные <i class="fas fa-eye"></i></a>';
                    }
                    else{
                        echo 'Ваши активные заявки на вывод денежных средств ';
                        echo '<a href="' . Url::current(['allWithdrawals' => 1]) . '" class="btn btn-sm btn-warning r-0">Показать все <i class="fas fa-eye"></i></a>';
                    }
                    if(isset($admin) && $admin && (!isset($_GET['showTestWithdrawals']) || $_GET['showTestWithdrawals'] == 0) ){
                        echo ' <a href="' . Url::current(['showTestWithdrawals' => 1]) . '" class="btn btn-sm btn-danger r-0 text-dark">Показать тестовые <i class="fas fa-fighter-jet"></i></a>';
                    }
                    elseif(isset($admin) && $admin && isset($_GET['showTestWithdrawals']) && $_GET['showTestWithdrawals'] == 1){
                        echo ' <a href="' . Url::current(['showTestWithdrawals' => 0]) . '" class="btn btn-sm btn-danger r-0 text-dark">Вернуть обычные <i class="fas fa-fighter-jet"></i></a>';
                    }
                ?>
            </caption>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Сумма</th>
                <th scope="col">Статус</th>
                <th scope="col">Канал</th>
                <?php 
                    if($allWithdrawals){
                        echo '<th>Комментарий</th>';
                    }
                ?>
                <th scope="col">Дата создания заявки</th>
            </tr>
        </thead>
        <tbody>
    <?php
    if(!empty($withdrawals)){
        $countArr = count($withdrawals);
        for($i = 0; $i < $countArr; $i++){
            echo '<tr>';
            echo '<th scope="row">' . $i+1 . '</th>';
            echo '<td>' . $withdrawals[$i]['count'] . ' рублей</td>';
            if($withdrawals[$i]['status'] == 0){
                echo '<td>' . 'Ожидает подтверждения с почты' . '</td>';
            }
            elseif($withdrawals[$i]['status'] == 1){
                echo '<td>' . 'Ожидает вывода денежных средств' . '</td>';
            }
            elseif($withdrawals[$i]['status'] == 2){
                echo '<td>' . 'Заявка отклонена' . '</td>';
            }
            elseif($withdrawals[$i]['status'] == 3){
                echo '<td>' . 'Выплачено' . '</td>';       
            }
            elseif($withdrawals[$i]['status'] == 4){
                echo '<td>' . 'Отменено пользователем' . '</td>';
            }
            echo '<td>' . $withdrawals[$i]['shop'] . '</td>';
            if($allWithdrawals){
                echo '<td>';
                if(!empty($withdrawals[$i]['comment'])){
                    echo '<button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#Modal' . $i . '">Показать <i class="fas fa-eye"></i></button>           
                    <div class="modal fade" id="Modal' . $i . '" tabindex="-1" aria-labelledby="ModalLabel' . $i . '" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title text-dark" id="ModalLabel' . $i . '">Комментарий записи #' . $i+1 . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body text-dark">' .
                          Html::encode($withdrawals[$i]['comment'])
                        . '</div>
                        </div>
                      </div>
                    </div>';
                }
                else{
                    echo '<span class="not-set">(не задано)</span>';
                }
                echo '</td>';

            }
            echo '<td>' . (new DateTime($withdrawals[$i]['created_time'], new DateTimeZone('Europe/Moscow')))->format('d.m.Y H:i:s') . '</td>';
            echo '</tr>';
        }
        if($withdrawalsCount > 25){
            $colspan = 5 + $allWithdrawals;
            echo '<tr><td colspan=' . $colspan . '>';
            $countButtons = ceil($withdrawalsCount / 25);
            echo '<nav aria-label="..."><ul class="pagination justify-content-center mb-0">';
            if(!isset($_GET['withdrawalsPage'])){
                $currentPage = 1;
            }
            else{
                $currentPage = $_GET['withdrawalsPage'];
            }
            $startI = 0;
            $endI = $countButtons;
            if($countButtons > 10){
                $startI = max($currentPage - 2, 1);
                $endI = min($startI + 4, $countButtons);
            }
            if($currentPage > 1){
                echo '<li class="page-item"><a class="page-link" href="' . Url::current(['withdrawalsPage' => $currentPage - 1]) . '" aria-label="<"><span aria-hidden="true">&laquo;</span></a></li>';
            }
            for($startI = 0; $i < $endI; $i++){
                if($currentPage == $i + 1){
                    echo '<li class="page-item active" aria-current="page"><span class="page-link">' . $i+1 .'</span></li>';
                }
                else{
                    echo '<li class="page-item"><a class="page-link" href="' . Url::current(['withdrawalsPage' => $i + 1]) . '">' . $i+1 . '</a></li>';
                }
            }
            if($currentPage < $countButtons){
                echo '<li class="page-item"><a class="page-link" href="' . Url::current(['withdrawalsPage' => $currentPage + 1]) . '" aria-label=">"><span aria-hidden="true">&raquo;</span></a></li>';
            }
            echo '</ul></nav>';
            echo '</td></tr>';
        }
    }
    else{
        $colspan = 5 + $allWithdrawals;
        echo '<tr><td colspan=' . $colspan . '>Ничего не найдено</td></tr>';
    }
    ?>
        </tbody>
    </table>

    <hr class="mt-4 mb-4">

    <table class="table caption-top table-dark table-striped">
        <thead>
            <caption class="text-dark bg-light">
                <?php
                    echo 'Ваши начисления ' . $accrualsCount . ' шт.';
                    if(isset($admin) && $admin && (!isset($_GET['showTestAccruals']) || $_GET['showTestAccruals'] == 0) ){
                        echo ' <a href="' . Url::current(['showTestAccruals' => 1]) . '" class="btn btn-sm btn-danger r-0 text-dark">Показать тестовые <i class="fas fa-fighter-jet"></i></a>';
                    }
                    elseif(isset($admin) && $admin && isset($_GET['showTestAccruals']) && $_GET['showTestAccruals'] == 1){
                        echo ' <a href="' . Url::current(['showTestAccruals' => 0]) . '" class="btn btn-sm btn-danger r-0 text-dark">Вернуть обычные <i class="fas fa-fighter-jet"></i></a>';
                    }
                ?>
            </caption>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Сумма</th>
                <th scope="col">Способ оплаты</th>
                <th scope="col">Канал</th>
                <th scope="col">Дата оплаты</th>
            </tr>
        </thead>
        <tbody>
    <?php
    if(!empty($accruals)){
        $countArr = count($accruals);
        for($i = 0; $i < $countArr; $i++){
            echo '<tr>';
            echo '<th scope="row">' . $i+1 . '</th>';
            echo '<td>' . $accruals[$i]['count'] . ' рублей</td>';
            echo '<td>' . $accruals[$i]['method'] . '</td>';
            echo '<td>' . $accruals[$i]['shop'] . '</td>';
            echo '<td>' . (new DateTime($accruals[$i]['created_time'], new DateTimeZone('Europe/Moscow')))->format('d.m.Y H:i:s') . '</td>';
            echo '</tr>';
        }
        if($accrualsCount > 25){
            echo '<tr><td colspan=5>';
            $countButtons = ceil($accrualsCount / 25);
            echo '<nav aria-label="..."><ul class="pagination justify-content-center mb-0">';
            if(!isset($_GET['accrualsPage'])){
                $currentPage = 1;
            }
            else{
                $currentPage = $_GET['accrualsPage'];
            }
            $startI = 0;
            $endI = $countButtons;
            if($countButtons > 10){
                $startI = max($currentPage - 2, 1);
                $endI = min($startI + 4, $countButtons);
            }
            if($currentPage > 1){
                echo '<li class="page-item"><a class="page-link" href="' . Url::current(['accrualsPage' => $currentPage - 1]) . '" aria-label="<"><span aria-hidden="true">&laquo;</span></a></li>';
            }
            for($i = $startI; $i < $endI; $i++){
                if($currentPage == $i + 1){
                    echo '<li class="page-item active" aria-current="page"><span class="page-link">' . $i+1 .'</span></li>';
                }
                else{
                    echo '<li class="page-item"><a class="page-link" href="' . Url::current(['accrualsPage' => $i + 1]) . '">' . $i+1 . '</a></li>';
                }
            }
            if($currentPage < $countButtons){
                echo '<li class="page-item"><a class="page-link" href="' . Url::current(['accrualsPage' => $currentPage + 1]) . '" aria-label=">"><span aria-hidden="true">&raquo;</span></a></li>';
            }
            echo '</ul></nav>';
            echo '</td></tr>';
        }
    }
    else{
        echo '<tr><td colspan=6>Ничего не найдено</td></tr>';
    }
    ?>
        </tbody>
    </table>

    <hr class="mt-4 mb-4">

    <div class="bg-dark text-danger text-center mb-4" style="min-height: 200px">
        Блок вывода ДС
    </div>
</div>

<script>
let link = document.getElementById('sideBarFinLink');
let button = document.getElementById('sideBarFinBtn');
link.href = '#';
button.disabled = true;
</script>