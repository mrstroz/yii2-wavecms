<?php

namespace mrstroz\wavecms\components\actions;

use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\db\ActiveRecord;
use yii\web\Response;

/**
 * Publish action
 */
class PublishAction extends Action
{

    /**
     * @var Controller $controller
     */
    public $controller;

    /**
     * @param $id
     * @return array
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function run($id)
    {
        $this->_checkConfig();

        Yii::$app->response->format = Response::FORMAT_JSON;


        /** @var ActiveRecord $model */
        $model = $this->_fetchOne($id);

        if ($model->{$this->controller->publishColumn} === 1) {
            $model->{$this->controller->publishColumn} = 0;
        } else {
            $model->{$this->controller->publishColumn} = 1;
        }

        $model->save(false);

        return ['publish' => $model->{$this->controller->publishColumn}, 'model' => $model->toArray()];
    }

}