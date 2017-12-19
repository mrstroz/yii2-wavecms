<?php

namespace mrstroz\wavecms\components\actions;

use mrstroz\wavecms\components\event\ModelEvent;
use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Index action - display GridView with all entries form $this->controller->query model.
 */
class SettingsAction extends Action
{

    /**
     * @var Controller $controller
     */
    public $controller;


    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->controller->modelClass === null) {
            throw new InvalidConfigException('The "modelClass" property must be set.');
        }
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidParamException
     */
    public function run()
    {
        /* @var $model Model */
        $model = Yii::createObject($this->controller->modelClass);

        /** Set header of page */
        $this->controller->view->params['h1'] = $this->controller->heading;
        $this->controller->view->title = $this->controller->heading;

        /** Add model event and trigger them */
        $eventModel = new ModelEvent();
        $eventModel->model = $model;
        $this->controller->trigger(Controller::EVENT_AFTER_MODEL_CREATE, $eventModel);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $this->controller->trigger(Controller::EVENT_BEFORE_MODEL_SAVE, $eventModel);
            foreach ($model->toArray() as $key => $value) {
                Yii::$app->settings->set($model->formName(), $key, $value);
            }
            $this->controller->trigger(Controller::EVENT_AFTER_MODEL_SAVE, $eventModel);

            Flash::message(
                'after_update',
                'success',
                ['message' => Yii::t('wavecms/main', 'Element has been updated')]);

            return $this->controller->refresh();
        }

        foreach ($model->attributes() as $attribute) {
            $model->{$attribute} = Yii::$app->settings->get($model->formName(), $attribute);
        }

        $this->controller->trigger(Controller::EVENT_BEFORE_RENDER);

        return $this->controller->render($this->controller->viewForm, array(
            'model' => $model
        ));

    }

}