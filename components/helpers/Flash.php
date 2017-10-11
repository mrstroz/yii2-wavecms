<?php

namespace mrstroz\wavecms\components\helpers;

use Yii;
use yii\base\Component;

class Flash extends Component
{

    public static $default_options = [
        'duration' => 5000,
        'message' => '',
        'positonY' => 'top',
        'positonX' => 'center'
    ];

    public static $options = [
        'success' => [
            'type' => 'success',
            'icon' => 'fa fa-check',
            'title' => 'Success'
        ],
        'warning' => [
            'type' => 'warning',
            'icon' => 'fa fa-exclamation-triangle',
            'title' => 'Warning'
        ],
        'danger' => [
            'type' => 'danger',
            'icon' => 'fa fa-exclamation-circle',
            'title' => 'Warning'
        ],
        'info' => [
            'type' => 'info',
            'icon' => 'fa fa-info-circle',
            'title' => 'Info'
        ]
    ];

    static public function message($key, $type, $options)
    {
        Yii::$app->getSession()->setFlash($key, array_merge(self::$default_options, self::$options[$type], $options));
    }
}