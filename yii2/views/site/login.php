<?php

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Вход';
?>
<div class="card shadow login-card-inner">
    <div class="card-body p-4">
        <h1 class="h4 mb-1 text-center login-title">Site.pro GAds</h1>
        <p class="text-muted text-center small mb-4">Платформа автоматизации ключевых слов</p>
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
        <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'alex@site.pro']) ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => '••••']) ?>
        <?= $form->field($model, 'rememberMe')->checkbox() ?>
        <div class="d-grid">
            <?= Html::submitButton('Войти', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
