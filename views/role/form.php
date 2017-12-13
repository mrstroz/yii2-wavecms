<?php

use mrstroz\wavecms\components\helpers\FontAwesome;
use mrstroz\wavecms\components\helpers\FormHelper;
use mrstroz\wavecms\components\helpers\WavecmsForm;
use mrstroz\wavecms\components\widgets\PanelWidget;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\Html;

?>

<?php $form = WavecmsForm::begin(); ?>

<?php echo Html::activeHiddenInput($model, 'type', ['value' => 1]); ?>

<div class="row">

    <div class="col-md-12">
        <?php PanelWidget::begin(['heading' => Yii::t('wavecms/user', 'Role'), 'panel_class' => 'panel-primary']); ?>

        <?php echo $form->field($model, 'name'); ?>

        <?php PanelWidget::end(); ?>
    </div>


</div>

<?php FormHelper::saveButton() ?>

<?php $extraButtons = [];

if (!$model->isNewRecord) {
    $extraButtons[] = Html::a(FontAwesome::icon('plus') . ' ' . Yii::t('wavecms/user', 'Add permissions'), ['add-permission', 'id' => $model->name], [
        'class' => 'btn btn-default'
    ]);
}

?>

<?php echo ButtonGroup::widget([
    'buttons' => $extraButtons
]); ?>

<?php WavecmsForm::end(); ?>
