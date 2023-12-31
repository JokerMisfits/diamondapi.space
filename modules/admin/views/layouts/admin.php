<?php
/** @var yii\web\View $this */
/** @var string $content */

app\assets\AppAsset::register($this);
$this->registerCssFile('@web/css/site.css', ['position' => $this::POS_HEAD]);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Yii::getAlias('@web/images/favicon.png')]);
$name = Yii::$app->name;
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language; ?>" class="h-100">
    <head>
        <title><?= yii\bootstrap5\Html::encode($this->title); ?></title>
        <?php $this->head(); ?>
    </head>
    <body class="d-flex flex-column h-100">
    <?php $this->beginBody(); ?>

    <header id="header">
        <?php
            yii\bootstrap5\NavBar::begin([
                'brandLabel' => '<span style="padding-left: 1.5rem;">' . $name . '</span>',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => ['class' => 'navbar-expand-md navbar-dark fixed-top bg-dark row']
            ]);
            echo yii\bootstrap5\Nav::widget([
                'options' => ['class' => 'navbar-nav col-md-9 m-0 p-0 text-center'],
                'items' => [
                    ['label' => 'Платежи', 'url' => ['/admin/order/index']],
                    ['label' => 'Клиенты', 'url' => ['/admin/client/index']],
                    ['label' => 'Пользователи', 'url' => ['/admin/tg-member/index']],
                    ['label' => 'Заявки', 'url' => ['/admin/withdrawal/index']],
                    ['label' => 'Сверка', 'url' => ['/admin/revise/index']],
                    ['label' => 'Товары', 'url' => ['/admin/product/index']]
                ]
            ]);
            echo yii\bootstrap5\Nav::widget([
                'options' => ['class' => 'navbar-nav d-flex justify-content-md-end col-md-3 text-center'],
                'items' => [
                    Yii::$app->user->isGuest
                        ? ['label' => 'Войти', 'url' => ['/login']]
                        : '<li class="nav-item">'
                        . yii\bootstrap5\Html::beginForm(['/logout'])
                        . yii\bootstrap5\Html::submitButton((strlen(Yii::$app->user->identity->username) > 5) 
                        ? 'Выйти<span class="d-none d-lg-inline">(' . Yii::$app->user->identity->username . ')</span>'
                        : 'Выйти(' . Yii::$app->user->identity->username . ')',
                            ['class' => 'nav-link btn btn-link logout text-center']
                        )
                        . yii\bootstrap5\Html::endForm()
                        . '</li>'
                ]
            ]);
            yii\bootstrap5\NavBar::end();
        ?>
    </header>

    <main id="main" class="flex-shrink-0" role="main" style="margin-top: 56px;">
            <?php if (!empty($this->params['breadcrumbs'])): ?>
                <?= yii\bootstrap5\Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]); ?>
            <?php endif ?>
            <?= app\widgets\Alert::widget(); ?>
            <?= $content; ?>
    </main>

    <footer id="footer" class="mt-auto py-3 bg-light">
        <div class="container">
            <div class="row text-dark">
                <div class="col-12 text-center">&copy; <?= date('Y') . ' Copyright: ' . $name; ?></div>
            </div>
        </div>
    </footer>

    <?php $this->endBody(); ?>
    </body>
</html>
<?php $this->endPage(); ?>