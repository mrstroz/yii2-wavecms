<?php

namespace mrstroz\wavecms\components\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class TabWidget extends Widget
{

    public $heading = '(Not set)';
    public static $tabs = [];

    public function init()
    {
        parent::init();
        ob_start();
    }

    public function run()
    {
        $content = ob_get_clean();
        $class = 'tab-pane';

        $id = Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_tab_' . count(self::$tabs);

        $return = Html::tag('div', $content, [
            'role' => 'tabpanel',
            'class' => $class,
            'id' => $id
        ]);

        self::$tabs[] = array('heading' => $this->heading);

        return $return;
    }

}