<?php
/** @var yii\web\View $this */
/** @var app\models\Clients|array $model */
$this->title = 'Каналы';
?>

<style>
    .lk-channels{
        font-family: sans-serif;
        font-weight: 500;
    }
    @media(max-width: 767px){
        #channelsInfo{
            border-bottom-left-radius: 0!important;
            border-left: 0!important;
            padding-left: 0!important;
        }
        #addChannelBtn{
            border-radius: 0!important;
        }
        #addBotBtn{
            border-radius: 0!important;
        }
    }
    @media(max-width: 414px){
        #channelsInfo{
            border: 0!important;
            border-radius: 0!important;
            padding: 0!important;
            margin-top: 0!important;
        }
    }


</style>

<h1 class="border-bottom border-dark mt-0 mb-4" style="padding-bottom: 3px;">Ваши каналы:</h1>

<div class="lk-channels">
    <?php
        if(!empty($model)){
            $countOfArr = count($model);
            for($i = 0; $i < $countOfArr; $i++){
                if(isset($model[$i]['bot_token'])){
                    echo '<div id="channelsInfo" class="table-responsive">';
                    echo '<table class="table caption-top table-borderless col-12">';
                    echo '<caption class="fs-3 text-dark border border-2 border-info border-bottom-0 mb-0 rounded-top" style="border-bottom: 2px solid #0d6efd!important;">Канал: ' . $model[$i]['shop'] . '</caption>';
                    echo '<thead>';
                    echo '<tr class="text-nowrap fs-4 fw-bold text-center border-bottom">';
                    echo '<th class="border-end" colspan="2" style="border-left: 2px solid #0d6efd!important;">Платежные системы:</th>';
                    echo '<th class="border-end" colspan="2">Статистика канала:</th>';
                    echo '<th class="border-end" colspan="2">Статистика чата:</th>';
                    echo '<th colspan="2" style="border-right: 2px solid #0d6efd!important;">Статистика приватного чата:</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    if(!isset($model[$i]['robokassa'])){
                        echo '<tr class="text-nowrap fs-5 mb-1 mx-0" style="border-left: 2px solid #0d6efd;">';
                        echo '<td>RoboKassa:</td>';
                        echo '<td class="border-end"><a class="btn btn-sm btn-primary" style="width: 140px;">Подключить</a></td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td style="border-right: 2px solid #0d6efd!important;">Значение</td>';
                        echo '</tr>';
                    }
                    else{
                        echo '<tr class="text-nowrap fs-5 mb-1 mx-0" style="border-left: 2px solid #0d6efd;">';
                        echo '<td>RoboKassa:</td>';
                        echo '<td class="text-success border-end">подключена <i class="fas fa-check"></i></td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td style="border-right: 2px solid #0d6efd!important;">Значение</td>';
                        echo '</tr>';
                    }
                    if(!isset($model[$i]['paykassa'])){
                        echo '<tr class="text-nowrap fs-5 mb-1 mx-0" style="border-left: 2px solid #0d6efd;">';
                        echo '<td>PayKassa:</td>';
                        echo '<td class="border-end"><a class="btn btn-sm btn-primary" style="width: 140px;">Подключить</a></td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td style="border-right: 2px solid #0d6efd!important;">Значение</td>';
                        echo '</tr>';                   
                    }
                    else{
                        echo '<tr class="text-nowrap fs-5 mb-1 mx-0" style="border-left: 2px solid #0d6efd;">';
                        echo '<td>PayKassa:</td>';
                        echo '<td class="text-success border-end">подключена <i class="fas fa-check"></i></td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td style="border-right: 2px solid #0d6efd!important;">Значение</td>';
                        echo '</tr>';
                    }
                    if(!isset($model[$i]['freekassa'])){
                        echo '<tr class="text-nowrap fs-5 mb-1 mx-0" style="border-left: 2px solid #0d6efd;">';
                        echo '<td>FreeKassa:</td>';
                        echo '<td class="border-end"><a class="btn btn-sm btn-primary" style="width: 140px;">Подключить</a></td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td style="border-right: 2px solid #0d6efd!important;">Значение</td>';
                        echo '</tr>';
                    }
                    else{
                        echo '<tr class="text-nowrap fs-5 mb-1 mx-0" style="border-left: 2px solid #0d6efd;">';
                        echo '<td>FreeKassa:</td>';
                        echo '<td class="text-success border-end">подключена <i class="fas fa-check"></i></td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td style="border-right: 2px solid #0d6efd!important;">Значение</td>';
                        echo '</tr>';
                    }
                    if(!isset($model[$i]['paypall'])){
                        echo '<tr class="text-nowrap fs-5 mb-1 mx-0" style="border-left: 2px solid #0d6efd; border-bottom: 2px solid #0d6efd;">';
                        echo '<td>PayPall:</td>';
                        echo '<td class="border-end"><a class="btn btn-sm btn-primary" style="width: 140px;">Подключить</a></td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td style="border-right: 2px solid #0d6efd!important;">Значение</td>';
                        echo '</tr>';                   
                    }
                    else{
                        echo '<tr class="text-nowrap fs-5 mb-1 mx-0" style="border-left: 2px solid #0d6efd; border-bottom: 2px solid #0d6efd;">';
                        echo '<td>PayPall:</td>';
                        echo '<td class="text-success border-end">подключена <i class="fas fa-check"></i></td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td class="border-end">Значение</td>';
                        echo '<td>Показатель</td>';
                        echo '<td style="border-right: 2px solid #0d6efd!important;">Значение</td>';
                        echo '</tr>';
                    }
                }
                else{
                    echo '<div class="col-12 col-md-8 col-lg-4 my-0 pt-1 fs-3 text-dark border border-2 border-info border-bottom-0 rounded-top ">Канал: ' . $model[$i]['shop'] . '</div>';
                    echo '<a href="/lk/options" id="addBotBtn" class="btn btn-primary col-12 col-md-8 col-lg-4 mb-4" style="border-top-left-radius: 0!important; border-top-right-radius: 0!important">Подключить Бота</a>';
                }
                $balance = $model[$i]['balance'] - $model[$i]['blocked_balance'];
                if($balance >= $model[$i]['min_count_withdrawal']){
                    echo '<tr class="bg-light border border-2 border-info border-top-0">';
                    echo '<td colspan=8 class="fs-5 text-dark">Баланс: ' . $balance . ' ₽ <a href="/lk/finance" class="btn btn-sm btn-warning" target="_self">Подробнее <i class="fas fa-external-link-alt"></i></a></td>';
                    echo '</tr>';
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                }
                elseif($balance < $model[$i]['min_count_withdrawal'] && isset($model[$i]['bot_token'])){
                    echo '<tr class="bg-light border border-2 border-info border-top-0">';
                    echo '<td colspan=8 class="fs-5 text-dark">Баланс: ' . $balance . ' ₽</td>';
                    echo '</tr>';
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                }
            }
        }
        echo '<div class="col-12 border-top border-dark"><a href="/lk/options" id="addChannelBtn" class="btn btn-dark my-4 col-12 col-md-8 offset-md-2 col-lg-4 offset-lg-4">Добавить канал</a></div>';
    ?>

</div>

<script>
let link = document.getElementById('sideBarChannelLink');
let button = document.getElementById('sideBarChannelBtn');
link.href = '#';
button.disabled = true;
</script>