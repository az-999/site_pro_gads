<?php

namespace app\services;

use app\models\Keyword;
use Yii;

class GoogleAdsExportService
{
    public function generateCsv(): string
    {
        $grouped = (new PrepareService())->getGroupedByLanguage();
        $campaignName = Yii::$app->params['campaignName'];
        $templates = Yii::$app->params['adTemplates'];
        $targetUrls = Yii::$app->params['targetUrls'];

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, [
            'Campaign',
            'Ad Group',
            'Keyword',
            'Criterion Type',
            'Final URL',
            'Headline 1',
            'Headline 2',
            'Description',
        ]);

        foreach ($grouped as $lang => $keywords) {
            $adGroup = 'AG - ' . strtoupper($lang);
            $template = $templates[$lang] ?? $templates['mixed'];
            $finalUrl = $targetUrls[$lang] ?? $targetUrls['mixed'];

            foreach ($keywords as $keyword) {
                fputcsv($handle, [
                    $campaignName,
                    $adGroup,
                    $keyword->text,
                    'Phrase',
                    $finalUrl,
                    $template['headline1'],
                    $template['headline2'],
                    $template['description'],
                ]);
            }
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv ?: '';
    }

    public function getPreviewData(): array
    {
        $grouped = (new PrepareService())->getGroupedByLanguage();
        $templates = Yii::$app->params['adTemplates'];
        $targetUrls = Yii::$app->params['targetUrls'];
        $preview = [];

        foreach ($grouped as $lang => $keywords) {
            $template = $templates[$lang] ?? $templates['mixed'];
            $preview[] = [
                'language' => $lang,
                'count' => count($keywords),
                'ad_group' => 'AG - ' . strtoupper($lang),
                'final_url' => $targetUrls[$lang] ?? $targetUrls['mixed'],
                'headline1' => $template['headline1'],
                'headline2' => $template['headline2'],
                'description' => $template['description'],
                'keywords' => array_slice(array_map(fn($k) => $k->text, $keywords), 0, 20),
            ];
        }

        return $preview;
    }
}
