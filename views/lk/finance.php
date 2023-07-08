<?php

use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'Finance';

$allWithdrawals = false;
if(isset($_GET['allWithdrawals']) && $_GET['allWithdrawals'] == 1){
    $allWithdrawals = true;
}

?>

<div class="lk-channels">
    <h1 class="mt-2" style="margin-bottom: -20px;">Ваши финансы:</h1><hr class="mb-2">

    <div class="col-12 text-danger text-center bg-dark" style="min-height: 200px">
        БЛОК ВЫВОДА БАЛАНСА
    </div>

    <hr class="mt-4 mb-4">

    <table class="table caption-top table-dark table-striped">
        <thead>
        <caption class="text-dark bg-light">
            <?php
                if($allWithdrawals){
                    echo 'Ваши заявки на вывод денежных средств';
                }
                else{
                    echo 'Ваши активные заявки на вывод денежных средств';
                }
            ?>
            <a href="/lk/finance?allWithdrawals=1" class="btn btn-sm btn-warning r-0">Показать все <i class="fas fa-eye"></i></a></caption>
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
            $countButtons = $withdrawalsCount / 25;
            echo '<nav aria-label="..."><ul class="pagination justify-content-center">';
            if(!isset($_GET['withdrawalsPage'])){
                $currentPage = 1;
            }
            else{
                $currentPage = $_GET['withdrawalsPage'];
            }
            for($i = 0; $i < $countButtons; $i++){
                if($currentPage == $i + 1){
                    echo '<li class="page-item active" aria-current="page"><span class="page-link">' . $i+1 .'</span></li>';
                }
                else{
                    echo '<li class="page-item"><a class="page-link" href="/lk/finance?withdrawalsPage=' . $i+1 . '">' . $i+1 . '</a></li>';
                }
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

    <div class="col-12 text-danger text-center bg-dark" style="min-height: 200px">
        БЛОК ВЫВОДА НАЧИСЛЕНИЙ
    </div>

</div>

<script>
let link = document.getElementById('sideBarFinLink');
let button = document.getElementById('sideBarFinBtn');
link.href = '#';
button.disabled = true;
</script>