<?php

namespace mrstroz\wavecms\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Error controller
 */
class ErrorController extends Controller
{

    public $layout = '@wavecms/views/layouts/login.php';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ]

                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


}
