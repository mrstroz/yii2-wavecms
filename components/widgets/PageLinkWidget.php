<?php

namespace mrstroz\wavecms\components\widgets;

use kartik\select2\Select2;
use mrstroz\wavecms\page\models\Page;
use mrstroz\wavecms\page\models\PageLang;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

class PageLinkWidget extends Widget
{

    /** @var ActiveForm $form */
    public $form;
    /** @var ActiveRecord $model */
    public $model;
    /** @var array $pages */
    public $pages = [];
    public $idAttribute = 'page_id';
    public $urlAttribute = 'page_url';
    public $blankAttribute = 'page_blank';

    public function init()
    {
        if (!$this->form)
            throw new InvalidConfigException(Yii::t('wavecms/base/main', 'Attribute {attribute} is not defined', ['attribute' => 'form']));

        if (!$this->model)
            throw new InvalidConfigException(Yii::t('wavecms/base/main', 'Attribute {attribute} is not defined', ['attribute' => 'model']));

        $pages = Page::find()->select([Page::tableName() . '.id', PageLang::tableName() . '.title'])->leftJoin(PageLang::tableName(), PageLang::tableName() . '.page_id = ' . Page::tableName() . '.id AND language = "' . Yii::$app->wavecms->editedLanguage . '"')->asArray()->all();
        $this->pages[Yii::t('wavecms/page/main', 'Pages')] = ArrayHelper::map($pages, 'id', 'title');

        parent::init();
    }


    public function run()
    {
        echo Html::beginTag('div', ['class' => 'row']);
        echo Html::beginTag('div', ['class' => 'col-md-5']);
        echo $this->form->field($this->model, $this->idAttribute)->widget(Select2::className(), [
            'data' => $this->pages,
            'options' => ['placeholder' => '... ' . Yii::t('wavecms/base/main', 'choose')],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        echo Html::endTag('div');
        echo Html::beginTag('div', ['class' => 'col-md-5']);
        echo $this->form->field($this->model, $this->urlAttribute)->hint(Yii::t('wavecms/page/main', 'Format: <code>http://exmple.com/url-to-page</code> or <code>/url-to-page</code>'));
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-md-2']);
        echo $this->form->field($this->model, $this->blankAttribute)->dropDownList([
            '0' => Yii::t('wavecms/base/main','No'),
            '1' => Yii::t('wavecms/base/main','Yes'),
        ]);
        echo Html::endTag('div');

        echo Html::endTag('div');

    }
}