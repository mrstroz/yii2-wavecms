<?php

namespace mrstroz\wavecms\components\actions;

use mrstroz\wavecms\components\behaviors\TranslateBehavior;
use mrstroz\wavecms\components\event\ModelEvent;
use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * Page action
 */
class PageAction extends Action
{

    /**
     * @var Controller $controller
     */
    public $controller;


    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidParamException
     */
    public function run()
    {
        $this->_checkConfig();

        /** Set header of page */
        $this->controller->view->params['h1'] = $this->controller->heading;
        $this->controller->view->title = $this->controller->heading;

        $query = $this->controller->query;
        /** @var ActiveRecord $model */
        $model = $query->one();

        /** Create model if not exist */
        if (!$model) {
            $model = Yii::createObject($query->modelClass);
        }

        /** Set scenario to ActiveRecord */
        if ($this->controller->scenario) {
            $model->scenario = $this->controller->scenario;
        }

        /** Set language for TranslateBehavior */
        foreach ($model->behaviors as $behavior) {
            if ($behavior instanceof TranslateBehavior) {
                $behavior->setLanguage(Yii::$app->wavecms->editedLanguage);
            }
        }

        /** Add model event and trigger them */
        $eventModel = new ModelEvent();
        $eventModel->model = $model;
        $this->controller->trigger(Controller::EVENT_AFTER_MODEL_CREATE, $eventModel);

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->validate()) {

                $this->controller->trigger(Controller::EVENT_BEFORE_MODEL_SAVE, $eventModel);
                $model->save();
                $this->controller->trigger(Controller::EVENT_AFTER_MODEL_SAVE, $eventModel);

                Flash::message(
                    'after_update',
                    'success',
                    ['message' => Yii::t('wavecms/main', 'Element has been updated')]
                );
            } else {
                Flash::message(
                    'create_error',
                    'danger',
                    ['message' => Html::errorSummary($model)]
                );
            }
        }

        $this->controller->trigger(Controller::EVENT_BEFORE_RENDER);

        return $this->controller->render($this->controller->viewForm, array(
            'model' => $model
        ));

    }

}