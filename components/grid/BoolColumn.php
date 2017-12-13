<?php

namespace mrstroz\wavecms\components\grid;


use Yii;
use yii\grid\DataColumn;


class BoolColumn extends DataColumn
{

    public $headerOptions = [
        'class' => 'bool-column'
    ];


    public function renderDataCellContent($model, $key, $index)
    {

        if ($model->{$this->attribute}) {
            return '<span class="label label-success">'. Yii::t('wavecms/main','Yes').'</span>';
        }

        return '<span class="label label-light-gray">'. Yii::t('wavecms/main','No').'</span>';
    }

}