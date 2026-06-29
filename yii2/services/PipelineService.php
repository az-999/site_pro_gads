<?php

namespace app\services;

use Yii;

class PipelineService
{
    public function runAll(): array
    {
        $cleanStats = (new CleanService())->run();
        $prepareStats = (new PrepareService())->run();

        return [
            'clean' => $cleanStats,
            'prepare' => $prepareStats,
        ];
    }
}
