<?php

use dosamigos\switchinput\SwitchBox;
use dosamigos\switchinput\SwitchRadio;
use mrstroz\wavecms\components\helpers\FontAwesome;
use mrstroz\wavecms\components\helpers\FormHelper;
use mrstroz\wavecms\components\helpers\WavecmsForm;
use mrstroz\wavecms\components\widgets\PanelWidget;
use mrstroz\wavecms\models\User;
use yii\bootstrap\ButtonGroup;
use yii\helpers\Html;

?>

<?php $form = WavecmsForm::begin(); ?>

<?php echo $form->field($model, 'category'); ?>

<div class="row">

    <div class="col-md-6">
        <?php echo $form->field($model, 'message')->textarea(); ?>
    </div>


    <div class="col-md-6">
        <?php echo $form->field($model, 'translation')->textarea(); ?>
    </div>

</div>


<?php FormHelper::saveButton() ?>

<?php WavecmsForm::end(); ?>
