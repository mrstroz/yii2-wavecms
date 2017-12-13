<?php

namespace mrstroz\wavecms\components\grid;


use mrstroz\wavecms\components\helpers\FontAwesome;
use Yii;
use yii\bootstrap\Html;
use yii\grid\DataColumn;


class SortColumn extends DataColumn
{

    public $label = '';
    public $headerOptions = ['class' => 'sort-column'];


    public function renderDataCellContent($model, $key, $index)
    {


        $Buttons = Html::a(
            FontAwesome::icon('arrow-up'),
            ['up-down', 'id' => $model->id, 'dir' => 'up'],
            [
                'class' => 'btn btn-xs btn-default btn-sort',
                'title' => Yii::t('wavecms/main','Move up'),
                'data-pjax' => 0
            ]);

        $Buttons .= ' ' . Html::a(
                FontAwesome::icon('arrow-down'),
                ['up-down', 'id' => $model->id, 'dir' => 'down'],
                [
                    'class' => 'btn btn-xs btn-default btn-sort',
                    'title' => Yii::t('wavecms/main','Move down'),
                    'data-pjax' => 0
                ]);

        return $Buttons;
    }

}