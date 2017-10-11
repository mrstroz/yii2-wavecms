<?php

namespace mrstroz\wavecms\components\grid;


use mrstroz\wavecms\components\helpers\FontAwesome;
use yii\bootstrap\Html;
use yii\grid\DataColumn;


class ButtonColumn extends DataColumn
{

    public $label = '';
    public $faIcon = 'edit';
    public $class = 'btn btn-xs btn-default';
    public $url = ['index'];
    public $dataPjax = 0;
    public $headerOptions = ['class' => 'button-column'];


    public function renderDataCellContent($model, $key, $index)
    {

        if (isset($this->url['id'])) {
            $this->url['id'] = $model->id;
        }

        $Buttons = Html::a(
            FontAwesome::icon($this->faIcon),
            $this->url,
            [
                'class' => $this->class,
                'data-pjax' => $this->dataPjax
            ]);


        return $Buttons;
    }

}