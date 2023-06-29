<?php

/** @var yii\web\View $this */

$this->context->layout = 'main';
$this->title = 'Главная страница';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">Congratulations!</h1>

        <p class="lead">You have successfully created your Yii-powered application.</p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4 mb-3 text-center">
                <h2>TEST PAYMENT</h2>
                <p><a class="btn btn-dark col-12" href=<?php echo '/payment?shop=club-dimitriev&count=1950&name=Доступ%20в%20Димитриев-Клуб%20Продвинутый&userId=230509432&days=90&hash=a91a6ad4c37174835282b21a95e58d29' .
                '&csrf=' . Yii::$app->session->get('csrf') .
                 '#tgWebAppData=query_id%3DAAF4S70NAAAAAHhLvQ1dJaHX%26user%3D%257B%2522id%2522%253A230509432%252C%2522first_name%2522%253A%2522.%2522%252C%2522last_name%2522%253A%2522%2522%252C%2522username%2522%253A%2522Xo_Diamond_XO%2522%252C%2522language_code%2522%253A%2522ru%2522%257D%26auth_date%3D1686961483%26hash%3D3eadfdea41ca11d7512f4005d806be62a1e14528598345d877aaafc0a2bebc1e&tgWebAppVersion=6.7&tgWebAppPlatform=tdesktop&tgWebAppThemeParams=%7B%22bg_color%22%3A%22%23282e33%22%2C%22button_color%22%3A%22%233fc1b0%22%2C%22button_text_color%22%3A%22%23ffffff%22%2C%22hint_color%22%3A%22%2382868a%22%2C%22link_color%22%3A%22%234be1c3%22%2C%22secondary_bg_color%22%3A%22%23313b43%22%2C%22text_color%22%3A%22%23f5f5f5%22%7D'?> >
                перейти &raquo;
                </a></p>
                <p></p>
            </div>
            <div class="col-lg-4 mb-3 text-center">
                <h2>TEST Админка</h2>
                <p><a class="btn btn-primary col-12" href="/admin/order">перейти &raquo;</a></p>
            </div>
            <div class="col-lg-4 text-center">
                <h2>Test disput</h2>
                <p><a class="btn btn-danger col-12" href="/payment/disput">перейти &raquo;</a></p>
            </div>
        </div>

    </div>
</div>