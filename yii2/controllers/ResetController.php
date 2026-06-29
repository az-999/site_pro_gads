<?php

namespace app\controllers;

use app\services\ResetService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class ResetController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'run' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $stats = (new ResetService())->getStats();

        return $this->render('index', ['stats' => $stats]);
    }

    public function actionRun(): Response
    {
        $confirm = Yii::$app->request->post('confirm');
        if ($confirm !== 'RESET') {
            Yii::$app->session->setFlash('error', 'Введите RESET для подтверждения.');
            return $this->redirect(['reset/index']);
        }

        try {
            $cleared = (new ResetService())->clearImportData();
            Yii::$app->session->setFlash('success', sprintf(
                'Данные очищены: удалено %d ключевых слов и %d загрузок. Можно импортировать заново.',
                $cleared['keywords'],
                $cleared['batches']
            ));
        } catch (\Throwable $e) {
            Yii::$app->session->setFlash('error', 'Ошибка сброса: ' . $e->getMessage());
        }

        return $this->redirect(['reset/index']);
    }
}
