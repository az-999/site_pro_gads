<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $sourceLabels */
/** @var array $statusLabels */
/** @var array $filters */

use app\models\Keyword;
use yii\bootstrap5\Html;
use yii\grid\GridView;

$this->title = 'Ключевые слова';
?>
<h1 class="h3 mb-4">Ключевые слова</h1>

<div class="card mb-3">
    <div class="card-body">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Источник</label>
                <select name="source" class="form-select">
                    <option value="">Все</option>
                    <?php foreach ($sourceLabels as $k => $v): ?>
                        <option value="<?= $k ?>" <?= $filters['source'] === $k ? 'selected' : '' ?>><?= Html::encode($v) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="">Все</option>
                    <?php foreach ($statusLabels as $k => $v): ?>
                        <option value="<?= $k ?>" <?= $filters['status'] === $k ? 'selected' : '' ?>><?= Html::encode($v) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Язык</label>
                <select name="language" class="form-select">
                    <option value="">Все</option>
                    <?php foreach (['en', 'ru', 'mixed'] as $lang): ?>
                        <option value="<?= $lang ?>" <?= $filters['language'] === $lang ? 'selected' : '' ?>><?= $lang ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Причина отклонения</label>
                <select name="reject_reason" class="form-select">
                    <option value="">Все</option>
                    <?php foreach (['junk', 'duplicate', 'brand', 'low_volume', 'forbidden', 'already_used'] as $reason): ?>
                        <option value="<?= $reason ?>" <?= $filters['rejectReason'] === $reason ? 'selected' : '' ?>><?= $reason ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Фильтр</button>
            </div>
        </form>
    </div>
</div>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => ['class' => 'table table-striped table-bordered'],
    'columns' => [
        'id',
        'text',
        [
            'attribute' => 'source',
            'value' => fn(Keyword $m) => $m->getSourceLabel(),
        ],
        'language',
        'volume',
        [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function (Keyword $m) {
                $class = match ($m->status) {
                    Keyword::STATUS_READY => 'success',
                    Keyword::STATUS_REJECTED, Keyword::STATUS_FORBIDDEN => 'danger',
                    Keyword::STATUS_CLEAN => 'primary',
                    default => 'secondary',
                };
                return Html::tag('span', $m->getStatusLabel(), ['class' => 'badge bg-' . $class . ' badge-status']);
            },
        ],
        'reject_reason',
    ],
]) ?>
