<?php

use mrstroz\wavecms\components\helpers\FontAwesome;
use mrstroz\wavecms\components\helpers\WavecmsForm;
use mrstroz\wavecms\components\widgets\PanelWidget;
use yii\helpers\Html;

?>

<?php $form = WavecmsForm::begin(); ?>

<?php echo Html::activeHiddenInput($model, 'is_admin', ['value' => 1]); ?>

<div class="row">

    <div class="col-md-6">
        <?php PanelWidget::begin(['heading' => Yii::t('wavecms/user/login','Change password'), 'panel_class' => 'panel-primary']); ?>

        <div class="alert alert-info">
            <?php echo FontAwesome::icon('info-circle'); ?>
            <?php echo Yii::t('wavecms/user/login','Leave empty fields if you don\'t want to change password'); ?>
        </div>

        <?php echo $form->field($model, 'password')->passwordInput(); ?>
        <?php echo $form->field($model, 'password_repeat')->passwordInput(); ?>

        <?php PanelWidget::end(); ?>

    </div>


    <div class="col-md-6">
        <?php PanelWidget::begin(['heading' => Yii::t('wavecms/user/login','User data')]); ?>

        <?php echo $form->field($model, 'first_name'); ?>
        <?php echo $form->field($model, 'last_name'); ?>
        <?php echo $form->field($model, 'lang')->dropDownList([
            '' => Yii::t('wavecms/user/login','English'),
            'pl' => Yii::t('wavecms/user/login','Polish')
        ]); ?>

        <?php PanelWidget::end(); ?>
    </div>

</div>

<?php echo Html::submitButton(Yii::t('wavecms/user/login','Save changes'), ['class' => 'btn btn-primary']); ?>

<?php WavecmsForm::end(); ?>
