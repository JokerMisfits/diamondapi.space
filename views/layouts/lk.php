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
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.png')]);
$name = Yii::$app->name;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language; ?>" class="h-100">
    <head>
        <title><?= Html::encode($this->title); ?></title>
        <?php $this->head() ?>
    </head>
    <body class="d-flex flex-column h-100 container-fluid">
    <?php $this->beginBody(); ?>

    <header id="header">
        <?php
            NavBar::begin([
                'brandLabel' => '<span style="padding-left: 1.5rem;">' . $name . '</span>',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top row']
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav text-center col-md-9 m-0 p-0'],
                'items' => [
                    ['label' => 'Личный кабинет', 'url' => ['/lk']],
                    // ['label' => 'Главная', 'url' => ['/index']],
                    // ['label' => 'Главная', 'url' => ['/index']],
                    // ['label' => 'Главная', 'url' => ['/index']]
                ]
            ]);

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav text-center col-md-3 d-flex justify-content-md-end'],
                'items' => [
                    Yii::$app->user->isGuest
                        ? ['label' => 'Войти', 'url' => ['/login']]
                        : '<li class="nav-item">'
                            . Html::beginForm(['/logout'])
                            . Html::submitButton((strlen(Yii::$app->user->identity->username) > 5) 
                            ? 'Выйти<span class="d-none d-lg-inline">(' . Yii::$app->user->identity->username . ')</span>'
                            : 'Выйти(' . Yii::$app->user->identity->username . ')',
                                ['class' => 'nav-link btn btn-link logout']
                            )
                            . Html::endForm()
                            . '</li>'
                ]
            ]);
            NavBar::end();

        ?>
    </header>

    <main id="main" class="flex-shrink-0" role="main">
        <button class="btn btn-sm btn-light d-block d-md-none"
            style="border-radius: 0; z-index: 2; position: fixed; top: 56px; left: 0;
            writing-mode: vertical-rl; padding: 0!important; justify-content: center; text-orientation: upright; height: calc(100vh - 56px); opacity: 1;">
            <i class="fas fa-chevron-right fa-lg" style="margin: -6px!important; padding: -12px!important;"></i>
        </button>

        <div class="col-2 d-none d-md-block fixed-top" style="background-color: #fff; height: calc(100vh - 56px); margin-top: 56px; padding: 0; border-right:1px solid #212529;">
            <div class="btn-group-vertical col-12">
                <a id="sideBarProfileLink" href="/lk/index" class="col-12"><button id="sideBarProfileBtn" class="btn-lk col-12 border-bottom border-primary p-2 mb-0"><i class="fas fa-id-card-alt"></i> Профиль</button></a>
                <a id="sideBarChannelLink" href="/lk/channels" class="col-12"><button id="sideBarChannelBtn" class="btn-lk col-12 border-bottom border-primary p-2 mb-0"><i class="fas fa-comment-dots"></i> Каналы и чаты</button></a>
                <a id="sideBarPayLink" href="/lk/payments" class="col-12"><button id="sideBarPayBtn" class="btn-lk col-12 border-bottom border-primary p-2 mb-0"><i class="fas fa-comment-dollar"></i> Платежи</button></a>
                <a id="sideBarSubLink" href="/lk/subscriptions" class="col-12"><button id="sideBarSubBtn" class="btn-lk col-12 border-bottom border-primary p-2 mb-0"><i class="fas fa-users"></i> Подписки</button></a>
                <a id="sideBarFinLink" href="/lk/finance" class="col-12"><button id="sideBarFinBtn" class="btn-lk col-12 border-bottom border-primary p-2 mb-0"><i class="fas fa-wallet"></i> Финансы</button></a>
                <a id="sideBarOptionLink" href="/lk/options" class="col-12"><button id="sideBarOptionBtn" class="btn-lk col-12 border-bottom border-primary p-2 mb-0"><i class="fas fa-sliders-h"></i> Настройки</button></a>
            </div>
        </div>

        <div id="contentDiv" class="offset-md-2 col-12 col-md-10">
            <div style="min-height: calc(100vh - 112px); margin: 56px 0 0 0">
                <?= Alert::widget(); ?>
                <?= $content; ?>
            </div>
            
            <footer id="footer" class="mt-auto py-3 bg-light row">
                <div class="container">
                    <div class="row text-dark">
                        <div class="col-12 text-center">&copy; <?= date('Y') . ' Copyright: ' . $name ?></div>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>