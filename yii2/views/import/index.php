<?php

/** @var yii\web\View $this */
/** @var app\models\ImportBatch[] $batches */
/** @var array $sourceTypes */

use yii\bootstrap5\Html;

$this->title = 'Импорт';
$uploadUrl = \yii\helpers\Url::to(['/import/upload']);
?>
<h1 class="h3 mb-4">Импорт данных</h1>

<div class="card mb-4">
    <div class="card-body">
        <form id="import-form">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
            <div class="mb-3">
                <label class="form-label" for="source_type">Тип источника</label>
                <select name="source_type" id="source_type" class="form-select" required>
                    <?php foreach ($sourceTypes as $value => $label): ?>
                        <option value="<?= Html::encode($value) ?>"><?= Html::encode($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="dropzone" class="mb-3">
                <p class="mb-2">Перетащите CSV или JSON сюда</p>
                <button type="button" id="upload-btn" class="btn btn-primary">Выбрать файл</button>
            </div>

            <div id="progressBox"></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Последние загрузки</div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Файл</th>
                    <th>Источник</th>
                    <th>Формат</th>
                    <th>Строк</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($batches)): ?>
                <tr><td colspan="6" class="text-muted text-center py-3">Загрузок пока нет</td></tr>
            <?php else: ?>
                <?php foreach ($batches as $batch): ?>
                <tr>
                    <td><?= $batch->id ?></td>
                    <td><?= Html::encode($batch->filename) ?></td>
                    <td><?= Html::encode($sourceTypes[$batch->source_type] ?? $batch->source_type) ?></td>
                    <td><?= Html::encode(strtoupper($batch->format)) ?></td>
                    <td><?= $batch->rows_count ?></td>
                    <td><?= date('d.m.Y H:i', $batch->created_at) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$this->registerJs("window.UPLOAD_URL = " . json_encode($uploadUrl) . ";", \yii\web\View::POS_HEAD);
?>
