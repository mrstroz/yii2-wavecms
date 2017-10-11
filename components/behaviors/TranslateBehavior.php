<?php

namespace mrstroz\wavecms\components\behaviors;


use dosamigos\translateable\TranslateableBehavior;
use Yii;

class TranslateBehavior extends TranslateableBehavior
{

    public function afterFind($event)
    {
        if (isset(Yii::$app->wavecms)) {
            $event->sender->language = Yii::$app->wavecms->editedLanguage;
            $this->setLanguage(Yii::$app->wavecms->editedLanguage);
        }
        parent::afterFind($event);
    }

    public function afterInsert($event)
    {
        if (isset(Yii::$app->wavecms)) {
            $event->sender->language = Yii::$app->wavecms->editedLanguage;
            $this->setLanguage(Yii::$app->wavecms->editedLanguage);
        }
        parent::afterInsert($event);
    }

    public function afterUpdate($event)
    {
        if (isset(Yii::$app->wavecms)) {
            $event->sender->language = Yii::$app->wavecms->editedLanguage;
            $this->setLanguage(Yii::$app->wavecms->editedLanguage);
        }
        parent::afterUpdate($event);
    }
}