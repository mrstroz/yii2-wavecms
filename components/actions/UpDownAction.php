<?php

namespace mrstroz\wavecms\components\actions;

use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

/**
 * UpDown action - action used for sort items on list
 */
class UpDownAction extends Action
{

    /**
     * @var Controller $controller
     */
    public $controller;


    public function run($id, $dir)
    {
        $upDownQuery = clone $this->controller->query;
        $model = $this->_fetchOne($id);

        if (!$model)
            throw new NotFoundHttpException(Yii::t('wavecms/main', 'Element not found'));

        $compare = '<';
        $order = 'sort DESC';
        if ($dir === 'down') {
            $compare = '>';
            $order = 'sort ASC';
        }

        $query = $this->controller->query;
        /** @var ActiveRecord $modelClass */
        $modelClass = $query->modelClass;
        /** @var ActiveRecord $modelSort */
        $modelSort = $upDownQuery->andWhere([$compare, $modelClass::tableName() . '.sort', $model->sort])->orderBy($order)->one();

        if ($modelSort) {
            $sort = $modelSort->sort;
            $modelSort->sort = $model->sort;
            $model->sort = $sort;
            $modelSort->save();
            $model->save();

            if ($dir === 'up') {
                Flash::message(
                    'up_down',
                    'success',
                    ['message' => Yii::t('wavecms/main', 'Elements has been moved up')]
                );
            } else {
                Flash::message(
                    'up_down',
                    'success',
                    ['message' => Yii::t('wavecms/main', 'Elements has been moved down')]
                );
            }

        } else {
            if ($dir === 'up') {
                Flash::message(
                    'up_down',
                    'warning',
                    ['message' => Yii::t('wavecms/main', 'Elements cannot be moved up')]
                );
            } else {
                Flash::message(
                    'up_down',
                    'warning',
                    ['message' => Yii::t('wavecms/main', 'Elements cannot be moved down')]
                );
            }
        }

        return $this->controller->redirect(Yii::$app->request->referrer);
    }

}