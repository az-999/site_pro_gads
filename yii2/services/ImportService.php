<?php

namespace app\services;

use app\models\ImportBatch;
use app\models\Keyword;
use RuntimeException;
use Yii;

class ImportService
{
    private const COLUMN_MAP = [
        Keyword::SOURCE_GOOGLE_ADS => ['keyword', 'query', 'search term'],
        Keyword::SOURCE_SEARCH_CONSOLE => ['query', 'keyword', 'search query'],
        Keyword::SOURCE_AHREFS_ORGANIC => ['keyword', 'query'],
        Keyword::SOURCE_AHREFS_PAID => ['keyword', 'query'],
    ];

    private const VOLUME_MAP = [
        Keyword::SOURCE_GOOGLE_ADS => ['volume', 'clicks', 'impressions'],
        Keyword::SOURCE_SEARCH_CONSOLE => ['impressions', 'clicks', 'volume'],
        Keyword::SOURCE_AHREFS_ORGANIC => ['volume', 'search volume'],
        Keyword::SOURCE_AHREFS_PAID => ['volume', 'search volume'],
    ];

    public function importFromFile(string $filePath, string $sourceType, string $originalName): ImportBatch
    {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'json'], true)) {
            throw new RuntimeException('Допустимы только CSV и JSON файлы.');
        }

        $rows = $ext === 'csv'
            ? $this->parseCsv($filePath)
            : $this->parseJson($filePath);

        if (empty($rows)) {
            throw new RuntimeException('Файл пуст или не содержит данных.');
        }

        $batch = new ImportBatch([
            'filename' => $originalName,
            'format' => $ext,
            'source_type' => $sourceType,
            'rows_count' => 0,
            'created_at' => time(),
        ]);
        $batch->save(false);

        $count = 0;
        foreach ($rows as $row) {
            $text = $this->extractText($row, $sourceType);
            if ($text === null || $text === '') {
                continue;
            }

            $volume = $this->extractVolume($row, $sourceType);
            $keyword = new Keyword([
                'text' => $text,
                'normalized_text' => KeywordNormalizer::normalize($text),
                'source' => $sourceType,
                'language' => KeywordNormalizer::detectLanguage($text),
                'volume' => $volume,
                'status' => $sourceType === Keyword::SOURCE_GOOGLE_ADS
                    ? Keyword::STATUS_USED
                    : Keyword::STATUS_RAW,
                'reject_reason' => null,
                'import_batch_id' => $batch->id,
                'created_at' => time(),
            ]);
            $keyword->save(false);
            $count++;
        }

        $batch->rows_count = $count;
        $batch->save(false);

        return $batch;
    }

    private function parseCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new RuntimeException('Не удалось открыть CSV файл.');
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return [];
        }

        $header = array_map(fn($h) => $this->normalizeHeader((string) $h), $header);
        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < count($header)) {
                $data = array_pad($data, count($header), '');
            }
            $rows[] = array_combine($header, array_slice($data, 0, count($header)));
        }

        fclose($handle);
        return $rows;
    }

    private function parseJson(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new RuntimeException('Не удалось прочитать JSON файл.');
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Некорректный JSON.');
        }

        if (isset($decoded['data']) && is_array($decoded['data'])) {
            $decoded = $decoded['data'];
        }

        $rows = [];
        foreach ($decoded as $item) {
            if (!is_array($item)) {
                continue;
            }
            $normalized = [];
            foreach ($item as $key => $value) {
                $normalized[$this->normalizeHeader((string) $key)] = $value;
            }
            $rows[] = $normalized;
        }

        return $rows;
    }

    private function normalizeHeader(string $header): string
    {
        return mb_strtolower(trim($header), 'UTF-8');
    }

    private function extractText(array $row, string $sourceType): ?string
    {
        $candidates = self::COLUMN_MAP[$sourceType] ?? ['keyword', 'query', 'text'];
        foreach ($candidates as $col) {
            if (isset($row[$col]) && trim((string) $row[$col]) !== '') {
                return trim((string) $row[$col]);
            }
        }
        foreach ($row as $value) {
            if (is_string($value) && trim($value) !== '' && !is_numeric($value)) {
                return trim($value);
            }
        }
        return null;
    }

    private function extractVolume(array $row, string $sourceType): ?int
    {
        $candidates = self::VOLUME_MAP[$sourceType] ?? ['volume', 'clicks', 'impressions'];
        foreach ($candidates as $col) {
            if (isset($row[$col]) && is_numeric($row[$col])) {
                return (int) $row[$col];
            }
        }
        return null;
    }
}
