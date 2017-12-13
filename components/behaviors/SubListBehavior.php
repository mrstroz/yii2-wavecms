<?php

namespace mrstroz\wavecms\components\behaviors;


use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

class SubListBehavior extends Behavior
{

    public $listId;
    public $route;
    public $parentField = 'parent_id';

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete'
        ];
    }

    public function init()
    {

        if (!$this->listId)
            throw new InvalidConfigException(Yii::t('wavecms/main','Property "listId" is not defined in SubListBehavior'));


        if (!$this->route)
        throw new InvalidConfigException(Yii::t('wavecms/main','Property "route" is not defined in SubListBehavior'));


        parent::init();
    }



    public function afterDelete($event)
    {
        $routeExploded = explode('/',$this->route);
        $routeExploded[count($routeExploded)-1] = 'delete-sub-list';

        return Yii::$app->runAction(implode('/',$routeExploded), [
            'parentField' => $this->parentField,
            'parentId' => $event->sender->id,
            'parentRoute' => $this->route
        ]);
    }

}