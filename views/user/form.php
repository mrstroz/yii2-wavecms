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

<div class="row">

    <div class="col-md-6">
        <?php PanelWidget::begin(['heading' => Yii::t('wavecms/user', 'Login data'), 'panel_class' => 'panel-primary']); ?>

        <?php echo $form->field($model, 'email'); ?>
        <?php echo $form->field($model, 'password')->passwordInput(); ?>


        <div class="row">
            <div class="col-md-6">
                <?php echo $form->field($model, 'status')->dropDownList(
                    [
                        User::STATUS_DELETED => Yii::t('wavecms/user', 'Not active'),
                        User::STATUS_ACTIVE => Yii::t('wavecms/user', 'Active')
                    ]
                ); ?>
            </div>
            <div class="col-md-6">

                <?php echo $form->field($model, 'is_admin')->dropDownList(
                    [
                        User::IS_ADMIN_NO => Yii::t('wavecms/user', 'No'),
                        User::IS_ADMIN_YES => Yii::t('wavecms/user', 'Yes')
                    ]
                ); ?>

            </div>

        </div>

        <?php PanelWidget::end(); ?>

    </div>


    <div class="col-md-6">
        <?php PanelWidget::begin(['heading' => Yii::t('wavecms/user', 'User data')]); ?>

        <?php echo $form->field($model, 'first_name'); ?>
        <?php echo $form->field($model, 'last_name'); ?>
        <?php echo $form->field($model, 'lang')->dropDownList([
            '' => Yii::t('wavecms/user', 'English'),
            'pl' => Yii::t('wavecms/user', 'Polish')
        ]); ?>


        <?php PanelWidget::end(); ?>
    </div>

</div>

<?php $extraButtons = [];

if (!$model->isNewRecord) {
    $extraButtons[] = Html::a(FontAwesome::icon('exchange') . ' ' . Yii::t('wavecms/user', 'Assign role'), ['assign', 'id' => $model->id], [
        'class' => 'btn btn-default'
    ]);
}

?>

<?php FormHelper::saveButton() ?>

<?php echo ButtonGroup::widget([
    'buttons' => $extraButtons
]); ?>

<?php WavecmsForm::end(); ?>
