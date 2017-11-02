<?php

namespace mrstroz\wavecms;

use mrstroz\wavecms\components\helpers\FontAwesome;
use Yii;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        Yii::setAlias('@wavecms', '@vendor/mrstroz/yii2-wavecms');

        Yii::$app->i18n->translations['wavecms/user/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@wavecms/messages',
            'fileMap' => [
                'wavecms/user/login' => 'login.php',
            ],
        ];

        Yii::$app->i18n->translations['wavecms/base/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@wavecms/messages',
            'fileMap' => [
                'wavecms/base/main' => 'main.php',
            ],
        ];


        if ($app->hasModule('wavecms') && ($module = $app->getModule('wavecms')) instanceof Module) {

            if ($app instanceof ConsoleApplication) {
                $module->controllerNamespace = 'mrstroz\wavecms\commands';
            } else {

                if (!Yii::$app->user->isGuest) {
                    Yii::$app->language = Yii::$app->user->identity->lang;
                }

                $module->controllerNamespace = 'mrstroz\wavecms\controllers';

                Yii::$app->errorHandler->errorAction = '/wavecms/error/error';

                if (!count(Yii::$app->wavecms->languages))
                    throw new InvalidConfigException(Yii::t('wavecms/base/main', 'Property "languages" is not defined in config file for component "wavecms"'));

                if (!Yii::$app->session->get('editedLanguage')) {
                    Yii::$app->session->set('editedLanguage', Yii::$app->wavecms->languages[0]);
                }

                Yii::$app->wavecms->editedLanguage = Yii::$app->session->get('editedLanguage');

                Yii::$app->view->params['h1'] = Yii::t('wavecms/base/main', '<i>Not set</i>');
                Yii::$app->view->params['buttons_top'] = [];
                Yii::$app->view->params['buttons_btm'] = [];
                Yii::$app->view->params['buttons_sublist'] = [];

                Yii::$app->params['nav']['dashboard'] = [
                    'label' => FontAwesome::icon('home') . Yii::t('wavecms/base/main', 'Dashboard'),
                    'url' => ['/'],
                    'position' => 500
                ];

                Yii::$app->params['nav']['user'] = [
                    'label' => FontAwesome::icon('users') . Yii::t('wavecms/user/login', 'Users'),
                    'url' => 'javascript: ;',
                    'options' => [
                        'class' => 'drop-down'
                    ],
                    'permission' => 'user',
                    'position' => 9000,
                    'items' => [
                        [
                            'label' => FontAwesome::icon('list') . Yii::t('wavecms/user/login', 'List of users'),
                            'url' => ['/wavecms/user/index']
                        ],
                        [
                            'label' => FontAwesome::icon('key') . Yii::t('wavecms/user/login', 'Roles'),
                            'url' => ['/wavecms/role/index']
                        ],
                    ]
                ];

                Yii::$app->params['nav']['wavecms_settings'] = [
                    'label' => FontAwesome::icon('cog') . Yii::t('wavecms/base/main', 'Settings'),
                    'url' => 'javascript: ;',
                    'options' => [
                        'class' => 'drop-down'
                    ],
                    'permission' => 'user',
                    'position' => 10000,
                    'items' => [
                        [
                            'label' => FontAwesome::icon('flag') . Yii::t('wavecms/base/main', 'Translations'),
                            'url' => ['/wavecms/translation/index']
                        ]
                    ]
                ];

                Yii::$app->getUrlManager()->addRules(['/' => 'wavecms/dashboard/index']);
                Yii::$app->getUrlManager()->addRules(['login' => 'wavecms/login/login']);
                Yii::$app->getUrlManager()->addRules(['logout' => 'wavecms/login/logout']);
                Yii::$app->getUrlManager()->addRules(['my-account' => 'wavecms/login/my-account']);
                Yii::$app->getUrlManager()->addRules(['request-password-reset' => 'wavecms/login/request-password-reset']);
                Yii::$app->getUrlManager()->addRules(['reset-password' => 'wavecms/login/reset-password']);

                Yii::setAlias('@frontWeb', str_replace('/admin', '', Yii::getAlias('@web')));
                Yii::setAlias('@frontWebroot', str_replace('/admin', '', Yii::getAlias('@webroot')));

                Yii::$app->user->loginUrl = ['login'];

            }
        }
    }
}