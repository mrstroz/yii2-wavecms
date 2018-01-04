<?php

namespace mrstroz\wavecms\components\actions;

use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\db\ActiveRecord;

/**
 * Bulk unpublish action
 */
class BulkUnpublishAction extends Action
{

    /**
     * @var Controller $controller
     */
    public $controller;


    /**
     * @return mixed
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
                $model->{$this->controller->publishColumn} = 0;
                $model->save(false);
            }

            Flash::message(
                'bulk-publish',
                'success',
                ['message' => Yii::t('wavecms/main', 'Elements has been unpublished')]
            );
        } else {
            Flash::message(
                'bulk-publish',
                'warning',
                ['message' => Yii::t('wavecms/main', 'Elements has not been choosed')]
            );
        }

        return $this->controller->redirect(Yii::$app->request->referrer);
    }

}