<?php

namespace mrstroz\wavecms\components\behaviors;


use yii\base\Behavior;
use yii\db\ActiveRecord;

class CheckboxListBehavior extends Behavior
{

    public $fields = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind'
        ];
    }

    public function beforeUpdate($event)
    {

        if (is_array($this->fields)) {
            foreach ($this->fields as $field) {
                if (is_array($event->sender->{$field})) {
                    $event->sender->{$field} = implode(';', $event->sender->{$field});
                }
            }
        }

    }

    public function afterFind($event)
    {

        if (is_array($this->fields)) {
            foreach ($this->fields as $field) {
                if ($event->sender->{$field}) {
                    $event->sender->{$field} = explode(';', $event->sender->{$field});
                }
            }
        }

    }
}