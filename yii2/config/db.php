<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => sprintf(
        'mysql:host=%s;dbname=%s',
        getenv('DB_HOST') ?: 'mysql',
        getenv('DB_NAME') ?: 'site_pro_gads'
    ),
    'username' => getenv('DB_USER') ?: 'app',
    'password' => getenv('DB_PASSWORD') ?: 'app',
    'charset' => 'utf8mb4',
    'tablePrefix' => '',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
