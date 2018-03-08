<?php

namespace mrstroz\wavecms\components\actions;

use mrstroz\wavecms\components\event\ModelEvent;
use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\caching\Cache;
use yii\db\ActiveRecord;
use yii2mod\settings\components\Settings;

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

        foreach ($model->attributes() as $attribute) {
            $model->setOldAttribute($attribute, Yii::$app->settings->get($model->formName(), $attribute));
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->trigger(ActiveRecord::EVENT_BEFORE_UPDATE);
            $this->controller->trigger(Controller::EVENT_BEFORE_MODEL_SAVE, $eventModel);

            foreach ($model->toArray() as $key => $value) {
                if (!$value) {
                    Yii::$app->settings->remove($model->formName(), $key);
                } else {
                    Yii::$app->settings->set($model->formName(), $key, $value);
                }
            }
            $this->controller->trigger(Controller::EVENT_AFTER_MODEL_SAVE, $eventModel);

            if (Yii::$app->cacheFrontend instanceof Cache) {
                /** @var Settings $settingsComponent */
                $settingsComponent = Yii::createObject(Settings::class);
                Yii::$app->cacheFrontend->delete($settingsComponent->cacheKey);
            }

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