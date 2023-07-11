<?php
/** @var yii\web\View $this */
/** @var app\models\Clients|array $model */

$this->title = 'Channels';

?>

<style>
    h1{
        padding-bottom: 3px;
    }
    .lk-channels{
        font-family: sans-serif;
        font-weight: 500;
    }
    @media(max-width: 767px){
        #channelsHeader{
            border-top-left-radius: 0!important;
            border-left: 0!important;
            padding-left: 0!important;
        }
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
        #channelsHeader{
            border: none!important;
            padding: 0!important;
        }
        #channelsInfo{
            border: 0!important;
            border-radius: 0!important;
            border-bottom: 2px solid #0d6efd!important;
            padding: 0!important;
            margin-top: 0!important;
        }
    }


</style>

<h1 class="border-bottom border-dark mt-0 mb-4">Ваши каналы:</h1>

<div class="lk-channels">
    <?php
        if(isset($model[0]['shop'])){
            $countOfArr = count($model);
            for($i = 0; $i < $countOfArr; $i++){
                echo '<span id="channelsHeader" class="fs-3 border border-2 border-info border-bottom-0 mb-0 rounded-top p-2">Канал: ' . $model[$i]['shop'] . '</span>';
                if(isset($model[$i]['bot_token'])){
                    echo '<div id="channelsInfo" class="border border-2 border-primary rounded p-2" style="border-top-left-radius: 0!important; margin-top: 2px;">';
                    echo '<span class="fw-bold border-bottom border-info fs-4">Платежные системы: </span><br>';
                    if(!isset($model[$i]['robokassa'])){
                        echo '<span class="fs-5">RoboKassa: </span><a class="btn btn-sm btn-primary mb-1" style="width: 120px">Подключить</a><span class="row"></span>';
                    }
                    else{
                        echo '<span class="fs-5 mb-1">Robokassa:<span class="text-success"> подключена <i class="fas fa-check"></i></span></span><span class="row"></span>';
                    }
                    if(!isset($model[$i]['paykassa'])){
                        echo '<span class="fs-5" style="margin-right: 14px;">PayKassa: </span><a class="btn btn-sm btn-primary mb-1" style="width: 120px">Подключить</a><span class="row"></span>';                       
                    }
                    else{
                        echo '<span class="fs-5">PayKassa:<span class="text-success"><span style="margin-right: 10px;"></span> подключена <i class="fas fa-check"></i></span></span><span class="row"></span>';
                    }
                    if(!isset($model[$i]['freekassa'])){
                        echo '<span class="fs-5" style="margin-right: 8px;">FreeKassa: </span><a class="btn btn-sm btn-primary mb-1" style="width: 120px">Подключить</a><span class="row"></span>';                        
                    }
                    else{
                        echo '<span class="fs-5">FreeKassa:<span class="text-success"><span style="margin-right: 3px;"></span> подключена <i class="fas fa-check"></i></span></span><span class="row"></span>';
                    }
                    if(!isset($model[$i]['paypall'])){
                        echo '<span class="fs-5" style="margin-right: 37px;">PayPall: </span><a class="btn btn-sm btn-primary mb-1" style="width: 120px">Подключить</a><span class="row"></span>';                        
                    }
                    else{
                        echo '<span class="fs-5">PayPall:<span class="text-success"><span style="margin-right: 32px;"></span> подключена <i class="fas fa-check"></i></span></span>';
                    }
                    echo '</div>';
                }
                else{
                    echo '<br><a href="/lk/options" id="addBotBtn" class="btn btn-primary col-12 col-md-8 col-lg-4 mb-2 rounded" style="border-top-left-radius: 0!important;">Подключить Бота</a>';
                }
                
                $balance = $model[$i]['balance'] - $model[$i]['blocked_balance'];
                if($balance >= $model[$i]['min_count_withdrawal']){
                    echo '<div class="py-2 mb-4 px-md-2 border-bottom border-dark"><span class="fw-bold">Баланс: ' . $balance . ' ₽</span>' . ' <a href="/lk/finance" class="btn btn-sm btn-warning" target="_self">Подробнее <i class="fas fa-external-link-alt"></i></a></div>';
                }
                elseif($balance < $model[$i]['min_count_withdrawal'] && isset($model[$i]['bot_token'])){
                    echo '<div class="py-2 mb-4 px-md-2 border-bottom border-dark"><span class="fw-bold">Баланс: ' . $balance . ' ₽</span></div>';
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