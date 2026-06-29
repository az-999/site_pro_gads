<?php

use yii\db\Migration;

class m240101_000001_init extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string(255)->notNull()->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%import_batch}}', [
            'id' => $this->primaryKey(),
            'filename' => $this->string(255)->notNull(),
            'format' => $this->string(32)->notNull(),
            'source_type' => $this->string(32)->notNull(),
            'rows_count' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%keyword}}', [
            'id' => $this->primaryKey(),
            'text' => $this->string(500)->notNull(),
            'normalized_text' => $this->string(500)->notNull(),
            'source' => $this->string(32)->notNull(),
            'language' => $this->string(8)->null(),
            'volume' => $this->integer()->null(),
            'status' => $this->string(32)->notNull()->defaultValue('raw'),
            'reject_reason' => $this->string(64)->null(),
            'import_batch_id' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx_keyword_normalized', '{{%keyword}}', 'normalized_text');
        $this->createIndex('idx_keyword_status', '{{%keyword}}', 'status');
        $this->createIndex('idx_keyword_source', '{{%keyword}}', 'source');
        $this->addForeignKey(
            'fk_keyword_batch',
            '{{%keyword}}',
            'import_batch_id',
            '{{%import_batch}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createTable('{{%forbidden_keyword}}', [
            'id' => $this->primaryKey(),
            'text' => $this->string(500)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%setting}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(64)->notNull()->unique(),
            'value' => $this->text()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $security = Yii::$app->security;
        $this->insert('{{%user}}', [
            'email' => 'alex@site.pro',
            'password_hash' => $security->generatePasswordHash('111'),
            'auth_key' => $security->generateRandomString(),
            'created_at' => time(),
        ]);

        $now = time();
        $forbidden = [
            'free website builder no signup',
        ];
        foreach ($forbidden as $text) {
            $this->insert('{{%forbidden_keyword}}', [
                'text' => $text,
                'created_at' => $now,
            ]);
        }

        $this->insert('{{%setting}}', [
            'key' => 'min_volume',
            'value' => '50',
            'updated_at' => $now,
        ]);

        $brands = [
            'site.pro', 'sitepro', 'site pro', 'сайт про', 'сайте.про', 'сайт.про',
            'wix', 'tilda', 'squarespace',
        ];
        $this->insert('{{%setting}}', [
            'key' => 'brand_keywords',
            'value' => json_encode($brands, JSON_UNESCAPED_UNICODE),
            'updated_at' => $now,
        ]);
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_keyword_batch', '{{%keyword}}');
        $this->dropTable('{{%setting}}');
        $this->dropTable('{{%forbidden_keyword}}');
        $this->dropTable('{{%keyword}}');
        $this->dropTable('{{%import_batch}}');
        $this->dropTable('{{%user}}');
    }
}
