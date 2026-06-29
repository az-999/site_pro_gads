<?php

namespace app\services;

use app\models\ForbiddenKeyword;
use app\models\Keyword;
use app\models\Setting;
use Yii;

class CleanService
{
    public function run(): array
    {
        $stats = [
            'junk' => 0,
            'duplicate' => 0,
            'brand' => 0,
            'low_volume' => 0,
            'clean' => 0,
        ];

        $minVolume = (int) Setting::getValue('min_volume', Yii::$app->params['minVolume']);
        $brands = Setting::getValue('brand_keywords', Yii::$app->params['brandKeywords']);
        $junkList = Yii::$app->params['junkKeywords'];

        $brandNormalized = array_map([KeywordNormalizer::class, 'normalize'], $brands);
        $junkNormalized = array_map([KeywordNormalizer::class, 'normalize'], $junkList);

        $seen = [];
        $keywords = Keyword::find()
            ->where(['status' => [Keyword::STATUS_RAW, Keyword::STATUS_CLEAN]])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        foreach ($keywords as $keyword) {
            if ($keyword->source === Keyword::SOURCE_GOOGLE_ADS) {
                continue;
            }

            $norm = $keyword->normalized_text;

            if ($this->isJunk($norm, $junkNormalized)) {
                $this->reject($keyword, 'junk');
                $stats['junk']++;
                continue;
            }

            if ($this->isBrand($norm, $brandNormalized)) {
                $this->reject($keyword, 'brand');
                $stats['brand']++;
                continue;
            }

            if (isset($seen[$norm])) {
                $this->reject($keyword, 'duplicate');
                $stats['duplicate']++;
                continue;
            }

            if ($keyword->volume !== null && $keyword->volume < $minVolume) {
                $this->reject($keyword, 'low_volume');
                $stats['low_volume']++;
                continue;
            }

            $seen[$norm] = true;
            $keyword->status = Keyword::STATUS_CLEAN;
            $keyword->reject_reason = null;
            $keyword->save(false);
            $stats['clean']++;
        }

        return $stats;
    }

    private function isJunk(string $normalized, array $junkList): bool
    {
        if (mb_strlen($normalized) < 3) {
            return true;
        }
        foreach ($junkList as $junk) {
            if ($normalized === $junk || str_contains($normalized, $junk)) {
                return true;
            }
        }
        return false;
    }

    private function isBrand(string $normalized, array $brands): bool
    {
        foreach ($brands as $brand) {
            if ($normalized === $brand || str_contains($normalized, $brand)) {
                return true;
            }
        }
        return false;
    }

    private function reject(Keyword $keyword, string $reason): void
    {
        $keyword->status = Keyword::STATUS_REJECTED;
        $keyword->reject_reason = $reason;
        $keyword->save(false);
    }
}
