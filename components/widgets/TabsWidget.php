<?php

namespace mrstroz\wavecms\components\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class TabsWidget extends Widget
{

    public function init()
    {
        parent::init();
        ob_start();
    }

    public function run()
    {
        $content = ob_get_clean();

        $return = Html::beginTag('ul', [
            'class' => 'nav nav-tabs wavecms-tabs',
            'role' => 'tablist',
            'data-module-controller' => Yii::$app->controller->module->id . '_' . Yii::$app->controller->id
        ]);

        $i = 0;
        foreach (TabWidget::$tabs as $tab) {
            $class = '';

            $return .= Html::beginTag('li', [
                'role' => 'presentation',
                'class' => $class
            ]);

            $id = '#' . Yii::$app->controller->module->id . '_' . Yii::$app->controller->id . '_tab_' . $i;

            $return .= Html::a($tab['heading'], $id, [
                'aria-controls' => $id,
                'role' => 'tab',
                'data-toggle' => 'tab'
            ]);

            $return .= Html::endTag('li');
            $i++;
        }

        $return .= Html::endTag('ul');
        $return .= Html::tag('div', $content, ['class' => 'tab-content wavecms-tab-content']);

        return $return;
    }

}