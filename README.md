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
'controllerMap' => [
    'elfinder' => [
        'class' => 'mihaildev\elfinder\Controller',
        'access' => ['@'],
        'disabledCommands' => ['netmount'],
        'roots' => [
            [
                'baseUrl'=>'@frontWeb',
                'basePath'=>'@frontWebroot',
                'path' => 'userfiles',
                'name' => 'Files'
            ]
        ]
    ]
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
```yii
yii migrate --migrationPath=@yii/rbac/migrations
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
            'app*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                //'basePath' => '@app/messages',
                //'sourceLanguage' => 'en-US',
                'fileMap' => [
                    'app' => 'app.php',
                    'app/error' => 'error.php',
                ],
            ],
        ],
    ],
],
```

Available WaveCMS modules
-------------------------

[Page](https://github.com/mrstroz/yii2-wavecms-page) (home page, text pages, menu) - https://github.com/mrstroz/yii2-wavecms-page

[Example](https://github.com/mrstroz/yii2-wavecms-example) (example module) - https://github.com/mrstroz/yii2-wavecms-example



