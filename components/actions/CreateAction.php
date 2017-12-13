<?php

namespace mrstroz\wavecms\components\actions;

use mrstroz\wavecms\components\behaviors\TranslateBehavior;
use mrstroz\wavecms\components\event\ModelEvent;
use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\helpers\NavHelper;
use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * Create action
 */
class CreateAction extends Action
{

    /**
     * @var Controller $controller
     */
    public $controller;

    /**
     * @param null $parentField
     * @param null $parentId
     * @param null $parentRoute
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidParamException
     */
    public function run($parentField = null, $parentId = null, $parentRoute = null)
    {
        $this->_checkConfig();

        /** Set header of page */
        $this->controller->view->params['h1'] = $this->controller->heading;
        $this->controller->view->title = $this->controller->heading;

        /** Set return url (href for back button or save & return button) */
        if (!$this->controller->returnUrl) {
            if ($parentField && $parentId && $parentRoute) {
                $this->controller->returnUrl = array_merge(
                    ['/' . ltrim(urldecode($parentRoute), '/'), 'id' => $parentId],
                    $this->_forwardParams()
                );
            } else {
                $this->controller->returnUrl = array_merge(
                    ['index'],
                    $this->_forwardParams()
                );
            }
        }

        /** Set top buttons */
        array_unshift(
            $this->controller->view->params['buttons_top'],
            Html::a(
                Yii::t('wavecms/main', 'Return'),
                $this->controller->returnUrl,
                ['class' => 'btn btn-default']
            )
        );

        /**  Set index element as active in left navigation */
        NavHelper::$active = $this->controller->activeActions;


        /** @var ActiveRecord $model */
        $model = Yii::createObject($this->controller->query->modelClass);
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

        /** Validate & save model */
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->validate()) {

                if ($parentField && $parentId && $parentRoute) {
                    $model->{$parentField} = $parentId;
                }

                $this->controller->trigger(Controller::EVENT_BEFORE_MODEL_SAVE, $eventModel);
                $model->save();
                $this->controller->trigger(Controller::EVENT_AFTER_MODEL_SAVE, $eventModel);

                Flash::message(
                    'after_create',
                    'success',
                    ['message' => Yii::t('wavecms/main', 'Element has been created')]
                );

                if (Yii::$app->request->post('save_and_return')) {
                    return $this->controller->redirect($this->controller->returnUrl);
                }

                return $this->controller->redirect(array_merge([
                    'update',
                    'id' => $model->id,
                    'parentField' => $parentField,
                    'parentId' => $parentId,
                    'parentRoute' => $parentRoute,
                ], $this->_forwardParams()));

            }

            Flash::message(
                'create_error',
                'danger',
                ['message' => Html::errorSummary($model)]
            );
        }

        $this->controller->trigger(Controller::EVENT_BEFORE_RENDER);

        return $this->controller->render($this->controller->viewForm, array(
            'model' => $model
        ));
    }

}