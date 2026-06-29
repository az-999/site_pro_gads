<?php

namespace app\controllers;

use app\services\GoogleAdsExportService;
use yii\filters\AccessControl;
use yii\web\Controller;

class PreviewController extends Controller
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
        ];
    }

    public function actionIndex(): string
    {
        $preview = (new GoogleAdsExportService())->getPreviewData();

        return $this->render('index', ['preview' => $preview]);
    }
}
