<?php

namespace mrstroz\wavecms;

use mrstroz\wavecms\components\helpers\FontAwesome;
use mrstroz\wavecms\forms\AddPermissionForm;
use mrstroz\wavecms\forms\AssignRoleForm;
use mrstroz\wavecms\forms\LoginForm;
use mrstroz\wavecms\forms\RequestPasswordResetForm;
use mrstroz\wavecms\forms\ResetPasswordForm;
use mrstroz\wavecms\models\AuthAssignment;
use mrstroz\wavecms\models\AuthItem;
use mrstroz\wavecms\models\AuthItemChild;
use mrstroz\wavecms\models\AuthRule;
use mrstroz\wavecms\models\Message;
use mrstroz\wavecms\models\query\MessageQuery;
use mrstroz\wavecms\models\query\SourceMessageQuery;
use mrstroz\wavecms\models\query\UserQuery;
use mrstroz\wavecms\models\search\SourceMessageSearch;
use mrstroz\wavecms\models\search\UserSearch;
use mrstroz\wavecms\models\SourceMessage;
use mrstroz\wavecms\models\User;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\console\Application as ConsoleApplication;
use yii\i18n\PhpMessageSource;
use yii\web\GroupUrlRule;

/**
 * Class Bootstrap
 * @package mrstroz\wavecms
 * Boostrap class for wavecms. Initialize languages, admin navigation, params
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * Bootstrap for wavecms
     * @param \yii\base\Application $app
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function bootstrap($app)
    {
        Yii::setAlias('@wavecms', '@vendor/mrstroz/yii2-wavecms');

        if ($app->id === 'app-backend' || $app->id === 'app-frontend') {

            /** Set extra aliases required in Wavecms with shared hostings*/
            Yii::setAlias('@frontWeb',
                str_replace('/admin', '', Yii::getAlias('@web'))
            );
            Yii::setAlias('@frontWebroot',
                str_replace('/public/admin', '/public', Yii::getAlias('@webroot'))
            );

        }

        /** Set backend language based on user lang (Must be done before define translations */
        if ($app->id === 'app-backend') {
            if (!Yii::$app->user->isGuest) {
                Yii::$app->language = Yii::$app->user->identity->lang;
            }
        }

        $this->initTranslations();

        /** @var Module $module */
        if ($app->hasModule('wavecms') && ($module = $app->getModule('wavecms')) instanceof Module) {

            if ($app instanceof ConsoleApplication) {
                $module->controllerNamespace = 'mrstroz\wavecms\commands';
            } else {
                $module->controllerNamespace = 'mrstroz\wavecms\controllers';

                if ($app->id === 'app-backend') {

                    /** @var string errorAction Set error action */
                    Yii::$app->errorHandler->errorAction = $module->errorAction;

                    /** Set required components */
                    $app->set('wavecms', [
                        'class' => 'mrstroz\wavecms\WavecmsComponent',
                        'languages' => $module->languages
                    ]);

                    $app->set('cacheFrontend', [
                        'class' => 'yii\caching\FileCache',
                        'cachePath' => Yii::getAlias('@frontend') . '/runtime/cache'
                    ]);

                    $app->set('settings', [
                        'class' => 'yii2mod\settings\components\Settings',
                    ]);

                    Yii::$app->assetManager->appendTimestamp = true;

                    Yii::$app->i18n->translations['yii2mod.settings'] = [
                        'class' => 'yii\i18n\PhpMessageSource',
                        'basePath' => '@yii2mod/settings/messages'
                    ];

                    $this->initContainer($module);
                    $this->initLanguages();
                    $this->initParams();
                    $this->initRoutes($app, $module);
                    $this->initNavigation();
                }
            }
        }
    }

    /**
     * Init translations
     */
    protected function initTranslations()
    {
        Yii::$app->i18n->translations['wavecms/*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@wavecms/messages',
            'fileMap' => [
                'main' => 'main.php',
                'user' => 'user.php',
            ],
        ];
    }

    /**
     * Init wavecms languages
     * @param $module
     * @throws \yii\base\InvalidConfigException
     */
    protected function initLanguages()
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->language = Yii::$app->user->identity->lang;
        }

        if (!isset(Yii::$app->wavecms))
            throw new InvalidConfigException(Yii::t('wavecms/main', 'Component "wavecms" not defined in config.php'));

        if (!count(Yii::$app->wavecms->languages))
            throw new InvalidConfigException(Yii::t('wavecms/main', 'Property "languages" is not defined in config file for component "wavecms"'));

        if (!Yii::$app->session->get('editedLanguage')) {
            Yii::$app->session->set('editedLanguage', Yii::$app->wavecms->languages[0]);
        }

        Yii::$app->wavecms->editedLanguage = Yii::$app->session->get('editedLanguage');
    }

    /**
     * Init required params
     */
    protected function initParams()
    {
        Yii::$app->view->params['h1'] = Yii::t('wavecms/main', '<i>Not set</i>');
        Yii::$app->view->params['buttons_top'] = [];
        Yii::$app->view->params['buttons_btm'] = [];
        Yii::$app->view->params['buttons_sublist'] = [];
    }

    /**
     * Init routes
     * @param Application $app
     * @param Module $module
     * @throws InvalidConfigException
     */
    protected function initRoutes(Application $app, $module)
    {
        $config = [
            'class' => GroupUrlRule::class,
            'rules' => $module->routes,
        ];

        $rule = Yii::createObject($config);
        $app->getUrlManager()->addRules([$rule], false);

        Yii::$app->user->loginUrl = $module->loginUrl;
    }

    /**
     * Init class map and dependency injection container
     * @param Module $module
     * @return void
     * @throws Exception
     */
    protected function initContainer($module)
    {
        $map = [];

        $defaultClassMap = [
            /* FORMS */
            'AddPermissionForm' => AddPermissionForm::class,
            'AssignRoleForm' => AssignRoleForm::class,
            'LoginForm' => LoginForm::class,
            'RequestPasswordResetForm' => RequestPasswordResetForm::class,
            'ResetPasswordForm' => ResetPasswordForm::class,

            /* MODELS */
            'AuthAssignment' => AuthAssignment::class,
            'AuthItem' => AuthItem::class,
            'AuthItemChild' => AuthItemChild::class,
            'AuthRule' => AuthRule::class,
            'Message' => Message::class,
            'SourceMessage' => SourceMessage::class,
            'User' => User::class,

            /* QUERIES */
            'MessageQuery' => MessageQuery::class,
            'SourceMessageQuery' => SourceMessageQuery::class,
            'UserQuery' => UserQuery::class,

            /* SEARCH */
            'SourceMessageSearch' => SourceMessageSearch::class,
            'UserSearch' => UserSearch::class,
        ];

        $routes = [
            'mrstroz\\wavecms\\forms' => [
                'AddPermissionForm',
                'AssignRoleForm',
                'LoginForm',
                'RequestPasswordResetForm',
                'ResetPasswordForm',
            ],
            'mrstroz\\wavecms\\models' => [
                'AuthAssignment',
                'AuthItem',
                'AuthItemChild',
                'AuthRule',
                'Message',
                'SourceMessage',
                'User',
            ],
            'mrstroz\\wavecms\\models\\query' => [
                'MessageQuery',
                'SourceMessageQuery',
                'UserQuery',
            ],
            'mrstroz\\wavecms\\models\\search' => [
                'SourceMessageSearch',
                'UserSearch',
            ]
        ];

        $mapping = array_merge($defaultClassMap, $module->classMap);

        foreach ($mapping as $name => $definition) {
            $map[$this->getContainerRoute($routes, $name) . "\\$name"] = $definition;
        }

        $di = Yii::$container;

        foreach ($map as $class => $definition) {
            /** Check if definition does not exist in container. */
            if (!$di->has($class)) {
                $di->set($class, $definition);
            }
        }

    }

    /**
     * Init left navigation
     */
    protected function initNavigation()
    {
        Yii::$app->params['nav']['wavecms_dashboard'] = [
            'label' => FontAwesome::icon('home') . Yii::t('wavecms/main', 'Dashboard'),
            'url' => ['/'],
            'position' => 500
        ];

        Yii::$app->params['nav']['wavecms_user'] = [
            'label' => FontAwesome::icon('users') . Yii::t('wavecms/user', 'Users'),
            'url' => 'javascript: ;',
            'options' => [
                'class' => 'drop-down'
            ],
            'permission' => 'wavecms-user',
            'position' => 9000,
            'items' => [
                [
                    'label' => FontAwesome::icon('user') . Yii::t('wavecms/user', 'List of users'),
                    'url' => ['/wavecms/user/index']
                ],
                [
                    'label' => FontAwesome::icon('key') . Yii::t('wavecms/user', 'Roles'),
                    'url' => ['/wavecms/role/index']
                ],
            ]
        ];

        Yii::$app->params['nav']['wavecms_settings'] = [
            'label' => FontAwesome::icon('cog') . Yii::t('wavecms/main', 'Settings'),
            'url' => 'javascript: ;',
            'options' => [
                'class' => 'drop-down'
            ],
            'permission' => 'wavecms-settings',
            'position' => 10000,
            'items' => [
                [
                    'label' => FontAwesome::icon('flag') . Yii::t('wavecms/main', 'Translations'),
                    'url' => ['/wavecms/translation/index']
                ],
                [
                    'label' => FontAwesome::icon('database') . Yii::t('wavecms/main', 'Cache'),
                    'url' => ['/wavecms/settings/cache']
                ]
            ]
        ];
    }


    /**
     * Get container route for class name
     * @param array $routes
     * @param $name
     * @throws Exception
     * @return int|string
     */
    private function getContainerRoute(array $routes, $name)
    {
        foreach ($routes as $route => $names) {
            if (in_array($name, $names, false)) {
                return $route;
            }
        }
        throw new Exception("Unknown configuration class name '{$name}'");
    }
}