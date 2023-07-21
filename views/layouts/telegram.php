<?php
/** @var yii\web\View $this */
/** @var string $content */

use yii\web\View;
use app\widgets\Alert;
use yii\bootstrap5\Html;
use app\assets\AppAsset;

AppAsset::register($this);
$this->registerJsFile('https://telegram.org/js/telegram-web-app.js', ['position' => View::POS_HEAD]);
$this->registerCssFile('@web/css/telegram.css', ['position' => View::POS_HEAD]);
$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Yii::getAlias('@web/images/favicon.png')]);
$name = Yii::$app->name;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body id="body" class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<main id="telegram" class="flex-shrink-0 col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3" role="telegram">
    <div class="container">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">&copy; <?= date('Y') . ' Copyright: ' . $name ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
