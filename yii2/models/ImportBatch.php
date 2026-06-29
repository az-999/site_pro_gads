<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $filename
 * @property string $format
 * @property string $source_type
 * @property int $rows_count
 * @property int $created_at
 */
class ImportBatch extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%import_batch}}';
    }

    public function rules(): array
    {
        return [
            [['filename', 'format', 'source_type'], 'required'],
            [['rows_count', 'created_at'], 'integer'],
            [['filename'], 'string', 'max' => 255],
            [['format', 'source_type'], 'string', 'max' => 32],
        ];
    }

    public function getKeywords()
    {
        return $this->hasMany(Keyword::class, ['import_batch_id' => 'id']);
    }
}
