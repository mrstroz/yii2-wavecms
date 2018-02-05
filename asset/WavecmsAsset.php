<?php

namespace mrstroz\wavecms\asset;

use Yii;
use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class WavecmsAsset extends AssetBundle
{
    public $sourcePath = '@vendor/mrstroz/yii2-wavecms/asset/media/build/';
    public $css = [
        'css/all.css'
    ];
    public $js = [
        'js/script.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];


//    public $publishOptions = [
//        'forceCopy'=>true,
//    ];


    public function init()
    {
        parent::init();

        Yii::$app->assetManager->bundles['yii\\bootstrap\\BootstrapAsset'] = [
            'css' => []
        ];

        Yii::$app->assetManager->bundles['yii\\bootstrap\\BootstrapPluginAsset'] = [
            'js' => []
        ];

    }
}
