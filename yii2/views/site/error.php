<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\bootstrap5\Html;

$this->title = $name;
?>
<div class="site-error text-center py-5">
    <h1 class="display-4"><?= Html::encode($this->title) ?></h1>
    <p class="text-muted"><?= nl2br(Html::encode($message)) ?></p>
    <p><?= Html::a('На главную', ['/site/index'], ['class' => 'btn btn-primary']) ?></p>
</div>
