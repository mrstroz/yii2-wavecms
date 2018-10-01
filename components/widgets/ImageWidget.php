<?php

namespace mrstroz\wavecms\components\widgets;

use mrstroz\wavecms\components\behaviors\ImageBehavior;
use mrstroz\wavecms\components\helpers\FontAwesome;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class ImageWidget extends InputWidget
{


    public function run()
    {

        $this->field->form->options['enctype'] = 'multipart/form-data';

        echo Html::activeFileInput($this->model, $this->attribute, $this->options);

        $modelBehaviors = $this->model->behaviors;

        if (is_array($modelBehaviors)) {
            foreach ($modelBehaviors as $behavior) {
                if ($behavior instanceof ImageBehavior) {

                    if ($this->attribute === $behavior->attribute) {

                        if (!empty($this->model->{$this->attribute})) {
                            $folder = $behavior->getWebFolder();

                            echo Html::beginTag('div', [
                                'class' => ['row thumbnail-outer magnific-outer']
                            ]);

                            echo Html::beginTag('div', [
                                'class' => 'col-md-4'
                            ]);
                            echo Html::img($folder . '/' . $behavior->thumbFolder . '/' . $this->model->{$this->attribute}, ['class' => 'img-responsive thumbnail']);
                            echo Html::a($this->model->{$this->attribute}, $folder . '/' . $this->model->{$this->attribute},
                                ['class' => 'label label-default popup', 'target' => '_blank']);

                            echo Html::beginTag('div', [
                                'class' => 'checkbox'
                            ]);
                            echo Html::input('checkbox', $this->attribute . '_image_delete', 1, ['id' => $this->attribute . '_image_delete']);
                            echo Html::label(Yii::t('wavecms/main', 'Delete ?'), $this->attribute . '_image_delete');
                            echo Html::endTag('div');
                            echo Html::endTag('div');

                            echo Html::beginTag('div', [
                                'class' => 'col-md-8'
                            ]);

                            if (count($behavior->sizes)) {
                                echo Html::beginTag('table', ['class' => 'table table-striped table-bordered']);

                                echo Html::beginTag('tr');
                                echo Html::tag('th', Yii::t('wavecms/main', 'Width'));
                                echo Html::tag('th', Yii::t('wavecms/main', 'Height'));
                                echo Html::tag('th', '', [
                                    'class' => 'action-column'
                                ]);
                                echo Html::endTag('tr');

                                $i = 0;
                                foreach ($behavior->sizes as $size) {

                                    if (count($size) != 2 && count($size) != 3)
                                        throw new InvalidConfigException(Yii::t('wavecms/main', 'Size is wrong defined for attribute {attribute}', ['attribute' => $this->attribute]));

                                    echo Html::beginTag('tr');

                                    echo Html::tag('td', $size[0]);
                                    echo Html::tag('td', $size[1]);

                                    echo Html::beginTag('td');
                                    echo Html::a(FontAwesome::icon('search'), $folder . '/' . $i . '/' . $this->model->{$this->attribute}, [
                                        'target' => '_blank',
                                        'class' => 'btn btn-default btn-xs popup'
                                    ]);

                                    echo Html::endTag('td');
                                    echo Html::endTag('tr');

                                    $i++;
                                }

                                echo Html::endTag('table');
                            }

                            echo Html::endTag('div');
                            echo Html::endTag('div');
                        }
                    }
                }
            }
        }

        parent::run();
    }
}