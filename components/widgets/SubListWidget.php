<?php

namespace mrstroz\wavecms\components\widgets;

use mrstroz\wavecms\components\behaviors\SubListBehavior;
use mrstroz\wavecms\components\helpers\FontAwesome;
use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\Widget;

class SubListWidget extends Widget
{

    public $listId;
    public $model;
    public $params = [];

    public function run()
    {
        if (!$this->listId)
            throw new InvalidConfigException(Yii::t('wavecms/base/main', 'Property "listId" is not defined in SubListWidget'));

        if (!$this->model)
            throw new InvalidConfigException(Yii::t('wavecms/base/main', 'Property "model" is not defined in SubListWidget'));

        $modelBehaviors = $this->model->behaviors;

        if (is_array($modelBehaviors)) {
            foreach ($modelBehaviors as $behavior) {
                if ($behavior instanceof SubListBehavior) {
                    if ($this->listId === $behavior->listId) {
                        if (Yii::$app->controller->action->id === 'update' || Yii::$app->controller->action->id === 'page') {
                            return Yii::$app->runAction($behavior->route, [
                                'parentField' => $behavior->parentField,
                                'parentId' => $this->model->id,
                                'parentRoute' => Yii::$app->requestedRoute
                            ]);
                        }
                        return '<div class="alert alert-info">' . FontAwesome::icon('info-circle') . ' ' . Yii::t('wavecms/base/main', 'Save changes to add sub list elements') . '</div>';
                    }
                }
            }
        }

//

    }
}