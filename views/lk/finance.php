<?php
/** @var yii\web\View $this */
/** @var app\models\Withdrawals $model */
/** @var array $clients */
/** @var yii\widgets\ActiveForm $form */
$this->title = 'Финансы';
$allWithdrawals = false;
if(array_key_exists('allWithdrawals', $_GET) && $_GET['allWithdrawals'] == 1){
    $allWithdrawals = true;
}
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
    <h1 class="pt-2 pb-2 pt-md-0 pb-md-1">Ваши финансы:</h1>

    <div class="table-responsive">
        <table class="table caption-top table-dark table-striped mb-0" style="margin-top: -1px;">
            <thead>
                <caption class="text-dark bg-light border-top border-dark" style="border-bottom: 1px solid #0dcaf0!important;">Ваши каналы
                <div class="col-12"><a href="/lk/finance" class="btn btn-sm btn-warning r-0">Сбросить фильтры <i class="fas fa-filter"></i></a>
                    <?php
                        if(isset($admin) && $admin && (!isset($_GET['showTestClients']) || $_GET['showTestClients'] == 0) ){
                            echo ' <a href="' . yii\helpers\Url::current(['showTestClients' => 1]) . '" class="btn btn-sm btn-danger r-0 text-dark">Показать тестовые <i class="fas fa-fighter-jet"></i></a></div>';
                        }
                        elseif(isset($admin) && $admin && isset($_GET['showTestClients']) && $_GET['showTestClients'] == 1){
                            echo ' <a href="' . yii\helpers\Url::current(['showTestClients' => 0]) . '" class="btn btn-sm btn-danger r-0 text-dark">Вернуть обычные <i class="fas fa-fighter-jet"></i></a></div>';
                        }
                    ?>
                </caption>
                <tr>
                    <?php
                        if(isset($admin) && $admin && isset($_GET['showTestClients']) && $_GET['showTestClients'] == 1){
                            echo '<th class="text-nowrap text-start text-danger fw-bold" scope="col">#</th>';
                            echo '<th class="text-nowrap text-center text-md-start text-danger fw-bold" scope="col">Канал</th>';
                            echo '<th class="text-nowrap text-center text-md-start text-danger fw-bold" scope="col">Баланс</th>';
                            echo '<th class="text-nowrap text-center text-md-start text-danger fw-bold" scope="col">Выведено</th>';
                            echo '<th class="text-nowrap text-center text-md-start text-danger fw-bold" scope="col">Ожидает вывода</th>';
                        }
                        else{
                            echo '<th class="text-nowrap text-start" scope="col">#</th>';
                            echo '<th class="text-nowrap text-center text-md-start" scope="col">Канал</th>';
                            echo '<th class="text-nowrap text-center text-md-start" scope="col">Баланс</th>';
                            echo '<th class="text-nowrap text-center text-md-start" scope="col">Выведено</th>';
                            echo '<th class="text-nowrap text-center text-md-start" scope="col">Ожидает вывода</th>';
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
                            echo '<th class="text-nowrap text-start" scope="row">' . $i+1 . '</th>';
                            echo '<td class="text-nowrap text-center text-md-start">' . $clients[$i]['shop'] . '</td>';
                            echo '<td class="text-nowrap text-center text-md-start">' . $clients[$i]['test_balance'] . ' ₽</td>';
                            echo '<td class="text-nowrap text-center text-md-start">' . $clients[$i]['test_total_withdrawal'] . ' ₽</td>';
                            echo '<td class="text-nowrap text-center text-md-start">' . $clients[$i]['test_blocked_balance'] . ' ₽</td>';
                            echo '</tr>';
                        }
                    }
                    else{
                        for($i = 0; $i < $countArr; $i++){
                            echo '<tr>';
                            echo '<th class="text-nowrap text-start" scope="row">' . $i+1 . '</th>';
                            echo '<td class="text-nowrap text-center text-md-start">' . $clients[$i]['shop'] . '</td>';
                            echo '<td class="text-nowrap text-center text-md-start">' . $clients[$i]['balance'] . ' ₽</td>';
                            echo '<td class="text-nowrap text-center text-md-start">' . $clients[$i]['total_withdrawal'] . ' ₽</td>';
                            echo '<td class="text-nowrap text-center text-md-start">' . $clients[$i]['blocked_balance'] . ' ₽</td>';
                            echo '</tr>';
                        }
                    }
                    if($clientsCount > 5){
                        echo '<tr><td colspan=5>';
                        $countButtons = $clientsCount / 5;
                        echo '<nav aria-label="..."><ul class="pagination justify-content-start justify-content-sm-center mb-0">';
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
                            echo '<li class="page-item"><a class="page-link" href="' . yii\helpers\Url::current(['clientsPage' => $currentPage - 1]) . '" aria-label="<"><span aria-hidden="true">&laquo;</span></a></li>';
                        }
                        for($i = 0; $i < $countButtons; $i++){
                            if($currentPage == $i + 1){
                                echo '<li class="page-item active" aria-current="page"><span class="page-link">' . $i+1 .'</span></li>';
                            }
                            else{
                                echo '<li class="page-item"><a class="page-link" href="' . yii\helpers\Url::current(['clientsPage' => $i + 1]) . '">' . $i+1 . '</a></li>';
                            }
                        }
                        if($currentPage < $countButtons){
                            echo '<li class="page-item"><a class="page-link" href="' . yii\helpers\Url::current(['clientsPage' => $currentPage + 1]) . '" aria-label=">"><span aria-hidden="true">&raquo;</span></a></li>';
                        }
                        echo '</ul></nav>';
                        echo '</td></tr>';
                    }
                }
                else{
                    echo '<tr><td class="text-nowrap" colspan=5>Ничего не найдено</td></tr>';
                }
            ?>
            </tbody>
        </table>
    </div>

    <div class="table-responsive">
        <table class="table caption-top table-dark table-striped mb-0">
            <thead>
                <caption class="text-dark bg-light border-top border-bottom border-info">
                    <?php
                        if($allWithdrawals){
                            echo 'Ваши заявки на вывод денежных средств';
                            echo '<div class="col-12"><a href="' . yii\helpers\Url::current(['allWithdrawals' => 0]) . '" class="btn btn-sm btn-warning r-0">Показать активные <i class="fas fa-eye"></i></a>';
                        }
                        else{
                            echo 'Ваши активные заявки на вывод денежных средств ';
                            echo '<div class="col-12"><a href="' . yii\helpers\Url::current(['allWithdrawals' => 1]) . '" class="btn btn-sm btn-warning r-0">Показать все <i class="fas fa-eye"></i></a>';
                        }
                        if(isset($admin) && $admin && (!isset($_GET['showTestWithdrawals']) || $_GET['showTestWithdrawals'] == 0) ){
                            echo ' <a href="' . yii\helpers\Url::current(['showTestWithdrawals' => 1]) . '" class="btn btn-sm btn-danger r-0 text-dark">Показать тестовые <i class="fas fa-fighter-jet"></i></a></div>';
                        }
                        elseif(isset($admin) && $admin && isset($_GET['showTestWithdrawals']) && $_GET['showTestWithdrawals'] == 1){
                            echo ' <a href="' . yii\helpers\Url::current(['showTestWithdrawals' => 0]) . '" class="btn btn-sm btn-danger r-0 text-dark">Вернуть обычные <i class="fas fa-fighter-jet"></i></a></div>';
                        }
                    ?>
                </caption>
                <tr>
                    <th class="text-nowrap text-start" scope="col">#</th>
                    <th class="text-nowrap text-center text-md-start" scope="col">Канал</th>
                    <th class="text-nowrap text-center text-md-start" scope="col">Сумма</th>
                    <th class="text-nowrap text-center text-md-start" scope="col">Статус</th>
                    <th class="text-nowrap text-center text-md-start" scope="col">Дата создания заявки</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(!empty($withdrawals)){
                $countArr = count($withdrawals);
                for($i = 0; $i < $countArr; $i++){
                    echo '<tr>';
                    echo '<th class="text-nowrap text-start" scope="row">' . $i+1 . '</th>';
                    echo '<td class="text-nowrap text-center text-md-start">' . $withdrawals[$i]['shop'] . '</td>';
                    echo '<td class="text-nowrap text-center text-md-start">' . $withdrawals[$i]['count'] . ' ₽</td>';
                    if($withdrawals[$i]['status'] == 0){
                        echo '<td class="text-nowrap text-center text-md-start">' . 'Ожидает подтверждения с почты' . '</td>';
                    }
                    elseif($withdrawals[$i]['status'] == 1){
                        echo '<td class="text-nowrap text-center text-md-start">' . 'Ожидает вывода денежных средств' . '</td>';
                    }
                    elseif($withdrawals[$i]['status'] == 2){
                        echo '<td class="text-nowrap text-center text-md-start">' . 'Заявка отклонена';
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
                                        yii\helpers\Html::encode($withdrawals[$i]['comment'])
                                        . '</div>
                                        </div>
                                    </div>
                                </div>';
                            }
                        }
                        echo '</td>';
                    }
                    elseif($withdrawals[$i]['status'] == 3){
                        echo '<td class="text-nowrap text-center text-md-start">' . 'Выплачено' . '</td>';       
                    }
                    elseif($withdrawals[$i]['status'] == 4){
                        echo '<td class="text-nowrap text-center text-md-start">' . 'Отменено пользователем' . '</td>';
                    }
                    echo '<td>' . (new DateTime($withdrawals[$i]['created_time'], new DateTimeZone('Europe/Moscow')))->format('d.m.Y H:i:s') . '</td>';
                    echo '</tr>';
                }
                if($withdrawalsCount > 25){
                    echo '<tr><td class="text-nowrap" colspan=5>';
                    $countButtons = $withdrawalsCount / 25;
                    echo '<nav aria-label="..."><ul class="pagination justify-content-start justify-content-sm-center mb-0">';
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
                        echo '<li class="page-item"><a class="page-link" href="' . yii\helpers\Url::current(['withdrawalsPage' => $currentPage - 1]) . '" aria-label="<"><span aria-hidden="true">&laquo;</span></a></li>';
                    }
                    for($startI = 0; $i < $endI; $i++){
                        if($currentPage == $i + 1){
                            echo '<li class="page-item active" aria-current="page"><span class="page-link">' . $i+1 .'</span></li>';
                        }
                        else{
                            echo '<li class="page-item"><a class="page-link" href="' . yii\helpers\Url::current(['withdrawalsPage' => $i + 1]) . '">' . $i+1 . '</a></li>';
                        }
                    }
                    if($currentPage < $countButtons){
                        echo '<li class="page-item"><a class="page-link" href="' . yii\helpers\Url::current(['withdrawalsPage' => $currentPage + 1]) . '" aria-label=">"><span aria-hidden="true">&raquo;</span></a></li>';
                    }
                    echo '</ul></nav>';
                    echo '</td></tr>';
                }
            }
            else{
                echo '<tr><td class="text-nowrap" colspan=5>Ничего не найдено</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>

    <div class="table-responsive">
        <table class="table caption-top table-dark table-striped mb-0">
            <thead>
                <caption class="text-dark bg-light border-top border-bottom border-info">
                    <?php
                        echo 'Ваши начисления ' . $accrualsCount . ' шт.';
                        if(isset($admin) && $admin && (!isset($_GET['showTestAccruals']) || $_GET['showTestAccruals'] == 0) ){
                            echo '<div class="col-12"><a href="' . yii\helpers\Url::current(['showTestAccruals' => 1]) . '" class="btn btn-sm btn-danger r-0 text-dark">Показать тестовые <i class="fas fa-fighter-jet"></i></a></div>';
                        }
                        elseif(isset($admin) && $admin && isset($_GET['showTestAccruals']) && $_GET['showTestAccruals'] == 1){
                            echo '<div class="col-12"><a href="' . yii\helpers\Url::current(['showTestAccruals' => 0]) . '" class="btn btn-sm btn-danger r-0 text-dark">Вернуть обычные <i class="fas fa-fighter-jet"></i></a></div>';
                        }
                    ?>
                </caption>
                <tr>
                    <th class="text-nowrap text-start" scope="col">#</th>
                    <th class="text-nowrap text-center text-md-start" scope="col">Канал</th>
                    <th class="text-nowrap text-center text-md-start" scope="col">Сумма</th>
                    <th class="text-nowrap text-center text-md-start" scope="col">Способ оплаты</th>
                    <th class="text-nowrap text-center text-md-start" scope="col">Дата платежа</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(!empty($accruals)){
                $countArr = count($accruals);
                for($i = 0; $i < $countArr; $i++){
                    echo '<tr>';
                    echo '<th class="text-nowrap text-start" scope="row">' . $i+1 . '</th>';
                    echo '<td class="text-nowrap text-center text-md-start">' . $accruals[$i]['shop'] . '</td>';
                    echo '<td class="text-nowrap text-center text-md-start">' . $accruals[$i]['count'] . ' ₽</td>';
                    echo '<td class="text-nowrap text-center text-md-start">' . $accruals[$i]['method'] . '</td>';
                    echo '<td>' . (new DateTime($accruals[$i]['created_time'], new DateTimeZone('Europe/Moscow')))->format('d.m.Y H:i:s') . '</td>';
                    echo '</tr>';
                }
                if($accrualsCount > 25){
                    echo '<tr><td class="text-nowrap" colspan=5>';
                    echo '<nav aria-label="..."><ul class="pagination justify-content-start justify-content-sm-center mb-0">';
                    if(!isset($_GET['accrualsPage'])){
                        $currentPage = 1;
                    }
                    else{
                        $currentPage = $_GET['accrualsPage'];
                    }
                    $startI = 0;
                    $countButtons = $accrualsCount / 25;
                    $endI = $countButtons;
                    if($endI > 10){
                        $startI = max($currentPage - 2, 1);
                        $endI = min($startI + 4, $countButtons);
                    }
                    if($currentPage > 1){
                        echo '<li class="page-item"><a class="page-link" href="' . yii\helpers\Url::current(['accrualsPage' => $currentPage - 1]) . '" aria-label="<"><span aria-hidden="true">&laquo;</span></a></li>';
                    }
                    for($i = $startI; $i < $endI; $i++){
                        if($currentPage == $i + 1){
                            echo '<li class="page-item active" aria-current="page"><span class="page-link">' . $i+1 .'</span></li>';
                        }
                        else{
                            echo '<li class="page-item"><a class="page-link" href="' . yii\helpers\Url::current(['accrualsPage' => $i + 1]) . '">' . $i+1 . '</a></li>';
                        }
                    }
                    if($currentPage < $countButtons){
                        echo '<li class="page-item"><a class="page-link" href="' . yii\helpers\Url::current(['accrualsPage' => $currentPage + 1]) . '" aria-label=">"><span aria-hidden="true">&raquo;</span></a></li>';
                    }
                    echo '</ul></nav>';
                    echo '</td></tr>';
                }
            }
            else{
                echo '<tr><td class="text-nowrap" colspan=5>Ничего не найдено</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>

    <?php
        if(Yii::$app->authManager->checkAccess(Yii::$app->user->identity->id, 'email-verify')){
            if(!empty($clients)){
                echo '<div id="withdrawalDiv" class="col-12 col-lg-6 offset-lg-3 my-4 bg-light text-dark rounded p-2">';
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
                if(!empty($shop)){
                    $form = yii\widgets\ActiveForm::begin([
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
                    
                    $js = <<<JS
                    $('#shop-select').change(async function(){
                        const selectedShop = $(this).val();
                        if(!isNaN(selectedShop)){
                            $('#min-count').attr('min', minWithdrawal[selectedShop]);
                        }
                    });
                    JS;
                    $this->registerJs($js);

                    echo $form->field($model, 'card_number')->textInput([
                        'pattern' => '[0-9]{16}',
                        'placeholder' => 'Введите номер банковской карты',
                    ]);
                    echo yii\helpers\Html::hiddenInput('csrf', $csrf);
                    echo yii\helpers\Html::submitButton('Отправить', ['class' => 'btn btn-dark']);
                    yii\widgets\ActiveForm::end();
                }
                echo '</div>';
            }
        }
        else{
            echo '<div class="text-dark text-center my-4 p-2 bg-light rounded col-12 col-lg-8 offset-lg-2 border">';
            echo '<legend>Для получения доступа к выводу ДС</legend>' . '<br>' . 'Необходимо привязать email к вашему аккаунту.';
            echo yii\helpers\Html::beginForm(['/lk/verify'], 'post');
            echo yii\helpers\Html::hiddenInput('target', 'email');
            echo yii\helpers\Html::hiddenInput('csrf', $csrf);
            echo yii\helpers\Html::submitButton('Приступить <i class="far fa-envelope"></i>', ['class' => 'btn btn-primary col-12 col-md-8 col-lg-6 mt-2 mb-2']);
            echo yii\helpers\Html::endForm();
            echo '</div>';
        }
    ?>

<script>
    let link = document.getElementById('sideBarFinLink');
    let button = document.getElementById('sideBarFinBtn');
    link.href = '#';
    button.disabled = true;
</script>