<?php
/** @var yii\web\View $this */
/** @var app\components\PaymentComponent|array $payment */
$this->registerJsFile('https://telegram.org/js/telegram-web-app.js', ['position' => $this::POS_HEAD]);
$this->title = 'Страница оплаты';

// echo '<pre>';
// var_dump($payment);
// echo '</pre>';
// exit(0);

?>

<div class="payment-index col-12 col-md-6 container py-4 my-4 border border-light rounded bg-dark text-light">
    <?php
        if(!array_key_exists('error', $payment)){
            $products = json_encode($payment['products']);
            $js = <<<JS
                var products = $products;
            JS;
            $this->registerJs($js, $this::POS_HEAD);

            echo '<span class="fs-5 fw-bold text-light">Форма оплаты:</span><hr class="text-danger">';

            echo '<div id="stage1" style="display: block;">';
            echo '<label class="mb-1" for="product_id">Выберите тип подписки:</label>';
            echo yii\helpers\Html::dropDownList('product_id', null, yii\helpers\ArrayHelper::map($payment['products'], 'id', 'name'), ['id' => 'product_id', 'class' => 'form-control', 'style' => 'cursor: pointer;']);
            echo '</div>';
            echo '<div id="stage2" style="display: none;">';
            if(array_key_exists('robokassa', $payment['methods'])){
                echo '<a id="roboCardHref" href="' . yii\helpers\Url::to(['payment', 'method' => 'RoboKassa', 'shop' => $payment['shop']]) . '" target="_self">';
                echo '<button id="roboCardBtn" class="btn btn-primary col-12 mt-1" style="min-width: 180px; height: 64px; font-weight: 500;">';
                echo '<img src="' . Yii::getAlias('@web/images/card.svg') . '" alt="Банковская карта"> <span>Банковская карта</span><br>';
                echo '<span>Мир, Visa, Mastercard, Union Pay, QIWI</span>';
                echo '</button>';
                echo '</a>';
            }
            if(array_key_exists('paypall', $payment['methods'])){
                echo '<a id="payPallHref" href="' . yii\helpers\Url::to(['payment', 'method' => 'PayPall', 'shop' => $payment['shop']]) . '" target="_self">';
                echo '<button class="btn btn-primary col-12 mt-1" style="min-width: 180px; height: 64px; font-weight: 500;">';
                echo '<span>Иностранные платежи (PayPall)</span>';
                echo '</button>';
                echo '</a>';
            }
            if(array_key_exists('freekassa', $payment['methods'])){
                echo '<br>';
                echo '<a id="freeHref" href="' . yii\helpers\Url::to(['payment', 'method' => 'FreeKassa', 'shop' => $payment['shop']]) . '" target="_self">';
                echo '<button class="btn btn-primary col-12 mt-1" style="min-width: 180px; height: 64px; font-weight: 500;">';
                echo '<span>Другие способы оплаты<br>
                (crypto, ₽, $, ₴, ₸)</span>';
                echo '</button>';
                echo '</a>';
            }
            if(array_key_exists('paykassa', $payment['methods'])){
                $form = yii\widgets\ActiveForm::begin([
                    'id' => 'payCryptoHref',
                    'action' => ['payment',  'method' => 'PayKassa', 'shop' => $payment['shop']],
                    'method' => 'POST'
                ]);
                echo '<label style="font-size: 18px;font-weight: bold;">Оплата криптовалютой</label><br>';
                echo '<select class="form-select" name="pscur">';
                foreach($pk->getPaymentSystems("crypto") as $item){
                    foreach($item["currency_list"] as $currency) {
                        echo '<option value="' . sprintf("%s_%s", mb_strtolower($item["system"]), mb_strtolower($currency)) . '">';
                        echo sprintf("%s %s", $item["display_name"], $currency);
                        echo '</option>';
                    }
                };
                echo '</select>';
                echo '<button id="payCryptoBtn" type="submit" class="btn col-12 mt-1" style="font-weight: 500;">Оплатить</button>';
                yii\widgets\ActiveForm::end();
            }
            echo '</div>';

            echo '<div class="mt-2">';
            echo '<span class="fw-bold" style="font-size: 18px;">Детализация платежа</span>';
            echo '<hr class="my-0">';
            echo '<div class="col-12 text-start detail-inner" class="my-1 mx-1">';
            echo '<span style="font-size: 16px; font-weight: 500;">Магазин:</span><br>';
            echo '<span style="font-size: 16px; font-weight: 400;">' . $payment['shop'] . '</span><br>';
            echo '<span style="font-size: 14px; font-weight: 500;">Дней подписки: </span><br>';
            echo '<span style="font-weight: 400;"> <span id="payment_days">' . $payment['products'][0]['access_days'] . '</span> дней</span><br>';
            echo '<hr style="margin-right: 10px;" class="mb-0">';
            echo '<span style="font-size: 16px; font-weight: 400;">К оплате: <span id="payment_price">' . $payment['products'][0]['price'] . '</span> RUB</span><br>';
            echo '</div>';
            echo '</div>';
            echo '<button id="slideButton" onclick="slideForm();" class="btn btn-lg btn-primary col-12 mb-0 mt-2">Далее</button>';

            echo '</div>';
        }
        else{
            echo '<span class="fw-bold">Ошибка оплаты:</span><hr class="text-danger">';
            echo '<span class="text-center text-danger fw-bold">' . $payment['error'] . '</span>';
        }
    ?>
