<?php

namespace mrstroz\wavecms\components\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class CheckboxListBehavior
 * @package mrstroz\wavecms\components\behaviors
 * Behavior used for save checkboxes list in WaveCMS
 */
class CheckboxListBehavior extends Behavior
{

    /**
     * @var array List of fields proceed by behavior
     */
    public $fields = [];

    /**
     * @inheritdoc
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind'
        ];
    }

    /**
     * Change array to string separate by ;
     * @param $event
     */
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

    /**
     * Change string separated by ; to array
     * @param $event
     */
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