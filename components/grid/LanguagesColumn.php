<?php

namespace mrstroz\wavecms\components\grid;

use Yii;
use yii\grid\DataColumn;

class LanguagesColumn extends DataColumn
{

    public $attribute = 'languages';
    public $headerOptions = [
        'class' => 'languages-column'
    ];

    public function __construct(array $config = [])
    {
        $this->label = Yii::t('wavecms/base/main', 'Languages');
        parent::__construct($config);
    }


    public function renderDataCellContent($model, $key, $index)
    {

        $column = '';
        if ($model->{$this->attribute}) {
            foreach ($model->{$this->attribute} as $lang) {
                $column .= '<span class="label label-default text-uppercase">' . $lang . '</span> ';
            }
        }
        return $column;
    }

}