<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Withdrawals $model */
/** @var array $clients */
/** @var ActiveForm $form */

$this->title = 'Finance';

$allWithdrawals = false;

if(isset($_GET['allWithdrawals']) && $_GET['allWithdrawals'] == 1){
    $allWithdrawals = true;
}

$test = 123;

?>

<style>
    @media(max-width: 991px){
        #withdrawalDiv{
            border-top: 1px solid #0dcaf0;
            border-bottom: 1px solid #0dcaf0;
        }
    }
    #contentInnerDiv{
        padding-left: 0!important;
        padding-right: 0!important;
    }
</style>

<div class="lk-channels">
    <h1 class="pt-2 pb-2 pt-md-0 pb-md-1 mb-0">Ваши финансы:</h1>

    <table class="table caption-top table-dark table-striped mb-0" style="margin-top: -1px;">
        <thead>
            <caption class="text-dark bg-light border-top border-dark" style="border-bottom: 1px solid #0dcaf0!important;">Ваши каналы
            <div class="col-12"><a href="/lk/finance" class="btn btn-sm btn-warning r-0">Сбросить фильтры <i class="fas fa-filter"></i></a>
                <?php
                    if(isset($admin) && $admin && (!isset($_GET['showTestClients']) || $_GET['showTestClients'] == 0) ){
                        echo ' <a href="' . Url::current(['showTestClients' => 1]) . '" class="btn btn-sm btn-danger r-0 text-dark">Показать тестовые <i class="fas fa-fighter-jet"></i></a></div>';
                    }
                    elseif(isset($admin) && $admin && isset($_GET['showTestClients']) && $_GET['showTestClients'] == 1){
                        echo ' <a href="' . Url::current(['showTestClients' => 0]) . '" class="btn btn-sm btn-danger r-0 text-dark">Вернуть обычные <i class="fas fa-fighter-jet"></i></a></div>';
                    }
                ?>
            </caption>
            <tr class="text-center">
                <?php
                    if(isset($admin) && $admin && isset($_GET['showTestClients']) && $_GET['showTestClients'] == 1){
                        echo '<th class="text-danger fw-bold" scope="col">#</th>';
                        echo '<th class="text-danger fw-bold" scope="col">Канал</th>';
                        echo '<th class="text-danger fw-bold" scope="col">Баланс</th>';
                        echo '<th class="text-danger fw-bold" scope="col">Выведено</th>';
                        echo '<th class="text-danger fw-bold" scope="col">Заморожено</th>';
                    }
                    else{
                        echo '<th scope="col">#</th>';
                        echo '<th scope="col">Канал</th>';
                        echo '<th scope="col">Баланс</th>';
                        echo '<th scope="col">Выведено</th>';
                        echo '<th scope="col">Заморожено</th>';
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
                        echo '<tr class="text-center">';
                        echo '<th scope="row">' . $i+1 . '</th>';
                        echo '<td>' . $clients[$i]['shop'] . '</td>';
                        echo '<td class="text-nowrap">' . $clients[$i]['test_balance'] . ' ₽</td>';
                        echo '<td class="text-nowrap">' . $clients[$i]['test_total_withdrawal'] . ' ₽</td>';
                        echo '<td class="text-nowrap">' . $clients[$i]['test_blocked_balance'] . ' ₽</td>';
                        echo '</tr>';
                    }
                }
                else{
                    for($i = 0; $i < $countArr; $i++){
                        echo '<tr class="text-center">';
                        echo '<th scope="row">' . $i+1 . '</th>';
                        echo '<td>' . $clients[$i]['shop'] . '</td>';
                        echo '<td class="text-nowrap">' . $clients[$i]['balance'] . ' ₽</td>';
                        echo '<td class="text-nowrap">' . $clients[$i]['total_withdrawal'] . ' ₽</td>';
                        echo '<td class="text-nowrap">' . $clients[$i]['blocked_balance'] . ' ₽</td>';
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

    <table class="table caption-top table-dark table-striped mb-0">
        <thead>
            <caption class="text-dark bg-light border-top border-bottom border-info">
                <?php
                    if($allWithdrawals){
                        echo 'Ваши заявки на вывод денежных средств';
                        echo '<div class="col-12"><a href="' . Url::current(['allWithdrawals' => 0]) . '" class="btn btn-sm btn-warning r-0">Показать активные <i class="fas fa-eye"></i></a>';
                    }
                    else{
                        echo 'Ваши активные заявки на вывод денежных средств ';
                        echo '<div class="col-12"><a href="' . Url::current(['allWithdrawals' => 1]) . '" class="btn btn-sm btn-warning r-0">Показать все <i class="fas fa-eye"></i></a>';
                    }
                    if(isset($admin) && $admin && (!isset($_GET['showTestWithdrawals']) || $_GET['showTestWithdrawals'] == 0) ){
                        echo ' <a href="' . Url::current(['showTestWithdrawals' => 1]) . '" class="btn btn-sm btn-danger r-0 text-dark">Показать тестовые <i class="fas fa-fighter-jet"></i></a></div>';
                    }
                    elseif(isset($admin) && $admin && isset($_GET['showTestWithdrawals']) && $_GET['showTestWithdrawals'] == 1){
                        echo ' <a href="' . Url::current(['showTestWithdrawals' => 0]) . '" class="btn btn-sm btn-danger r-0 text-dark">Вернуть обычные <i class="fas fa-fighter-jet"></i></a></div>';
                    }
                ?>
            </caption>
            <tr class="text-center text-md-start">
                <th scope="col">#</th>
                <th scope="col">Канал</th>
                <th scope="col">Сумма</th>
                <th scope="col">Статус</th>
                <th scope="col">Дата</th>
            </tr>
        </thead>
        <tbody>
    <?php
    if(!empty($withdrawals)){
        $countArr = count($withdrawals);
        for($i = 0; $i < $countArr; $i++){
            echo '<tr class="text-center text-md-start">';
            echo '<th scope="row">' . $i+1 . '</th>';
            echo '<td>' . $withdrawals[$i]['shop'] . '</td>';
            echo '<td class="text-nowrap">' . $withdrawals[$i]['count'] . ' ₽</td>';
            if($withdrawals[$i]['status'] == 0){
                echo '<td>' . 'Ожидает подтверждения с почты' . '</td>';
            }
            elseif($withdrawals[$i]['status'] == 1){
                echo '<td>' . 'Ожидает вывода денежных средств' . '</td>';
            }
            elseif($withdrawals[$i]['status'] == 2){
                echo '<td>' . 'Заявка отклонена';
                if($allWithdrawals){
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
                }
                echo '</td>';
            }
            elseif($withdrawals[$i]['status'] == 3){
                echo '<td>' . 'Выплачено' . '</td>';       
            }
            elseif($withdrawals[$i]['status'] == 4){
                echo '<td>' . 'Отменено пользователем' . '</td>';
            }
            echo '<td>' . (new DateTime($withdrawals[$i]['created_time'], new DateTimeZone('Europe/Moscow')))->format('d.m.Y H:i:s') . '</td>';
            echo '</tr>';
        }
        if($withdrawalsCount > 25){
            echo '<tr><td colspan=5>';
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
        echo '<tr><td colspan=5>Ничего не найдено</td></tr>';
    }
    ?>
        </tbody>
    </table>

    <table class="table caption-top table-dark table-striped mb-0">
        <thead>
            <caption class="text-dark bg-light border-top border-bottom border-info">
                <?php
                    echo 'Ваши начисления ' . $accrualsCount . ' шт.';
                    if(isset($admin) && $admin && (!isset($_GET['showTestAccruals']) || $_GET['showTestAccruals'] == 0) ){
                        echo '<div class="col-12"><a href="' . Url::current(['showTestAccruals' => 1]) . '" class="btn btn-sm btn-danger r-0 text-dark">Показать тестовые <i class="fas fa-fighter-jet"></i></a></div>';
                    }
                    elseif(isset($admin) && $admin && isset($_GET['showTestAccruals']) && $_GET['showTestAccruals'] == 1){
                        echo '<div class="col-12"><a href="' . Url::current(['showTestAccruals' => 0]) . '" class="btn btn-sm btn-danger r-0 text-dark">Вернуть обычные <i class="fas fa-fighter-jet"></i></a></div>';
                    }
                ?>
            </caption>
            <tr class="text-center text-md-start">
                <th scope="col">#</th>
                <th scope="col">Сумма</th>
                <th scope="col">Способ оплаты</th>
                <th scope="col">Канал</th>
                <th scope="col">Дата</th>
            </tr>
        </thead>
        <tbody>
    <?php
    if(!empty($accruals)){
        $countArr = count($accruals);
        for($i = 0; $i < $countArr; $i++){
            echo '<tr class="text-center text-md-start">';
            echo '<th scope="row">' . $i+1 . '</th>';
            echo '<td class="text-nowrap">' . $accruals[$i]['count'] . ' ₽</td>';
            echo '<td>' . $accruals[$i]['method'] . '</td>';
            echo '<td>' . $accruals[$i]['shop'] . '</td>';
            echo '<td>' . (new DateTime($accruals[$i]['created_time'], new DateTimeZone('Europe/Moscow')))->format('d.m.Y H:i:s') . '</td>';
            echo '</tr>';
        }
        if($accrualsCount > 25){
            echo '<tr><td colspan=5>';
            if($countArr > 25){
                $countButtons = ceil($accrualsCount / $countArr);
                $_GET['accrualsFirstPageCount'] = $countArr;
            }
            else{
                if(isset($_GET['accrualsFirstPageCount']) && $_GET['accrualsFirstPageCount'] > 25){
                    $countButtons = ceil($accrualsCount / $_GET['accrualsFirstPageCount']);
                }
                else{
                    $countButtons = ceil($accrualsCount / 25);
                }
            }
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

    <div id="withdrawalDiv" class="col-12 col-lg-6 offset-lg-3 my-4 bg-light text-dark rounded p-2">
        <?php
            if(!empty($clients)){
                $countArr = count($clients);
                $js = <<<JS
                let minWithdrawal = new Array();
                JS;
                $this->registerJs($js);
                for($i = 0; $i < $countArr; $i++){
                    if(($clients[$i]['balance'] - $clients[$i]['blocked_balance']) > $clients[$i]['min_count_withdrawal']){
                        $shop[$i] = $clients[$i]['shop'];
                        $minWithdrawal = $clients[$i]['min_count_withdrawal'];
                        $js = <<<JS
                        minWithdrawal[$i] = $minWithdrawal;
                        JS;
                        $this->registerJs($js);
                    }
                }
                $js = <<<JS
                $('#shop-select').change(async function(){
                    const selectedShop = $(this).val();
                    if(!isNaN(selectedShop)){
                        $('#min-count').attr('min', minWithdrawal[selectedShop]);
                    }
                });
                JS;
                $this->registerJs($js);
                if(!empty($shop)){
                    $form = ActiveForm::begin([
                        'id' => 'withdrawals-form',
                        'method' => 'post',
                        'options' => [
                            'autocomplete' => 'off',
                            'class' => 'form-horizontal'
                        ],
                    ]);
                    echo '<legend>Вывод денежных средств</legend>';
                    echo $form->field($model, 'shop')->dropDownList($shop, ['class' => 'form-control', 'id' => 'shop-select', 'prompt' => 'Выберите канал'])->label('Название канала');
                    echo $form->field($model, 'count')->input('number', ['min' => 0, 'max' => 100000, 'id' => 'min-count', 'placeholder' => 'Введите сумму']);
                    echo $form->field($model, 'card_number')->textInput([
                        'pattern' => '[0-9]{16}',
                        'placeholder' => 'Введите номер банковской карты',
                    ]);
                    echo Html::hiddenInput('csrf', $csrf);
                    echo Html::submitButton('Отправить', ['class' => 'btn btn-dark']);
                    ActiveForm::end();
                }
            }
        ?>
    </div>

</div>

<script>
let link = document.getElementById('sideBarFinLink');
let button = document.getElementById('sideBarFinBtn');
link.href = '#';
button.disabled = true;
</script>