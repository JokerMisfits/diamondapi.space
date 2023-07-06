<?php
/** @var yii\web\View $this */

$this->title = 'Channels';

?>

<style>
    .lk-channels{
        font-family: sans-serif;
        font-weight: 500;
    }
</style>

<div class="lk-channels">
    <h1 class="mt-2" style="margin-bottom: -20px;">Ваши каналы:</h1><hr class="mb-4">

    <?php
        if(isset($model[0]['shop'])){
            $countOfArr = count($model);
            for($i = 0; $i < $countOfArr; $i++){
                echo '<span class="fs-3 border border-2 border-info border-bottom-0 mb-0 rounded-top p-2">Канал: ' . $model[$i]['shop'] . '</span>';
                if(isset($model[$i]['bot_token'])){
                    echo '<div class="border border-2 border-primary rounded p-2" style="border-top-left-radius: 0!important; margin-top: 2px;">';
                    echo '<span class="fw-bold border-bottom border-info fs-4">Платежные системы: </span><br>';
                    if(!isset($model[$i]['robokassa'])){
                        echo '<span class="fs-5">RoboKassa: </span><a class="btn btn-sm btn-primary mb-1">Подключить</a>';
                    }
                    else{
                        echo '<span class="fs-5 mb-1">Robokassa:<span class="text-success"> подключена <i class="fas fa-check"></i></span></span><span class="row"></span>';
                    }
                    if(!isset($model[$i]['paykassa'])){
                        echo '<span class="fs-5" style="margin-right: 13px;">PayKassa: </span><a class="btn btn-sm btn-primary mb-1">Подключить</a><span class="row"></span>';                       
                    }
                    else{
                        echo '<span class="fs-5">PayKassa:<span class="text-success"><span style="margin-right: 10px;"></span> подключена <i class="fas fa-check"></i></span></span><span class="row"></span>';
                    }
                    if(!isset($model[$i]['freekassa'])){
                        echo '<span class="fs-5" style="margin-right: 6px;">FreeKassa: </span><a class="btn btn-sm btn-primary mb-1">Подключить</a><span class="row"></span>';                        
                    }
                    else{
                        echo '<span class="fs-5">FreeKassa:<span class="text-success"><span style="margin-right: 3px;"></span> подключена <i class="fas fa-check"></i></span></span><span class="row"></span>';
                    }
                    if(!isset($model[$i]['paypall'])){
                        echo '<span class="fs-5" style="margin-right: 34px;">PayPall: </span><a class="btn btn-sm btn-primary mb-1">Подключить</a><span class="row"></span>';                        
                    }
                    else{
                        echo '<span class="fs-5">PayPall:<span class="text-success"><span style="margin-right: 32px;"></span> подключена <i class="fas fa-check"></i></span></span><span class="row"></span>';
                    }
                    echo '</div>';
                }
                else{
                    echo '<br><a href="/lk/options" class="btn btn-primary col-12 col-md-8 col-lg-4 mb-3" style="border-top-left-radius: 0;">Подключить Бота</a>';
                }
                echo '<br>';
                $balance = $model[$i]['balance'] - $model[$i]['blocked_balance'];
                if($balance >= $model[$i]['min_count_withdrawal']){
                    echo '<span class="fw-bold">Баланс: ' . $balance . ' рублей.</span>' . ' <a href="/lk/finance" class="btn btn-sm btn-warning" target="_self">Подробнее <i class="fas fa-external-link-alt"></i></a>';
                }
                else{
                    echo '<span class="fw-bold">Баланс: ' . $balance . ' рублей.</span>';
                }
                echo '<hr class="mt-1 mb-4">';
            }
        }
        echo '<a href="/lk/options" class="btn btn-lg btn-secondary mb-4">Добавить канал</a>';
    ?>

</div>

<script>
let link = document.getElementById('sideBarChannelLink');
let button = document.getElementById('sideBarChannelBtn');
link.href = '#';
button.disabled = true;
</script>