<?php

namespace mrstroz\wavecms\components\actions;

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

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            foreach ($model->toArray() as $key => $value) {
                Yii::$app->settings->set($model->formName(), $key, $value);
            }

            Flash::message(
                'after_update',
                'success',
                ['message' => Yii::t('wavecms/base/main', 'Element has been updated')]);

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