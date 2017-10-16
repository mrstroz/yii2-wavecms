<?php

namespace mrstroz\wavecms\components\actions;

use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\base\Action as BaseAction;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * Action base class
 */
class Action extends BaseAction
{

    /**
     * @var Controller $controller
     */
    public $controller;

    /**
     * Function to get one model by id. Using in crete/update/page action
     * @param $id
     * @return ActiveRecord
     * @throws \yii\web\NotFoundHttpException
     */
    protected function _fetchOne($id)
    {
        $query = $this->controller->query;
        $modelClass = $query->modelClass;
        /** @var ActiveRecord $model */
        $model = $query->andWhere([$modelClass::tableName() . '.id' => $id])->one();

        if (!$model)
            throw new NotFoundHttpException(Yii::t('wavecms/base/main', 'Element not found'));

        if ($this->controller->scenario) {
            $model->scenario = $this->controller->scenario;
        }

        return $model;
    }

    /**
     * Check if $this->query is set
     * @throws \yii\base\InvalidConfigException
     */
    protected function _checkConfig()
    {
        if (!$this->controller->query)
            throw new InvalidConfigException(Yii::t('wavecms/base/main', 'The "query" property must be set.'));

        if (!$this->controller->query instanceof ActiveQuery)
            throw new InvalidConfigException(Yii::t('wavecms/base/main', 'The "query" property is not instance of ActiveQuery.'));
    }

    /**
     *
     * @return array
     */
    protected function _forwardParams()
    {
        $return = [];

        foreach ($this->controller->forwardParams as $param) {
            $return[$param] = Yii::$app->request->get($param);
        }

        return $return;
    }
}