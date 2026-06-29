<?php

return [
    'adminEmail' => 'alex@site.pro',
    'minVolume' => 50,
    'brandKeywords' => [
        'site.pro', 'sitepro', 'site pro', 'сайт про', 'сайте.про', 'сайт.про',
        'wix', 'tilda', 'wordpress.com', 'squarespace',
    ],
    'junkKeywords' => [
        'site', 'www', 'login', 'porn', 'torrent', 'скачать торрент',
    ],
    'forbiddenKeywords' => [
        'free website builder no signup',
    ],
    'targetUrls' => [
        'en' => 'https://site.pro/',
        'ru' => 'https://site.pro/ru/',
        'mixed' => 'https://site.pro/',
    ],
    'adTemplates' => [
        'en' => [
            'headline1' => 'Build Your Website with Site.pro',
            'headline2' => 'Professional Website Builder',
            'description' => 'Create stunning websites easily. No coding required. Start free today.',
        ],
        'ru' => [
            'headline1' => 'Конструктор сайтов Site.pro',
            'headline2' => 'Создайте сайт без программирования',
            'description' => 'Профессиональный конструктор сайтов. Быстрый старт, готовые шаблоны.',
        ],
        'mixed' => [
            'headline1' => 'Site.pro Website Builder',
            'headline2' => 'Create Your Site Today',
            'description' => 'Build professional websites with Site.pro.',
        ],
    ],
    'campaignName' => 'Site.pro - Keywords Import',
];
