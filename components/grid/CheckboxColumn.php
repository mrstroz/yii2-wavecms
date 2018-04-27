<?php

namespace mrstroz\wavecms\components\grid;


use yii\grid\CheckboxColumn as YiiCheckboxColumn;


class CheckboxColumn extends YiiCheckboxColumn
{

    public $headerOptions = [
        'class' => 'checkbox-column'
    ];

    protected function renderHeaderCellContent()
    {
        $html = parent::renderHeaderCellContent();

        return '<div class="checkbox">' . $html . '<label></label></div>';
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        $html = parent::renderDataCellContent($model, $key, $index);

        return '<div class="checkbox">' . $html . '<label></label></div>';
    }

}