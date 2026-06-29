<?php

namespace app\services;

use app\models\ImportBatch;
use app\models\Keyword;
use Yii;

class ResetService
{
    public function getStats(): array
    {
        return [
            'keywords' => (int) Keyword::find()->count(),
            'batches' => (int) ImportBatch::find()->count(),
        ];
    }

    public function clearImportData(): array
    {
        $stats = $this->getStats();

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            $db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
            $db->createCommand()->truncateTable(Keyword::tableName())->execute();
            $db->createCommand()->truncateTable(ImportBatch::tableName())->execute();
            $db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->clearUploadTempFiles();

        return $stats;
    }

    private function clearUploadTempFiles(): void
    {
        $dir = Yii::getAlias('@runtime/uploads');
        if (!is_dir($dir)) {
            return;
        }

        foreach (glob($dir . '/*') ?: [] as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
}
