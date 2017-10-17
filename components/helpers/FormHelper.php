<?php

namespace mrstroz\wavecms\components\helpers;

use Yii;
use yii\bootstrap\ButtonGroup;
use yii\helpers\Html;

class FormHelper
{

    public static function saveButton($extraButtons = [])
    {

        echo Html::hiddenInput('save_and_return', null, [
            'id' => 'save_and_return'
        ]);

        $buttons[] = Html::submitButton(Yii::t('wavecms/base/main', 'Save'), [
            'class' => 'btn btn-primary'
        ]);

        if (Yii::$app->controller->action->id !== 'page' && Yii::$app->controller->action->id !== 'settings') {
            $buttons[] = Html::submitButton(Yii::t('wavecms/base/main', 'Save &amp; Return'), [
                'class' => 'btn btn-primary',
                'onclick' => 'document.getElementById("save_and_return").value = 1'
            ]);
        }

        $buttons = array_merge($buttons, $extraButtons);

        echo ButtonGroup::widget([
            'buttons' => $buttons
        ]);

    }


}