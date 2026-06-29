<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $text
 * @property int $created_at
 */
class ForbiddenKeyword extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%forbidden_keyword}}';
    }

    public function rules(): array
    {
        return [
            [['text'], 'required'],
            [['text'], 'string', 'max' => 500],
            [['created_at'], 'integer'],
        ];
    }
}
