<?php

/** @var yii\web\View $this */
/** @var array $stats */

use yii\bootstrap5\Html;

$this->title = 'Сброс';
?>
<h1 class="h3 mb-4">Сброс тестовых данных</h1>

<div class="card border-danger">
    <div class="card-header bg-danger text-white">Очистка импорта</div>
    <div class="card-body">
        <p>Удаляются все ключевые слова и история загрузок. Остаются:</p>
        <ul>
            <li>пользователь и вход</li>
            <li>настройки очистки (min volume, бренды)</li>
            <li>запрещённые ключевые слова</li>
        </ul>

        <dl class="row mb-4">
            <dt class="col-sm-4">Ключевых слов в БД</dt>
            <dd class="col-sm-8"><strong><?= $stats['keywords'] ?></strong></dd>
            <dt class="col-sm-4">Загрузок (batches)</dt>
            <dd class="col-sm-8"><strong><?= $stats['batches'] ?></strong></dd>
        </dl>

        <?php if ($stats['keywords'] > 0 || $stats['batches'] > 0): ?>
            <?= Html::beginForm(['/reset/run'], 'post', ['class' => 'border rounded p-3 bg-light']) ?>
            <div class="mb-3">
                <label class="form-label" for="confirm">
                    Для подтверждения введите <code>RESET</code>
                </label>
                <input type="text" name="confirm" id="confirm" class="form-control" autocomplete="off" placeholder="RESET" required>
            </div>
            <?= Html::submitButton('Очистить базу', ['class' => 'btn btn-danger']) ?>
            <?= Html::endForm() ?>
        <?php else: ?>
            <div class="alert alert-info mb-0">База уже пуста — можно загружать мок-файлы на странице <?= Html::a('Импорт', ['/import/index']) ?>.</div>
        <?php endif; ?>
    </div>
</div>
