<?php

namespace mrstroz\wavecms\components\widgets;


use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ActiveField extends \yii\bootstrap\ActiveField
{

    public $checkboxTemplate = "<div class=\"checkbox\">\n{input}\n{beginLabel}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";
    public $radioTemplate = "<div class=\"radio\">\n{input}\n{beginLabel}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";

    public $horizontalCheckboxTemplate = "{beginWrapper}\n<div class=\"checkbox\">\n{input}\n{beginLabel}\n{labelTitle}\n{endLabel}\n</div>\n{error}\n{endWrapper}\n{hint}";
    public $horizontalRadioTemplate = "{beginWrapper}\n<div class=\"radio\">\n{input}\n{beginLabel}\n{labelTitle}\n{endLabel}\n</div>\n{error}\n{endWrapper}\n{hint}";

    public function checkboxList($items, $options = [])
    {
        $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
        $encode = ArrayHelper::getValue($options, 'encode', true);

        $myOptions = [
            'item' => function ($index, $label, $name, $checked, $value) use ($itemOptions, $encode) {
                $label = $encode ? Html::encode($label) : $label;
                $id = $name . '_' . $index;
                $options = array_merge([
//                    'label' => $encode ? Html::encode($label) : $label,
                    'value' => $value,
                    'id' => $id
                ], $itemOptions);

                $class = 'checkbox';
                if ($this->inline) {
                    $class .= ' checkbox-inline';
                }

//                return '<div class="checkbox">' . Html::checkbox($name, $checked, $options) . '<label></label></div>';
                return '<div class="' . $class . '">' . Html::checkbox($name, $checked, $options) . '<label for="' . $id . '">' . $label . '</label></div>';
            }
        ];

        return parent::checkboxList($items, array_merge($myOptions, $options));
    }

    public function radioList($items, $options = [])
    {

        $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
        $encode = ArrayHelper::getValue($options, 'encode', true);
        $options['item'] = function ($index, $label, $name, $checked, $value) use ($itemOptions, $encode) {
            $label = $encode ? Html::encode($label) : $label;
            $id = $name . '_' . $index;
            $options = array_merge([
//                'label' => $encode ? Html::encode($label) : $label,
                'value' => $value,
                'id' => $id
            ], $itemOptions);

            $class = 'radio';
            if ($this->inline) {
                $class .= ' radio-inline';
            }

            return '<div class="' . $class . '">' . Html::radio($name, $checked, $options) . '<label for="' . $id . '">' . $label . '</label></div>';
        };

        return parent::radioList($items, $options);
    }

}