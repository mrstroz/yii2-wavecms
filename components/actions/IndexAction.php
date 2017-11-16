<?php

namespace mrstroz\wavecms\components\actions;

use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * Index action - display GridView with all entries form $this->controller->query model.
 */
class IndexAction extends Action
{

    /**
     * @var Controller $controller
     */
    public $controller;

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidParamException
     */
    public function run()
    {
        $this->_checkConfig();

        /** Set header of page */
        $this->controller->view->params['h1'] = $this->controller->heading;
        $this->controller->view->title = $this->controller->heading;

        /** Set top buttons */
        array_unshift(
            $this->controller->view->params['buttons_top'],
            Html::a(
                Yii::t('wavecms/base/main', 'Create new'),
                array_merge(['create'], $this->_forwardParams()),
                ['class' => 'btn btn-primary']
            )
        );

        /** Create dataProvider based on $this->controller->query if not exist */
        if (!$this->controller->dataProvider) {
            $this->controller->dataProvider = new ActiveDataProvider([
                'query' => $this->controller->query
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

        return $this->controller->render($this->controller->viewIndex, array(
            'dataProvider' => $this->controller->dataProvider,
            'filterModel' => $this->controller->filterModel,
            'columns' => $this->controller->columns,
            'sort' => $this->controller->sort
        ));
    }

}