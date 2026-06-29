<?php

namespace app\controllers;

use app\models\Keyword;
use app\services\GoogleAdsExportService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class ExportController extends Controller
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
                    'download' => ['get', 'post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $readyCount = Keyword::find()->where(['status' => Keyword::STATUS_READY])->count();

        return $this->render('index', ['readyCount' => $readyCount]);
    }

    public function actionDownload(): Response
    {
        $csv = (new GoogleAdsExportService())->generateCsv();

        if ($csv === '') {
            Yii::$app->session->setFlash('warning', 'Нет ключевых слов для экспорта. Запустите пайплайн обработки.');
            return $this->redirect(['export/index']);
        }

        return Yii::$app->response->sendContentAsFile(
            $csv,
            'google_ads_import_' . date('Y-m-d') . '.csv',
            ['mimeType' => 'text/csv']
        );
    }
}
