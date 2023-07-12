<?php

/** @var yii\web\View $this */
/** @var string $content */

use yii\web\View;
use app\widgets\Alert;
use yii\bootstrap5\Nav;
use yii\bootstrap5\Html;
use app\assets\AppAsset;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
$this->registerCssFile('@web/css/site.css', ['position' => View::POS_HEAD]);
$this->registerCssFile('@web/css/lk.css', ['position' => View::POS_HEAD]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', [
    'crossorigin' => 'anonymous',
    'position' => View::POS_HEAD,
]);
$this->registerJsFile('@web/js/lk.js', ['position' => View::POS_HEAD]);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/images/favicon.png')]);
$name = Yii::$app->name;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language; ?>" class="h-100">
    <head>
        <title><?= Html::encode($this->title); ?></title>
        <?php $this->head() ?>
    </head>
    <body class="d-flex flex-column h-100">
    <?php $this->beginBody(); ?>

    <style>
        @media(max-width: 898px){
            #sideBarIcon{
                margin-left: 20px;
                margin-right: 20px;
            }
        }
    </style>

    <header id="header">
        <?php
            NavBar::begin([
                'brandLabel' => '<span style="padding-left: 1.5rem;">' . $name . '</span>',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => ['class' => 'navbar-expand-md navbar-dark fixed-top bg-dark row']
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav col-md-6 m-0 p-0 text-center'],
                'items' => [
                    ['label' => 'Личный кабинет', 'url' => ['/lk']]
                ]
            ]);
            if(isset(Yii::$app->user->identity->tg_member_id)){
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav d-md-none m-0 p-0 text-center'],
                    'items' => [
                        ['label' => 'Главная', 'url' => ['/lk/index'], 'class' => ['d-block', 'd-md-none']],
                        ['label' => 'Каналы', 'url' => ['/lk/channels'], 'class' => 'd-block d-md-none'],
                        ['label' => 'Платежи', 'url' => ['/lk/payments'], 'class' => 'd-block d-md-none'],
                        ['label' => 'Подписки', 'url' => ['/lk/subscriptions'], 'class' => 'd-block d-md-none'],
                        ['label' => 'Финансы', 'url' => ['/lk/finance'], 'class' => 'd-block d-md-none'],
                        ['label' => 'Настройки', 'url' => ['/lk/options'], 'class' => 'd-block d-md-none']
                    ]
                ]);
            }
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav d-flex justify-content-md-end col-md-6 text-center'],
                'items' => [
                    Yii::$app->user->isGuest
                        ? ['label' => 'Войти', 'url' => ['/login']]
                        : '<li class="nav-item">'
                        . Html::beginForm(['/logout'])
                        . Html::submitButton((strlen(Yii::$app->user->identity->username) > 5) 
                        ? 'Выйти<span class="d-none d-lg-inline">(' . Yii::$app->user->identity->username . ')</span>'
                        : 'Выйти(' . Yii::$app->user->identity->username . ')',
                            ['class' => 'nav-link btn btn-link logout text-center']
                        )
                        . Html::endForm()
                        . '</li>'
                ]
            ]);
            NavBar::end();
        ?>
    </header>

    <main id="main" class="flex-shrink-0" role="main">
        <?php
            if(isset(Yii::$app->user->identity->tg_member_id)){
                echo '<div class="col-2 d-none d-md-block fixed-top border-end border-dark overflow-auto;" style="height: calc(100vh - 56px); margin-top: 56px;">';
                echo '<div class="btn-group-vertical col-12">';
                echo '<a id="sideBarProfileLink" href="/lk/index" class="col-12"><button id="sideBarProfileBtn" class="btn-lk col-12 border-bottom p-2 mb-0 font-monospace"><i id="sideBarIcon" class="fas fa-id-card-alt"></i> Главная</button></a>';
                echo '<a id="sideBarChannelLink" href="/lk/channels" class="col-12"><button id="sideBarChannelBtn" class="btn-lk col-12 border-bottom p-2 mb-0 font-monospace"><i id="sideBarIcon" class="fas fa-comment-dots"></i> Каналы</button></a>';
                echo '<a id="sideBarPayLink" href="/lk/payments" class="col-12"><button id="sideBarPayBtn" class="btn-lk col-12 border-bottom p-2 mb-0 font-monospace"><i id="sideBarIcon" class="fas fa-comment-dollar"></i> Платежи</button></a>';
                echo '<a id="sideBarSubLink" href="/lk/subscriptions" class="col-12"><button id="sideBarSubBtn" class="btn-lk col-12 border-bottom p-2 mb-0 font-monospace"><i id="sideBarIcon" class="fas fa-users"></i> Подписки</button></a>';
                echo '<a id="sideBarFinLink" href="/lk/finance" class="col-12"><button id="sideBarFinBtn" class="btn-lk col-12 border-bottom p-2 mb-0 font-monospace"><i id="sideBarIcon" class="fas fa-wallet"></i> Финансы</button></a>';
                echo '<a id="sideBarOptionLink" href="/lk/options" class="col-12"><button id="sideBarOptionBtn" class="btn-lk col-12 border-bottom p-2 mb-0 font-monospace"><i id="sideBarIcon" class="fas fa-sliders-h"></i> Настройки</button></a>';
                echo '</div>';
                echo '</div>';
                echo '<div id="contentDiv" class="col-12 col-md-10 offset-md-2 p-0">';
            }
            else{
                echo '<div id="contentDiv" class="col-12 p-0">';
            }
        ?>
            <div id="contentInnerDiv" class="px-0 px-md-1" style="min-height: calc(100vh - 113px); margin-top: 56px;">
                <?= Alert::widget(); ?>
                <?= $content; ?>
            </div>
            
            <footer id="footer" class="mt-auto py-3 bg-light border-top">
                <div class="container">
                    <div class="row text-dark">
                        <div class="col-12 text-dark text-center">&copy; <?= date('Y') . ' Copyright: ' . $name ?></div>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>