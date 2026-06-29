<?php

/** @var yii\web\View $this */
/** @var int $readyCount */

use yii\bootstrap5\Html;

$this->title = 'Экспорт';
?>
<h1 class="h3 mb-4">Экспорт в Google Ads</h1>

<div class="card">
    <div class="card-body">
        <p>Готово к экспорту: <strong><?= $readyCount ?></strong> ключевых слов.</p>
        <p class="text-muted">
            Файл в формате Google Ads Editor CSV: кампания, группа объявлений, ключевое слово,
            тип соответствия, финальный URL, заголовки и описание.
        </p>
        <?php if ($readyCount > 0): ?>
            <?= Html::a('Скачать Google Ads CSV', ['/export/download'], ['class' => 'btn btn-success btn-lg']) ?>
        <?php else: ?>
            <button class="btn btn-secondary btn-lg" disabled>Нет данных для экспорта</button>
        <?php endif; ?>
        <div class="mt-3">
            <?= Html::a('Превью кампании', ['/preview/index'], ['class' => 'btn btn-outline-primary']) ?>
        </div>
    </div>
</div>
