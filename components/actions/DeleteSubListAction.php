<?php

namespace mrstroz\wavecms\components\actions;

use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\db\ActiveRecord;

/**
 * Delete SubList action
 */
class DeleteSubListAction extends Action
{

    /**
     * @var Controller $controller
     */
    public $controller;

    /**
     * @param $parentField
     * @param $parentId
     * @param $parentRoute
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function run($parentField, $parentId, $parentRoute)
    {
        $this->_checkConfig();

        $query = $this->controller->query;
        /** @var ActiveRecord $modelClass */
        $modelClass = $query->modelClass;
        /** @var ActiveRecord $model */
        $models = $query->andWhere([$modelClass::tableName() . '.' . $parentField => $parentId])->all();

        if ($models) {
            foreach ($models as $model) {
                if ($this->controller->scenario) {
                    $model->scenario = $this->controller->scenario;
                }
                $model->delete();
            }
        }
        Flash::message(
            'delete_sub_list',
            'success',
            ['message' => Yii::t('wavecms/main', 'Elements from list "{heading}" has been deleted', ['heading' => $this->controller->heading])]
        );
    }
}