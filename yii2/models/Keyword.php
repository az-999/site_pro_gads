<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $text
 * @property string $normalized_text
 * @property string $source
 * @property string|null $language
 * @property int|null $volume
 * @property string $status
 * @property string|null $reject_reason
 * @property int|null $import_batch_id
 * @property int $created_at
 */
class Keyword extends ActiveRecord
{
    public const SOURCE_GOOGLE_ADS = 'google_ads';
    public const SOURCE_SEARCH_CONSOLE = 'search_console';
    public const SOURCE_AHREFS_ORGANIC = 'ahrefs_organic';
    public const SOURCE_AHREFS_PAID = 'ahrefs_paid';

    public const STATUS_RAW = 'raw';
    public const STATUS_CLEAN = 'clean';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_USED = 'used';
    public const STATUS_FORBIDDEN = 'forbidden';
    public const STATUS_READY = 'ready';

    public static function tableName(): string
    {
        return '{{%keyword}}';
    }

    public function rules(): array
    {
        return [
            [['text', 'normalized_text', 'source', 'status'], 'required'],
            [['volume', 'import_batch_id', 'created_at'], 'integer'],
            [['text'], 'string', 'max' => 500],
            [['normalized_text'], 'string', 'max' => 500],
            [['source', 'status', 'reject_reason', 'language'], 'string', 'max' => 64],
        ];
    }

    public static function sourceLabels(): array
    {
        return [
            self::SOURCE_GOOGLE_ADS => 'Google Ads',
            self::SOURCE_SEARCH_CONSOLE => 'Search Console',
            self::SOURCE_AHREFS_ORGANIC => 'Ahrefs Organic',
            self::SOURCE_AHREFS_PAID => 'Ahrefs Paid',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_RAW => 'Новый',
            self::STATUS_CLEAN => 'Чистый',
            self::STATUS_REJECTED => 'Отклонён',
            self::STATUS_USED => 'Уже используется',
            self::STATUS_FORBIDDEN => 'Запрещён',
            self::STATUS_READY => 'Готов к экспорту',
        ];
    }

    public function getSourceLabel(): string
    {
        return self::sourceLabels()[$this->source] ?? $this->source;
    }

    public function getStatusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }
}
