<?php

namespace mrstroz\wavecms\components\grid;


use dosamigos\editable\Editable;
use yii\grid\DataColumn;


class EditableColumn extends DataColumn
{

    public $type = 'text';
    public $url = ['editable'];
    public $mode = 'popup';
    public $placement = 'top';

    public function renderDataCellContent($model, $key, $index)
    {

        return Editable::widget([
            'model' => $model,
            'attribute' => $this->attribute,
            'type' => $this->type,
            'url' => $this->url,
            'mode' => $this->mode,
            'placement' => $this->placement,

            'clientOptions' => [
                'emptytext' => \Yii::t('wavecms/main','[no data]'),
            ]
        ]);
    }

}