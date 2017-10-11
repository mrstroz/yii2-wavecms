<?php

namespace mrstroz\wavecms;

class Module extends \yii\base\Module
{

    public $models = [];

    public function init()
    {

        if (!isset($this->models['User'])) {
            $this->models['User'] = 'mrstroz\wavecms\models\User';
        }

        parent::init();
    }

}
