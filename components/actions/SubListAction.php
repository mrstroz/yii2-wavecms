<?php

namespace mrstroz\wavecms\components\actions;

use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * SubList action - display GridView for SubListWidget
 */
class SubListAction extends Action
{

    /**
     * @var Controller $controller
     */
    public $controller;

    /**
     * @param $parentField
     * @param $parentId
     * @param $parentRoute
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidParamException
     */
    public function run($parentField, $parentId, $parentRoute)
    {
        $this->_checkConfig();
        $this->controller->layout = false;

        /** Set sublist buttons */
        array_unshift(
            $this->controller->view->params['buttons_sublist'],
            Html::a(
                Yii::t('wavecms/main', 'Create new'),
                array_merge(['create', 'parentField' => $parentField, 'parentId' => $parentId, 'parentRoute' => $parentRoute], $this->_forwardParams()),
                ['class' => 'btn btn-primary btn-sm btn-sub-list']
            )
        );

        /** Add 'where' to query for SubList (children) items */
        $this->controller->query->andWhere([$parentField => $parentId]);

        /** Create dataProvider based on $this->controller->query if not exist */
        if (!$this->controller->dataProvider) {
            $this->controller->dataProvider = new ActiveDataProvider([
                'query' => $this->controller->query,
            ]);
        }

        /** Extra settings for sortable list */
        if ($this->controller->sort) {
            /** @var ActiveRecord $modelClass */
            $modelClass = $this->controller->query->modelClass;
            $this->controller->query->orderBy($modelClass::tableName() . '.sort');

            $this->controller->dataProvider->sort = false;
        }

        /** Add filter model */
        if ($this->controller->filterModel) {
            $this->controller->filterModel->search($this->controller->dataProvider);
        }

        $this->controller->trigger(Controller::EVENT_BEFORE_RENDER);

        return $this->controller->render($this->controller->viewSubList, array(
            'dataProvider' => $this->controller->dataProvider,
            'filterModel' => $this->controller->filterModel,
            'columns' => $this->controller->columns,
            'sort' => $this->controller->sort
        ));
    }

}