<?php

namespace app\assets;

use yii\web\AssetBundle;

class UploadAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/SimpleAjaxUploader.min.js',
        'js/upload.js',
    ];
    public $depends = [
        AppAsset::class,
    ];
}
