<?php

namespace mrstroz\wavecms;

use yii\base\Module as BaseModule;

/**
 * Class Module
 * @package mrstroz\wavecms
 * This is the main module class of the yii2-wavecms.
 */
class Module extends BaseModule
{
    /**
     * @var string Error action for wavecms
     */
    public $errorAction = '/wavecms/error/error';

    /**
     * @var array|string
     */
    public $loginUrl = ['login'];

    /**
     * @var array Class mapping
     */
    public $classMap = [];

    /**
     * @var array Languages that are using in CMS
     */
    public $languages = ['en'];

    /**
     * @var array url routes
     */
    public $routes = [
        '/' => 'wavecms/dashboard/index',
        'login' => 'wavecms/login/login',
        'logout' => 'wavecms/login/logout',
        'my-account' => 'wavecms/login/my-account',
        'request-password-reset' => 'wavecms/login/request-password-reset',
        'reset-password' => 'wavecms/login/reset-password'
    ];


}
