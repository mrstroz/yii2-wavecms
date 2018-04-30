<?php

namespace mrstroz\wavecms\components\widgets;

use mrstroz\wavecms\components\behaviors\FileBehavior;
use Yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class FileWidget extends InputWidget
{


    public function run()
    {

        $this->field->form->options['enctype'] = 'multipart/form-data';

        echo Html::activeFileInput($this->model, $this->attribute, $this->options);

        $modelBehaviors = $this->model->behaviors;

        if (is_array($modelBehaviors)) {
            foreach ($modelBehaviors as $behavior) {
                if ($behavior instanceof FileBehavior) {

                    if ($this->attribute === $behavior->attribute) {

                        if (!empty($this->model->{$this->attribute})) {
                            $folder = $behavior->getWebFolder();

                            echo Html::beginTag('div', [
                                'class' => ['row thumbnail-outer']
                            ]);

                            echo Html::beginTag('div', [
                                'class' => 'col-md-12'
                            ]);

                            echo Html::a($this->model->{$this->attribute},  $folder . '/' . $this->model->{$this->attribute},
                                ['class' => 'label label-default', 'target' => '_blank']);

                            echo Html::beginTag('div', [
                                'class' => 'checkbox'
                            ]);
                            echo Html::input('checkbox', $this->attribute . '_file_delete', 1, ['id' => $this->attribute . '_file_delete']);
                            echo Html::label(Yii::t('wavecms/main', 'Delete ?'), $this->attribute . '_file_delete');

                            echo Html::endTag('div');


                            echo Html::endTag('div');
                            echo Html::endTag('div');
                        }
                    }
                }
            }
        }

        parent::run(); // TODO: Change the autogenerated stub
    }
}