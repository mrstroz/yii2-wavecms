<?php

namespace mrstroz\wavecms\controllers;

use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\base\InvalidParamException;

class LanguageController extends Controller
{

    public function init()
    {
        $this->actionsDisabledFromAccessControl = [
            'change'
        ];

        parent::init();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors(); // TODO: Change the autogenerated stub

        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => [
                'change'
            ],
            'roles' => [
                '@'
            ],
        ];

        return $behaviors;
    }

    public function actionChange($lang)
    {

        if (!in_array($lang, Yii::$app->wavecms->languages))
            throw new InvalidParamException(Yii::t('wavecms/base/main', 'Wrong language'));

        Yii::$app->session->set('editedLanguage',$lang);

        Flash::message('language_change', 'success', ['message' => Yii::t('wavecms/base/main', 'Edited language has been changed to {lang}',['lang' => $lang])]);

        return $this->redirect(Yii::$app->request->referrer);


    }
}