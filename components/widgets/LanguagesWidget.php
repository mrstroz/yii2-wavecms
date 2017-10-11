<?php

namespace mrstroz\wavecms\components\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

class LanguagesWidget extends Widget
{

    public $form;
    public $model;
    public $languagesAttribute = 'languages';

    public function init()
    {
        if (!$this->form)
            throw new InvalidConfigException(Yii::t('wavecms/base/main', 'Attribute {attribute} is not defined', ['attribute' => 'form']));

        if (!$this->model)
            throw new InvalidConfigException(Yii::t('wavecms/base/main', 'Attribute {attribute} is not defined', ['attribute' => 'model']));

        parent::init();
    }

    public function run()
    {
        PanelWidget::begin(['heading' => Yii::t('wavecms/base/main', 'Languages') . '*']);
        echo Yii::t('wavecms/page/main', 'Element will be displayed in following languages:');
        echo $this->form->field($this->model, $this->languagesAttribute)
            ->checkboxList(Yii::$app->wavecms->languageCheckboxItems(),['class' => 'language-checkboxes'])
            ->label(false);
        PanelWidget::end();
    }
}