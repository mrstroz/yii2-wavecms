<?php

namespace mrstroz\wavecms\components\helpers;

use yii\bootstrap\ActiveForm;
use mrstroz\wavecms\components\widgets\ActiveField;

class WavecmsForm extends ActiveForm
{

    public $fieldClass = ActiveField::class;

    public static function begin($config = [])
    {
        $defaultConfig['options']['class'] = 'wavecms-form';
        $defaultConfig['options']['id'] = 'wavecms-form';


        $begin = parent::begin(array_merge($defaultConfig, $config));

        return $begin;
    }

}