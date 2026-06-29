<?php

namespace app\controllers;

use app\models\ForbiddenKeyword;
use app\models\Setting;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class SettingsController extends Controller
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
                    'save' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $minVolume = Setting::getValue('min_volume', Yii::$app->params['minVolume']);
        $brands = Setting::getValue('brand_keywords', Yii::$app->params['brandKeywords']);
        $forbidden = ForbiddenKeyword::find()->orderBy(['id' => SORT_ASC])->all();

        return $this->render('index', [
            'minVolume' => $minVolume,
            'brands' => is_array($brands) ? implode("\n", $brands) : $brands,
            'forbidden' => $forbidden,
        ]);
    }

    public function actionSave(): Response
    {
        $minVolume = (int) Yii::$app->request->post('min_volume', 50);
        $brandsText = Yii::$app->request->post('brand_keywords', '');
        $forbiddenText = Yii::$app->request->post('forbidden_keywords', '');

        Setting::setValue('min_volume', (string) max(0, $minVolume));

        $brands = array_values(array_filter(array_map('trim', explode("\n", $brandsText))));
        Setting::setValue('brand_keywords', $brands);

        ForbiddenKeyword::deleteAll();
        $lines = array_filter(array_map('trim', explode("\n", $forbiddenText)));
        foreach ($lines as $line) {
            $fk = new ForbiddenKeyword(['text' => $line, 'created_at' => time()]);
            $fk->save(false);
        }

        Yii::$app->session->setFlash('success', 'Настройки сохранены.');
        return $this->redirect(['settings/index']);
    }
}
