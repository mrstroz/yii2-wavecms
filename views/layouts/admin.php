<?php

/**
 * @var string $content
 * @var \yii\web\View $this
 */

use mrstroz\wavecms\asset\WavecmsAsset;
use mrstroz\wavecms\components\helpers\FontAwesome;
use mrstroz\wavecms\components\helpers\NavHelper;
use mrstroz\wavecms\components\widgets\Copyright;
use yii\bootstrap\ButtonGroup;
use yii\helpers\Html;
use yii\widgets\Menu;

$bundle = WavecmsAsset::register($this);

?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link rel="icon" type="image/png" href="<?php echo $bundle->baseUrl . '/img/favicon.png'; ?>">

    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<?php $this->beginBody(); ?>
<body>

<?php echo $this->render('partials/_growl.php'); ?>


<div class="container-fluid">
    <div class="row">
        <?php if (!Yii::$app->user->isGuest): ?>
            <div class="col-sm-3 col-md-2 sidebar">
                <h3 class="brand">
                    <?php echo Html::img($bundle->baseUrl . '/img/logo.svg', ['alt' => 'waveCMS']); ?>
                    wave<strong>CMS</strong>
                </h3>

                <a href="javascript:;" class="mobile-menu-btn">
                    <span></span>
                    <span></span>
                    <span></span>
                </a>

                <div class="mobile-menu-outer">
                    <?php
                    NavHelper::sortNav(Yii::$app->params['nav']);
                    NavHelper::applyActive(Yii::$app->params['nav']);
                    NavHelper::applyVisible(Yii::$app->params['nav']);

                    echo Menu::widget(
                        [
                            'items' => Yii::$app->params['nav'],
                            'options' => [
                                'class' => 'nav'
                            ],
                            'activeCssClass' => 'active opened',
                            'activateParents' => true,
                            'encodeLabels' => false,
                            'submenuTemplate' => "\n<ul class='nav nav-submenu'>\n{items}\n</ul>\n"
                        ]
                    );

                    ?>

                    <div class="clearfix">
                        <?php

                        echo ButtonGroup::widget([
                            'buttons' => Yii::$app->wavecms->languageButtons(),
                            'options' => [
                                'class' => 'uppercase'
                            ]
                        ]);

                        echo ButtonGroup::widget([
                            'buttons' => [
                                Html::a(
                                    FontAwesome::icon('user-circle'),
                                    ['/my-account'],
                                    [
                                        'class' => 'btn btn-sm btn-light-gray',
                                        'title' => Yii::t('wavecms/user', 'My account')
                                    ]
                                ),
                                Html::a(
                                    FontAwesome::icon('sign-out-alt'),
                                    ['/logout'],
                                    [
                                        'class' => 'btn btn-sm btn-light-gray',
                                        'title' => Yii::t('wavecms/user', 'Logout')
                                    ]
                                ),
                            ]
                        ]);

                        ?>
                    </div>
                </div>

            </div>
        <?php endif; ?>

        <div class="col-sm-9 col-md-10 main">

            <?php /*
            <div class="row">
                <?php
                NavBar::begin([
                    'options' => [
                        'class' => 'navbar-default header'
                    ],
                    'innerContainerOptions' => [
                        'class' => 'container-fluid'
                    ]
                ]);

                echo Nav::widget([
                    'items' => [
                        ['label' => FontAwesome::icon('user') . 'Your account', 'url' => ['/site/index']],
                        ['label' => FontAwesome::icon('sign-out') . 'Logout', 'url' => ['/site/about']],
                    ],
                    'encodeLabels' => false,
                    'options' => ['class' => 'navbar-nav navbar-right'],
                ]);

                NavBar::end();

                ?>
            </div>
            */ ?>


            <div class="row">
                <div class="col-md-8">
                    <h1 class="header-title"><?= $this->params['h1'] ?></h1>
                </div>

                <div class="col-md-4">
                    <?php if ($this->params['buttons_top']): ?>
                        <?= ButtonGroup::widget([
                            'buttons' => $this->params['buttons_top'],
                            'options' => [
                                'class' => 'btn-group-top'
                            ]
                        ]); ?>
                    <?php endif; ?>
                </div>
            </div>

            <?= $content ?>

            <?php if ($this->params['buttons_btm']): ?>
                <?= ButtonGroup::widget([
                    'buttons' => $this->params['buttons_btm'],
                    'options' => [
                        'class' => 'btn-group-btm'
                    ]
                ]); ?>
            <?php endif; ?>

            <?= Copyright::widget(); ?>
        </div>
    </div>
</div>

<!-- /footer content -->
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
