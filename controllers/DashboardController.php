<?php

namespace mrstroz\wavecms\controllers;

use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\helpers\VarDumper;

class DashboardController extends Controller
{

    public function actions()
    {
        $actions = parent::actions();

        return [];
    }

    public function init()
    {
        $this->actionsDisabledFromAccessControl = [
            'index'
        ];

        parent::init();
    }

    public function actionIndex()
    {
        $this->view->params['h1'] = Yii::t('wavecms/base/main', 'Dashboard');

        $this->view->title = $this->view->params['h1'];
        return $this->render('index');
    }
}