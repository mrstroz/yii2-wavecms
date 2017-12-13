<?php

namespace mrstroz\wavecms\components\actions;

use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\db\ActiveRecord;

/**
 * Delete action
 */
class DeleteAction extends Action
{

    /**
     * @var Controller $controller
     */
    public $controller;


    /**
     * @param $id
     * @param null $parentField
     * @param null $parentId
     * @param null $parentRoute
     * @return mixed
     * @throws \yii\db\StaleObjectException
     * @throws \Exception
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function run($id, $parentField = null, $parentId = null, $parentRoute = null)
    {
        $this->_checkConfig();

        /** Set return url */
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

        /**
         * Fetch item and delete
         * @var ActiveRecord $model
         */
        $model = $this->_fetchOne($id);
        $model->delete();

        Flash::message(
            'delete',
            'success',
            ['message' => Yii::t('wavecms/main', 'Element has been deleted')]
        );

        return $this->controller->redirect($this->controller->returnUrl);
    }
}