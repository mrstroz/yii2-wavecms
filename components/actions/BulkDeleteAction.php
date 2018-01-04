<?php

namespace mrstroz\wavecms\components\actions;

use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\db\ActiveRecord;

/**
 * Bulk delete action
 */
class BulkDeleteAction extends Action
{

    /**
     * @var Controller $controller
     */
    public $controller;


    /**
     * @return mixed
     * @throws \yii\db\StaleObjectException
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $this->_checkConfig();

        /** @var array $selection Get selected ID's */
        $selection = Yii::$app->request->post('selection');

        if ($selection) {
            foreach ($selection as $item) {
                /**
                 * Fetch item and delete
                 * @var ActiveRecord $model
                 */
                $query = clone $this->query;
                $model = $query->andWhere([$this->tableName . '.id' => $item])->one();
                $model->delete();
            }

            Flash::message(
                'bulk-delete',
                'success',
                ['message' => Yii::t('wavecms/main', 'Elements has been deleted')]
            );
        } else {
            Flash::message(
                'bulk-delete',
                'warning',
                ['message' => Yii::t('wavecms/main', 'Elements has not been choosed')]
            );
        }

        return $this->controller->redirect(Yii::$app->request->referrer);
    }
}