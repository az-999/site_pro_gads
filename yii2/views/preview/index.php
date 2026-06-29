<?php

/** @var yii\web\View $this */
/** @var array $preview */

use yii\bootstrap5\Html;

$this->title = 'Превью кампании';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Превью кампании Google Ads</h1>
    <?= Html::a('Скачать CSV', ['/export/download'], ['class' => 'btn btn-success']) ?>
</div>

<?php if (empty($preview)): ?>
    <div class="alert alert-info">
        Нет готовых ключевых слов. Загрузите данные и запустите «Обработать всё» на дашборде.
    </div>
<?php else: ?>
    <?php foreach ($preview as $group): ?>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <span>Язык: <strong><?= Html::encode(strtoupper($group['language'])) ?></strong></span>
            <span class="badge bg-primary"><?= $group['count'] ?> ключевых слов</span>
        </div>
        <div class="card-body">
            <dl class="row mb-3">
                <dt class="col-sm-3">Группа объявлений</dt>
                <dd class="col-sm-9"><?= Html::encode($group['ad_group']) ?></dd>
                <dt class="col-sm-3">Final URL</dt>
                <dd class="col-sm-9"><?= Html::encode($group['final_url']) ?></dd>
                <dt class="col-sm-3">Заголовок 1</dt>
                <dd class="col-sm-9"><?= Html::encode($group['headline1']) ?></dd>
                <dt class="col-sm-3">Заголовок 2</dt>
                <dd class="col-sm-9"><?= Html::encode($group['headline2']) ?></dd>
                <dt class="col-sm-3">Описание</dt>
                <dd class="col-sm-9"><?= Html::encode($group['description']) ?></dd>
            </dl>
            <h6>Примеры ключевых слов</h6>
            <div class="d-flex flex-wrap gap-1">
                <?php foreach ($group['keywords'] as $kw): ?>
                    <span class="badge bg-light text-dark border"><?= Html::encode($kw) ?></span>
                <?php endforeach; ?>
                <?php if ($group['count'] > count($group['keywords'])): ?>
                    <span class="badge bg-secondary">+<?= $group['count'] - count($group['keywords']) ?> ещё</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
