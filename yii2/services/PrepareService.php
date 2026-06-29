<?php

namespace app\services;

use app\models\ForbiddenKeyword;
use app\models\Keyword;
use Yii;

class PrepareService
{
    public function run(): array
    {
        $stats = [
            'used' => 0,
            'forbidden' => 0,
            'ready' => 0,
        ];

        $usedNormalized = Keyword::find()
            ->select('normalized_text')
            ->where(['source' => Keyword::SOURCE_GOOGLE_ADS])
            ->column();

        $usedMap = array_flip($usedNormalized);

        $forbiddenTexts = ForbiddenKeyword::find()->select('text')->column();
        $forbiddenNormalized = array_map(
            [KeywordNormalizer::class, 'normalize'],
            array_merge($forbiddenTexts, Yii::$app->params['forbiddenKeywords'])
        );
        $forbiddenMap = array_flip($forbiddenNormalized);

        $keywords = Keyword::find()
            ->where(['status' => Keyword::STATUS_CLEAN])
            ->all();

        $readySeen = [];

        foreach ($keywords as $keyword) {
            $norm = $keyword->normalized_text;

            if (isset($usedMap[$norm])) {
                $keyword->status = Keyword::STATUS_USED;
                $keyword->reject_reason = 'already_used';
                $keyword->save(false);
                $stats['used']++;
                continue;
            }

            if (isset($forbiddenMap[$norm])) {
                $keyword->status = Keyword::STATUS_FORBIDDEN;
                $keyword->reject_reason = 'forbidden';
                $keyword->save(false);
                $stats['forbidden']++;
                continue;
            }

            if (isset($readySeen[$norm])) {
                $keyword->status = Keyword::STATUS_REJECTED;
                $keyword->reject_reason = 'duplicate';
                $keyword->save(false);
                continue;
            }

            $readySeen[$norm] = true;
            $keyword->language = KeywordNormalizer::detectLanguage($keyword->text);
            $keyword->status = Keyword::STATUS_READY;
            $keyword->reject_reason = null;
            $keyword->save(false);
            $stats['ready']++;
        }

        return $stats;
    }

    /**
     * @return array<string, Keyword[]>
     */
    public function getGroupedByLanguage(): array
    {
        $keywords = Keyword::find()
            ->where(['status' => Keyword::STATUS_READY])
            ->orderBy(['language' => SORT_ASC, 'text' => SORT_ASC])
            ->all();

        $grouped = [];
        foreach ($keywords as $keyword) {
            $lang = $keyword->language ?? 'mixed';
            $grouped[$lang][] = $keyword;
        }
        return $grouped;
    }
}
