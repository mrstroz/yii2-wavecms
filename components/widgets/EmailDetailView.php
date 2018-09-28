<?php

namespace mrstroz\wavecms\components\widgets;


class EmailDetailView extends \yii\widgets\DetailView
{

    public $options = [
        'class' => 'table table-striped table-bordered detail-view',
        'style' => 'border-left: 1px solid gray; border-top: 1px solid gray; width: 100%;',
        'border' => '0',
        'cellpadding' => '5',
        'cellspacing' => '0',
    ];

    public $template = '<tr><th style="width: 25%; text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;" {captionOptions}>{label}</th><td style="width: 75%; border-right: 1px solid gray; border-bottom: 1px solid gray;" {contentOptions}>{value}</td></tr>';


}