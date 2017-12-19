# yii2-wavecms
Yii2 WaveCMS

**It is recommended to install on [Yii 2 Advanced Project Template](https://github.com/yiisoft/yii2-app-advanced)**

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Run

```
composer require --prefer-source "mrstroz/yii2-wavecms" "~0.1.0"
```

or add

```
"mrstroz/yii2-wavecms": "~0.1.0"
```

to the require section of your `composer.json` file.


Required steps
--------------

1. Update `backend/config/main.php` (Yii2 advanced template) 
```php
'modules' => [
    // ...   
    'wavecms' => [
        'class' => 'mrstroz\wavecms\Module',
        'languages' => ['en','pl']
    ],
],

'components' => [
    // ...
    'user' => [
        'identityClass' => 'mrstroz\wavecms\models\User', //Change identity class
        // ...
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

Overriding classes
------------------
Classes can be overridden by:
1. `classMap` attribute for WaveCMS module
```php
'modules' => [
    // ...   
    'wavecms' => [
        'class' => 'mrstroz\wavecms\Module',
        'languages' => ['en','pl'],
        'classMap' => [
            'User' => \common\models\User::class
        ]
    ],
],
```

2. Yii2 Dependency Injection configuration in `backend/config/main.php`
```php
'container' => [
    'definitions' => [
        mrstroz\wavecms\models\User::class => common\models\User::class
    ],
],
```

Overriding controllers
----------------------
Use `controllerMap` attribute for WaveCMS module to override controllers
```php
'modules' => [
    // ...   
    'wavecms' => [
        'class' => 'mrstroz\wavecms\Module',
        'languages' => ['en','pl'],
        'controllerMap' => [
            'user' => 'backend\controllers\UserController'
        ]
    ],
],
```

Overriding views
--------------
Use **[themes](http://www.yiiframework.com/doc-2.0/guide-output-theming.html)** for override views
```php
'components' => [
    // ...
    'view' => [
        'theme' => [
            'basePath' => '@app/themes/basic',
            'baseUrl' => '@web/themes/basic',
            'pathMap' => [
                '@wavecms/views' => '@app/themes/basic/wavecms',
            ],
        ],
    ],
    // ...
],
```

Multilingual
--------------
1. Many languages can be handle by [yii2-localeurls](https://github.com/codemix/yii2-localeurls). Follow all steps from Locale Urls installation.

2. Set CMS languages in `backend/config/main.php` for WaveCMS module
```php
'modules' => [
    // ...
    'wavecms' => [
        'class' => 'mrstroz\wavecms\Module',
        'languages' => ['en','pl']
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

Shared hosting
--------------
For **shared hosting**, copy and replace `environments` folder from `vendor/mrstroz/wavecms` and type `php init`. Frontend url: `/public`, Backend url: `/public/admin`


Available WaveCMS modules
-------------------------

[Page](https://github.com/mrstroz/yii2-wavecms-page) (home page, text pages, menu) - https://github.com/mrstroz/yii2-wavecms-page

[Example](https://github.com/mrstroz/yii2-wavecms-example) (example module) - https://github.com/mrstroz/yii2-wavecms-example

[Form](https://github.com/mrstroz/yii2-wavecms-form) (form module) - https://github.com/mrstroz/yii2-wavecms-form


> ![INWAVE LOGO](http://inwave.pl/html/img/logo.png)  
> INWAVE - Internet Software House  
> [inwave.eu](http://inwave.eu/)


