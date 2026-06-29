<?php

namespace app\controllers;

use app\assets\UploadAsset;
use app\models\ImportBatch;
use app\models\Keyword;
use app\services\ImportService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class ImportController extends Controller
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
                    'upload' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        UploadAsset::register($this->view);

        $batches = ImportBatch::find()->orderBy(['id' => SORT_DESC])->limit(10)->all();
        $sourceTypes = Keyword::sourceLabels();

        return $this->render('index', [
            'batches' => $batches,
            'sourceTypes' => $sourceTypes,
        ]);
    }

    public function actionUpload(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $sourceType = Yii::$app->request->post('source_type');
        $validSources = array_keys(Keyword::sourceLabels());

        if (!in_array($sourceType, $validSources, true)) {
            return ['success' => false, 'msg' => 'Неверный тип источника.'];
        }

        $file = UploadedFile::getInstanceByName('uploadfile');
        if ($file === null) {
            return ['success' => false, 'msg' => 'Файл не получен.'];
        }

        $ext = strtolower($file->extension);
        if (!in_array($ext, ['csv', 'json'], true)) {
            return ['success' => false, 'msg' => 'Допустимы только CSV и JSON.'];
        }

        $uploadDir = Yii::getAlias('@runtime/uploads');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $savePath = $uploadDir . '/' . uniqid('import_', true) . '.' . $ext;
        if (!$file->saveAs($savePath)) {
            return ['success' => false, 'msg' => 'Не удалось сохранить файл.'];
        }

        try {
            $batch = (new ImportService())->importFromFile($savePath, $sourceType, $file->name);
            @unlink($savePath);
            return [
                'success' => true,
                'rows' => $batch->rows_count,
                'batch_id' => $batch->id,
            ];
        } catch (\Throwable $e) {
            @unlink($savePath);
            return ['success' => false, 'msg' => $e->getMessage()];
        }
    }
}
