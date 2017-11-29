# yii2-wavecms
Yii2 Wavecms

**It is recommended to install on [Yii 2 Advanced Project Template](https://github.com/yiisoft/yii2-app-advanced)**

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Run

```
composer require --prefer-source "mrstroz/yii2-wavecms" "dev-master"
```

or add

```
"mrstroz/yii2-wavecms": "dev-master"
```

to the require section of your `composer.json` file.


Required steps
--------------

1. Update `backend/config/main.php` (Yii2 advanced template) 
```php
'bootstrap' => [
    // ...
    'mrstroz\wavecms\Bootstrap',
],
'modules' => [
    // ...
    
    'wavecms' => [
        'class' => 'mrstroz\wavecms\Module',
        /*
         * Overwrite model classes and form views
         'models' => [
            'User' => 'mrstroz\wavecms\models\User',
         ],
         */
    ],
],

'components' => [
    // ...
    'user' => [
        'identityClass' => 'mrstroz\wavecms\models\User', //Change identity class
        // ...
    ],
    'wavecms' => [
        'class' => 'mrstroz\wavecms\WavecmsComponent',
        'languages' => ['en','pl'] //Edited languages in CMS
    ],
    'cacheFrontend' => [
        'class' => 'yii\caching\FileCache',
        'cachePath' => Yii::getAlias('@frontend') . '/runtime/cache'
    ],
    'settings' => [
        'class' => 'yii2mod\settings\components\Settings',
    ],
    'i18n' => [
        'translations' => [
            'yii2mod.settings' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@yii2mod/settings/messages',
            ],
            // ...
        ],
    ],
]

```

2. Uncomment `urlManager` section in `backend/config/main.php` and add .htaccess

3. Update `common/config/main.php` (Yii2 advanced template) 
```php
'components' => [
    'authManager' => [
        'class' => 'yii\rbac\DbManager',
    ],
]

```

4. Run migration 

Add the `migrationPath` in `console/config/main.php` and run `yii migrate`:

```php
// Add migrationPaths to console config:
'controllerMap' => [
    'migrate' => [
        'class' => 'yii\console\controllers\MigrateController',
        'migrationPath' => [
            '@app/migrations',
            '@yii/rbac/migrations/',
            '@yii/i18n/migrations/',
            '@wavecms/migrations/',
            '@vendor/yii2mod/yii2-settings/migrations/'    
        ],
    ],
],
```

Or run migrates directly

```yii
yii migrate --migrationPath=@yii/rbac/migrations
yii migrate --migrationPath=@yii/i18n/migrations/
yii migrate --migrationPath=@vendor/mrstroz/yii2-wavecms/migrations
yii migrate --migrationPath=@vendor/yii2mod/yii2-settings/migrations
```

5. Update `console/config/main.php` (Yii2 advanced template)
```php
'bootstrap' => [
    // ...
    'mrstroz\wavecms\Bootstrap'
],
'modules' => [
    // ...
    'wavecms' => [
        'class' => 'mrstroz\wavecms\Module'
    ],
],
```

6. Add new WaveCMS user
```
yii wavecms/create [email] [password]
```

Optional steps
--------------

1. For **shared hosting**, copy and replace `environments` folder from `vendor/mrstroz/wavecms` and type `php init`. Frontend url: `/public`, Backend url: `/public/admin`
2. Use **[themes](http://www.yiiframework.com/doc-2.0/guide-output-theming.html)** for better code structure.
```php
'components' => [
    // ...
    'view' => [
        'theme' => [
            'basePath' => '@app/themes/basic',
            'baseUrl' => '@web/themes/basic',
            'pathMap' => [
                '@app/views' => '@app/themes/basic',
            ],
        ],
    ],
    // ...
],
```

Multilingual
--------------
1. Many languages can be handle by [yii2-localeurls](https://github.com/codemix/yii2-localeurls). Follow all steps from Locale Urls installation.

2. Change CMS languages in `backend/config/main.php` component
```php
'components' => [
    // ...
    'wavecms' => [
        'class' => 'mrstroz\wavecms\WavecmsComponent',
        'languages' => ['en','pl'] //Edited languages in CMS
    ],
    // ...
]
```

3. Configure message sources [see docs](http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html#2-configure-one-or-multiple-message-sources)
```php
'components' => [
    // ...
    'i18n' => [
        'translations' => [
            '*' => [
                'class' => 'yii\i18n\DbMessageSource',
            ],
        ],
    ],
],
```

4. Use `message` command to add translations added in templates
```
yii message/config-template path/to/config.php
yii message path/to/config.php
```

Available WaveCMS modules
-------------------------

[Page](https://github.com/mrstroz/yii2-wavecms-page) (home page, text pages, menu) - https://github.com/mrstroz/yii2-wavecms-page

[Example](https://github.com/mrstroz/yii2-wavecms-example) (example module) - https://github.com/mrstroz/yii2-wavecms-example


[Form](https://github.com/mrstroz/yii2-wavecms-form) (form module) - https://github.com/mrstroz/yii2-wavecms-form


