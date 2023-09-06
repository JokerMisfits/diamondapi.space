<?php
/** @var yii\web\View $this */
/** @var Paykassa\PaykassaSCI $pk */
$this->context->layout = 'telegram';
$this->title = 'Страница оплаты ' . $params['shop'];
if(isset($params['pk'])){
    $pk = $params['pk'];
}
?>

<div class="payment-index">
<?php
    if(isset($config['robokassa'])){
        echo '<a id="roboCardHref" href="' . yii\helpers\Url::to(['/payment/route', 'method' => 'RoboKassa', 'csrf' => $csrf, 'shop' => $params['shop'], 'count' => $params['count'], 'name' => urldecode($params['name']),
        'userId' => $params['userId'], 'days' => $params['days'], 'hash' => $params['hash']]) . '" target="_self">';
        echo '<button id="roboCardBtn" class="btn col-12" style="min-width: 180px; height: 64px; font-weight: 500;">';
        echo '<img src="' . Yii::getAlias('@web/images/card.svg') . '" alt="Банковская карта"> <span>Банковская карта</span><br>';
        echo '<span>Мир, Visa, Mastercard, Union Pay, QIWI</span>';
        echo '</button>';
        echo '</a>';
    }
    if(isset($config['paypall'])){
        echo '<a id="payPallHref" href="' . yii\helpers\Url::to(['/payment/route', 'method' => 'PayPall', 'csrf' => $csrf, 'shop' => $params['shop'], 'count' => $params['count'], 'name' => urldecode($params['name']),
        'userId' => $params['userId'], 'days' => $params['days'], 'hash' => $params['hash']]) . '" target="_self">';
        echo '<button class="btn col-12" style="min-width: 180px; height: 64px; font-weight: 500;">';
        echo '<span>Иностранные платежи (PayPall)</span>';
        echo '</button>';
        echo '</a>';
    }
    if(isset($config['freekassa'])){
        echo '<br>';
        echo '<a id="freeHref" href="' . yii\helpers\Url::to(['/payment/route', 'method' => 'FreeKassa', 'csrf' => $csrf, 'shop' => $params['shop'], 'count' => $params['count'], 'name' => urldecode($params['name']),
        'userId' => $params['userId'], 'days' => $params['days'], 'hash' => $params['hash']]) . '" target="_self">';
        echo '<button class="btn col-12" style="min-width: 180px; height: 64px; font-weight: 500;">';
        echo '<span>Другие способы оплаты<br>
        (crypto, ₽, $, ₴, ₸)</span>';
        echo '</button>';
        echo '</a>';
    }
    if(isset($pk)){
        $form = yii\widgets\ActiveForm::begin([
            'id' => 'payCryptoHref',
            'action' => ['payment/route',  'method' => 'PayKassa', 'csrf' => $csrf, 'shop' => $params['shop'], 'count' => $params['count'], 'name' => urldecode($params['name']), 'userId' => $params['userId'], 'days' => $params['days'], 'hash' => $params['hash']],
            'method' => 'post',
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
?>
        <div style="margin-top: 10px;">
            <span style="font-size: 18px; font-weight: 600">Детализация платежа</span>
            <hr style="margin-top: 0;">
            <div class="col-12 text-start detail-inner" style="margin: -10px 5px 5px 5px">
                <span style="font-size: 16px; font-weight: 500;">Магазин:</span><br>
                <span style="font-size: 16px; font-weight: 400;"><?= $params['shop']; ?></span><br>
                <span style="font-size: 16px; font-weight: 500;">Состав заказа:</span><br>
                <span style="font-size: 16px; font-weight: 400;"><?= $params['name']; ?></span><br>
                <span style="font-size: 14px; font-weight: 400;">Цена позиции: </span>
                <span style="font-weight: 450;"><?= $params['count'] . ',00 ₽'; ?></span><br>
                <span style="font-size: 14px; font-weight: 400;">Количество: </span>
                <span style="font-weight: 450;">x1</span>
                <hr style="margin-right: 10px;" class="mb-0">
                <span style="font-size: 16px; font-weight: 400;">К оплате: <?= $params['count'] . ',00 ₽'; ?></span><br>
            </div>
        </div>
    </div>
</div>

<script>
    const tg = window.Telegram.WebApp;
    let roboCard = document.getElementById('roboCardHref');
    let roboQiwi = document.getElementById('roboQiwiHref');
    let payCrypto = document.getElementById('payCryptoHref');
    let freeHref = document.getElementById('freeHref');
    let payPallHref = document.getElementById('payPallHref');
    if(roboCard){
        roboCard.href = roboCard.href + '&webApp=' + (tg.initDataUnsafe?.query_id || '');
    }
    if(roboQiwi){
        roboQiwi.href = roboQiwi.href + '&webApp=' + (tg.initDataUnsafe?.query_id || '');
    }
    if(freeHref){
        freeHref.href = freeHref.href + '&webApp=' + (tg.initDataUnsafe?.query_id || '');
    }
    if(payCrypto){
        payCrypto.action = payCrypto.action + '&webApp=' + (tg.initDataUnsafe?.query_id || '');
    }
    if(payPallHref){
        payPallHref.href = payPallHref.href + '&webApp=' + (tg.initDataUnsafe?.query_id || '');
    }
    if(tg.initDataUnsafe?.user?.id) {
        tg.ready();
        tg.expand();
        tg.enableClosingConfirmation();
        tg.setHeaderColor(tg.headerColor);
    }
    else{
        document.getElementById('body').innerText = '403 Forbidden';
        document.title = '403 Forbidden';
    }
    function setThemeClass() {
        document.documentElement.className = tg.colorScheme;
    }
    tg.onEvent('themeChanged', setThemeClass);
    setThemeClass();
</script>