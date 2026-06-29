<?php

$params = require __DIR__ . '/params.php';
if (file_exists(__DIR__ . '/params-local.php')) {
    $params = array_merge($params, require __DIR__ . '/params-local.php');
}
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'site-pro-gads',
    'name' => 'Site.pro GAds',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'site-pro-gads-cookie-key-change-in-prod',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\MemCache',
            'servers' => [
                [
                    'host' => getenv('MEMCACHED_HOST') ?: 'memcached',
                    'port' => 11211,
                ],
            ],
            'useMemcached' => true,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'login' => 'site/login',
                'logout' => 'site/logout',
                'import' => 'import/index',
                'import/upload' => 'import/upload',
                'keywords' => 'keyword/index',
                'settings' => 'settings/index',
                'settings/save' => 'settings/save',
                'preview' => 'preview/index',
                'export' => 'export/index',
                'export/download' => 'export/download',
                'pipeline/run' => 'pipeline/run',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

if (file_exists(__DIR__ . '/web-local.php')) {
    $config = yii\helpers\ArrayHelper::merge($config, require __DIR__ . '/web-local.php');
}

return $config;
