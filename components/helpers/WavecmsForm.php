<?php

namespace mrstroz\wavecms\components\helpers;

use yii\bootstrap\ActiveForm;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\Html;

class WavecmsForm extends ActiveForm
{

    public static function begin($config = [])
    {
        $defaultConfig['options']['class'] = 'wavecms-form';
        $defaultConfig['options']['id'] = 'wavecms-form';


        $begin = parent::begin(array_merge($defaultConfig, $config));

        return $begin;
    }

}