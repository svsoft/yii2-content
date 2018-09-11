<?php

namespace svsoft\yii\content\admin\widgets;

use yii\web\AssetBundle;

/**
 * Class FileUploadAsset
 * @package svsoft\yii\content\admin\widgets
 */
class FileUploadAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/content/admin/widgets/assets';

    public $css = [
        'css/file-upload-widget.css',
        'test.css'
    ];

    public $js = [
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
