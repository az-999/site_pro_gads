<?php

/** @var yii\web\View $this */
/** @var array $counts */

use yii\bootstrap5\Html;

$this->title = 'Дашборд';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Дашборд</h1>
    <?= Html::beginForm(['/pipeline/run'], 'post') ?>
    <?= Html::submitButton('Обработать всё', ['class' => 'btn btn-success']) ?>
    <?= Html::endForm() ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-stat">
            <div class="card-body">
                <div class="text-muted small">Всего</div>
                <div class="h3 mb-0"><?= $counts['total'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat success">
            <div class="card-body">
                <div class="text-muted small">Готово к экспорту</div>
                <div class="h3 mb-0"><?= $counts['ready'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat">
            <div class="card-body">
                <div class="text-muted small">Чистые</div>
                <div class="h3 mb-0"><?= $counts['clean'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat danger">
            <div class="card-body">
                <div class="text-muted small">Отклонённые</div>
                <div class="h3 mb-0"><?= $counts['rejected'] ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Новые (raw)</div>
                <div class="h4 mb-0"><?= $counts['raw'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Уже в Google Ads</div>
                <div class="h4 mb-0"><?= $counts['used'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Запрещённые</div>
                <div class="h4 mb-0"><?= $counts['forbidden'] ?></div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <p class="text-muted">
        Загрузите CSV/JSON на странице <?= Html::a('Импорт', ['/import/index']) ?>,
        затем нажмите «Обработать всё» для очистки и подготовки ключевых слов.
    </p>
</div>
