<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $key
 * @property string $value
 * @property int $updated_at
 */
class Setting extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%setting}}';
    }

    public function rules(): array
    {
        return [
            [['key', 'value'], 'required'],
            [['key'], 'string', 'max' => 64],
            [['value'], 'string'],
            [['updated_at'], 'integer'],
        ];
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $row = static::findOne(['key' => $key]);
        if ($row === null) {
            return $default;
        }
        $decoded = json_decode($row->value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $row->value;
    }

    public static function setValue(string $key, mixed $value): void
    {
        $row = static::findOne(['key' => $key]) ?? new static(['key' => $key]);
        $row->value = is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);
        $row->updated_at = time();
        $row->save(false);
    }
}
