<?php

namespace mrstroz\wavecms\components\grid;


use yii\grid\CheckboxColumn as YiiCheckboxColumn;


class CheckboxColumn extends YiiCheckboxColumn
{

    public $headerOptions = [
        'class' => 'checkbox-column'
    ];

}