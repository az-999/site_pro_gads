<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title ? $this->title . ' — Site.pro GAds' : 'Site.pro GAds') ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?php if (!Yii::$app->user->isGuest): ?>
<?php NavBar::begin([
    'brandLabel' => 'Site.pro GAds',
    'brandUrl' => Yii::$app->homeUrl,
    'options' => ['class' => 'navbar-expand-lg navbar-dark bg-primary mb-4'],
]); ?>
<?= Nav::widget([
    'options' => ['class' => 'navbar-nav me-auto'],
    'items' => [
        ['label' => 'Дашборд', 'url' => ['/site/index']],
        ['label' => 'Импорт', 'url' => ['/import/index']],
        ['label' => 'Ключевые слова', 'url' => ['/keyword/index']],
        ['label' => 'Настройки', 'url' => ['/settings/index']],
        ['label' => 'Превью', 'url' => ['/preview/index']],
        ['label' => 'Экспорт', 'url' => ['/export/index']],
    ],
]) ?>
<?= Nav::widget([
    'options' => ['class' => 'navbar-nav ms-auto align-items-center'],
    'items' => [
        '<li class="nav-item"><span class="navbar-text text-white-50 me-3">' . Html::encode(Yii::$app->user->identity->email) . '</span></li>',
        '<li class="nav-item">'
            . Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline'])
            . Html::submitButton('Выход', ['class' => 'btn btn-link nav-link py-0 text-white-50'])
            . Html::endForm()
            . '</li>',
    ],
    'encodeLabels' => false,
]) ?>
<?php NavBar::end(); ?>
<?php endif; ?>

<div class="container pb-5">
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show">
            <?= Html::encode($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>

    <?= $content ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
