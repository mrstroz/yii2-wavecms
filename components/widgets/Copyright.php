<?php

namespace mrstroz\wavecms\components\widgets;

use phpDocumentor\Reflection\Types\Self_;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\VarDumper;

class Copyright extends Widget
{

    public static $name = 'WaveCMS';
    public static $version = '';


    public function run()
    {
        $data = array();
        $packages = json_decode(file_get_contents(Yii::getAlias('@vendor') . '/composer/installed.json'));
        foreach ($packages as $package) {
            if (isset($package->name) && $package->name === 'mrstroz/yii2-wavecms') {
                if (strpos($package->version, 'dev') === false) {
                    self::$version = $package->version;
                }
            }
        }

        return Html::beginTag('div', ['class' => 'copyright']) . self::$name . ' ' . self::$version . Html::endTag('div');
    }

}
