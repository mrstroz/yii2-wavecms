<?php

namespace mrstroz\wavecms\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class SettingsBehavior extends Behavior
{

    /**
     * @var array the list of settings attributes.
     */
    public $settingsAttributes = [];


    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'find',
            ActiveRecord::EVENT_AFTER_INSERT => 'update',
            ActiveRecord::EVENT_AFTER_UPDATE => 'update',
            ActiveRecord::EVENT_AFTER_DELETE => 'delete'
        ];
    }

    public function find($event)
    {
        $lang = Yii::$app->language;
        if (Yii::$app->id === 'app-backend') {
            $lang = Yii::$app->wavecms->editedLanguage;
        }

        if (is_array($this->settingsAttributes)) {
            foreach ($this->settingsAttributes as $attribute) {
                $key = $event->sender->primaryKey;
                if (is_array($key)) {
                    $key = implode('_', $key);
                }
                $section = $event->sender->formName() . '_' . $key . '_' . $lang;
                $value = Yii::$app->settings->get($section, $attribute);
                $event->sender->{$attribute} = $value;
            }
        }
    }

    public function update($event)
    {
        $lang = Yii::$app->language;
        if (Yii::$app->id === 'app-backend') {
            $lang = Yii::$app->wavecms->editedLanguage;
        }

        if (is_array($this->settingsAttributes)) {
            foreach ($this->settingsAttributes as $attribute) {
                $key = $event->sender->primaryKey;
                if (is_array($key)) {
                    $key = implode('_', $key);
                }
                $section = $event->sender->formName() . '_' . $key . '_' . $lang;
                $value = $event->sender->{$attribute};
                $oldValue = Yii::$app->settings->get($section, $attribute);
                if ($value !== $oldValue) {
                    Yii::$app->settings->set($section, $attribute, $value);
                }
            }
        }
    }

    public function delete($event)
    {

        //TODO: Check language on frontend

        if (is_array($this->settingsAttributes)) {
            foreach ($this->settingsAttributes as $attribute) {
                foreach (Yii::$app->wavecms->languages as $language) {
                    $key = $event->sender->primaryKey;
                    if (is_array($key)) {
                        $key = implode('_', $key);
                    }
                    $section = $event->sender->formName() . '_' . $key . '_' . $language;
                    Yii::$app->settings->remove($section, $attribute);
                }
            }
        }
    }

    public function getSettings($attribute)
    {

        $key = $this->owner->primaryKey;
        if (is_array($key)) {
            $key = implode('_', $key);
        }

        $lang = Yii::$app->language;
        if (Yii::$app->id === 'app-backend') {
            $lang = Yii::$app->wavecms->editedLanguage;
        }

        $section = $this->owner->className() . '_' . $key . '_' . $lang;
        echo Yii::$app->settings->get($section, $attribute);
        return Yii::$app->settings->get($section, $attribute);
    }
}