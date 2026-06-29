<?php

namespace app\controllers;

use app\services\PipelineService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class PipelineController extends Controller
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

    public function actionRun(): Response
    {
        $stats = (new PipelineService())->runAll();

        Yii::$app->session->setFlash('success', sprintf(
            'Обработка завершена. Чистых: %d, готово к экспорту: %d, отклонено (мусор/бренд/дубль/частота): %d/%d/%d/%d.',
            $stats['clean']['clean'],
            $stats['prepare']['ready'],
            $stats['clean']['junk'],
            $stats['clean']['brand'],
            $stats['clean']['duplicate'],
            $stats['clean']['low_volume']
        ));

        return $this->redirect(['site/index']);
    }
}
