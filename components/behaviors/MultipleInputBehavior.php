<?php

namespace mrstroz\wavecms\components\behaviors;

use Throwable;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * Behavior for unclead/yii2-multiple-input
 * More info https://github.com/unclead/yii2-multiple-input
 */
class MultipleInputBehavior extends Behavior
{

    public $attribute;
    public $viaRelation;
    public $parentKeyColumn;
    public $sortColumn = 'sort';


    public function init()
    {

        if (!$this->attribute) {
            throw new InvalidConfigException(Yii::t('wavecms/main', 'Property "{property}" is not defined in {class}', ['property' => 'attribute', 'class' => __CLASS__]));
        }

        if (!$this->parentKeyColumn) {
            throw new InvalidConfigException(Yii::t('wavecms/main', 'Property "{property}" is not defined in {class}', ['property' => 'parentKeyColumn', 'class' => __CLASS__]));
        }

        if (!$this->viaRelation) {
            throw new InvalidConfigException(Yii::t('wavecms/main', 'Property "{property}" is not defined in {class}', ['property' => 'viaRelation', 'class' => __CLASS__]));
        }

        parent::init();
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete'
        ];
    }

    /**
     * @param $event
     * @throws InvalidConfigException
     * @throws Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function beforeUpdate($event)
    {

        /** @var ActiveRecord $sender */
        $sender = $event->sender;

        $ids = [];
        if (is_array($sender->{$this->attribute})) {
            foreach ($sender->{$this->attribute} as $column) {
                if (isset($column['id'])) {
                    if (is_numeric($column['id'])) {
                        $ids[] = $column['id'];
                    }
                } else {
                    throw new InvalidConfigException(Yii::t('wavecms/main', '"id" column not found in '));
                }
            }
        }

        foreach ($sender->getRelation($this->viaRelation)->andWhere(['NOT IN', 'id', $ids])->all() as $one) {
            $one->delete();
        }

        $className = $sender->getRelation($this->viaRelation)->modelClass;

        $i = 1;
        if (is_array($sender->{$this->attribute})) {
            foreach ($sender->{$this->attribute} as $column) {

                $item = null;
                if (is_numeric($column['id'])) {
                    $item = $sender->getRelation($this->viaRelation)->andWhere(['=', 'id', $column['id']])->one();
                }

                if (!$item) {
                    $item = new $className();
                    $item->{$this->parentKeyColumn} = $sender->primaryKey;
                }

                foreach ($column as $key => $value) {
                    $item->{$key} = $value;
                }

                if ($this->sortColumn) {
                    $item->{$this->sortColumn} = $i;
                }
                $item->save();

                $i++;
            }
        }

    }

    public function afterFind($event)
    {
        /** @var ActiveRecord $sender */
        $sender = $event->sender;

        $query = $sender->getRelation($this->viaRelation);
        if ($this->sortColumn) {
            $query->orderBy($this->sortColumn);
        }

        $items = $query->asArray()->all();
        $sender->{$this->attribute} = $items;
    }

    public function afterDelete($event)
    {
        /** @var ActiveRecord $sender */
        $sender = $event->sender;
        foreach ($sender->getRelation($this->viaRelation)->all() as $one) {
            $one->delete();
        }
    }
}