<?php

namespace mrstroz\wavecms;


use Yii;
use yii\base\Component;
use yii\bootstrap\Html;

class WavecmsComponent extends Component
{

    public $languages = [];
    public $editedLanguage;

    public function languageButtons()
    {
        $buttons = [];
        foreach (Yii::$app->wavecms->languages as $language) {
            $class = 'btn-light-gray ';
            if (Yii::$app->wavecms->editedLanguage === $language) {
                $class = 'btn-primary';
            }

            $buttons[] = Html::a($language, ['/wavecms/language/change', 'lang' => $language], ['class' => 'btn btn-sm ' . $class]);
        }
        return $buttons;
    }

    public function languageCheckboxItems()
    {
        $items = [];
        foreach (Yii::$app->wavecms->languages as $language) {
            $items[$language] = '<span class="label label-default text-uppercase">'.$language.'</span>';
        }

        return $items;
    }

}