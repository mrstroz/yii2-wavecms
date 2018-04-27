<?php

namespace mrstroz\wavecms\components\grid;


use mrstroz\wavecms\components\helpers\FontAwesome;
use Yii;
use yii\bootstrap\Html;
use yii\grid\DataColumn;


class PublishColumn extends DataColumn
{

    public $attribute = 'publish';
    public $label = '';
    public $headerOptions = [
        'class' => 'publish-column'
    ];


    public function renderDataCellContent($model, $key, $index)
    {
        if ($model->{$this->attribute} == 1) {
            return Html::a(
                FontAwesome::icon('toggle-on'),
                ['publish', 'id' => $model->id],
                [
                    'class' => 'btn btn-xs btn-success btn-publish',
                    'title' => Yii::t('wavecms/main','Publish'),
                    'data-pjax' => 0
                ]);
        } else {
            return Html::a(
                FontAwesome::icon('toggle-off'),
                ['publish', 'id' => $model->id],
                [
                    'class' => 'btn btn-xs btn-default btn-publish',
                    'title' => Yii::t('wavecms/main','Publish'),
                    'data-pjax' => 0
                ]);
        }
    }

}