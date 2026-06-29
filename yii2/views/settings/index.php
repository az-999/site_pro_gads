<?php

/** @var yii\web\View $this */
/** @var int|string $minVolume */
/** @var string $brands */
/** @var app\models\ForbiddenKeyword[] $forbidden */

use yii\bootstrap5\Html;

$this->title = 'Настройки';

$forbiddenText = implode("\n", array_map(fn($f) => $f->text, $forbidden));
?>
<h1 class="h3 mb-4">Настройки очистки</h1>

<div class="card">
    <div class="card-body">
        <?= Html::beginForm(['/settings/save'], 'post') ?>
        <div class="mb-3">
            <label class="form-label" for="min_volume">Минимальная частота (volume)</label>
            <input type="number" name="min_volume" id="min_volume" class="form-control" value="<?= Html::encode($minVolume) ?>" min="0">
            <div class="form-text">Ключевые слова с volume ниже порога будут отклонены (low_volume).</div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="brand_keywords">Бренды (по одному на строку)</label>
            <textarea name="brand_keywords" id="brand_keywords" class="form-control" rows="6"><?= Html::encode($brands) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label" for="forbidden_keywords">Запрещённые ключевые слова</label>
            <textarea name="forbidden_keywords" id="forbidden_keywords" class="form-control" rows="4"><?= Html::encode($forbiddenText) ?></textarea>
        </div>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <?= Html::endForm() ?>
    </div>
</div>