</div>

<script>
    var tg = window.Telegram?.WebApp;
    var roboCard = document.getElementById('roboCardHref');
    var roboQiwi = document.getElementById('roboQiwiHref');
    var payCrypto = document.getElementById('payCryptoHref');
    var freeHref = document.getElementById('freeHref');
    var payPallHref = document.getElementById('payPallHref');
    if(roboCard){
        roboCard.href = roboCard.href + '&webApp=' + (tg?.initDataUnsafe?.query_id || '');
        var aroboHref = roboCard.href;
    }
    if(freeHref){
        freeHref.href = freeHref.href + '&webApp=' + (tg?.initDataUnsafe?.query_id || '');
        var afreeHref = freeHref.href;
    }
    if(payCrypto){
        payCrypto.action = payCrypto.action + '&webApp=' + (tg?.initDataUnsafe?.query_id || '');
        var apayCrypto = payCrypto.action;
    }
    if(payPallHref){
        payPallHref.href = payPallHref.href + '&webApp=' + (tg?.initDataUnsafe?.query_id || '');
        var apayPallHref = payPallHref.href;
    }
</script>


<script>
    function slideForm(){
        let div1 = document.getElementById('stage1');
        let div2 = document.getElementById('stage2');
        let button = document.getElementById('slideButton');
        let dropdown = document.getElementById('product_id');

        if(div1.style.display === 'block'){
            div1.style.display = 'none';
            button.textContent = 'Назад';
            button.classList.remove('btn-primary');
            button.classList.add('btn-warning');
            div2.style.display = 'block';
            if(roboCard){
                roboCard.href = aroboHref +  '&productId=' + dropdown.value;
            }
            if(freeHref){
                freeHref.href = afreeHref + '&productId=' + dropdown.value;
            }
            if(payCrypto){
                payCrypto.action = payCrypto + '&productId=' + dropdown.value;
            }
            if(payPallHref){
                payPallHref.href = apayPallHref + '&productId=' + dropdown.value;
            }
        }
        else{
            div1.style.display = 'block';
            button.textContent = 'Далее';
            button.classList.remove('btn-warning');
            button.classList.add('btn-primary');
            div2.style.display = 'none';
            if(roboCard){
                roboCard.href = aroboHref +  '&productId=' + dropdown.value;
            }
            if(freeHref){
                freeHref.href = afreeHref + '&productId=' + dropdown.value;
            }
            if(payCrypto){
                payCrypto.action = payCrypto + '&productId=' + dropdown.value;
            }
            if(payPallHref){
                payPallHref.href = apayPallHref + '&productId=' + dropdown.value;
            }
        }
    }
</script>

<script>
    var dropdown = document.getElementById('product_id');
    var days = document.getElementById('payment_days');
    var count = document.getElementById('payment_price');
    dropdown.addEventListener('change', function() {
        var selectedValue = dropdown.value;
        days.textContent = products[dropdown.selectedIndex][`access_days`];
        count.textContent = products[dropdown.selectedIndex][`price`];
    });
</script>

<script>
    if(tg?.initDataUnsafe?.user?.id){
        tg.ready();
        tg.expand();
        tg.enableClosingConfirmation();
        tg.setHeaderColor(tg.headerColor);
    }
</script>