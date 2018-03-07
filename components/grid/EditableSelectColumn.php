<?php

namespace mrstroz\wavecms\components\grid;


use dosamigos\editable\Editable;
use Yii;
use yii\base\InvalidParamException;
use yii\web\JsExpression;

class EditableSelectColumn extends EditableColumn
{

    public $type = 'select';
    public $source;

    public function renderDataCellContent($model, $key, $index)
    {

        if (!is_array($this->source)) {
            throw new InvalidParamException(Yii::t('wavecms/main', 'Property "{property}" is not defined in {class}', ['property' => 'source', 'class' => 'EditableSelectColumn']));
        }

        foreach ($this->source as $option) {
            if ($option['value'] == $model->{$this->attribute}) {
                $valueText = $option['text'];
            }
        }

        return Editable::widget([
            'model' => $model,
            'attribute' => $this->attribute,
            'type' => $this->type,
            'url' => $this->url,
            'mode' => $this->mode,
            'placement' => $this->placement,
            'value' => $valueText,

            'clientOptions' => [
                'value' => $model->{$this->attribute},
                'source' => $this->source,
                'display' => new JsExpression("function(value, sourceData) {
                    var editableClass = '';
                    var editableText = '';
                    var that = $(this);

                    $.each(sourceData, function(i, v) { 
                        if(v.value == value) {
                            editableClass = v.class;
                            editableText = v.text;
                        }
                    });
                    
                    var span = $('<span></span>').attr('class',editableClass).text(editableText);
                    
                    $(this).html(span);
                    
                }"),

            ],
        ]);
    }

}