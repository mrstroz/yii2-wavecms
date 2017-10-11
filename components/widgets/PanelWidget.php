<?php

namespace mrstroz\wavecms\components\widgets;


use yii\base\Widget;

class PanelWidget extends Widget
{

    public $heading = '(Not set)';
    public $panel_class = 'panel-default';

    public function init()
    {
        parent::init();
        ob_start();
    }

    public function run()
    {
        $content = ob_get_clean();

        $return = '<div class="panel ' . $this->panel_class . '">
            <div class="panel-heading"><b>' . $this->heading . '</b></div>
            <div class="panel-body">' . $content . '</div></div>';

        return $return;
    }

}