<?php

use mrstroz\wavecms\components\helpers\FontAwesome;
use mrstroz\wavecms\components\helpers\WavecmsForm;
use mrstroz\wavecms\components\widgets\PanelWidget;
use yii\bootstrap\Html;
use yii\grid\DataColumn;
use yii\grid\GridView;

?>


<div class="row">

    <?php $form = WavecmsForm::begin(); ?>

    <?php echo Html::activeHiddenInput($permissionForm, 'role', ['value' => $model->name]); ?>

    <div class="col-md-4">
        <?php PanelWidget::begin(['heading' => Yii::t('wavecms/user/login','Permission name'), 'panel_class' => 'panel-primary']); ?>

        <?php echo $form->field($permissionForm, 'name')->hint( Yii::t('wavecms/user/login','Syntax: [module]/[controller]')) ?>
        <?php echo \yii\helpers\Html::button( Yii::t('wavecms/user/login','Add permission'), ['class' => 'btn btn-primary', 'type' => 'submit']); ?>

        <?php PanelWidget::end(); ?>
    </div>

    <?php WavecmsForm::end(); ?>


    <div class="col-md-8">
        <?php PanelWidget::begin(['heading' => Yii::t('wavecms/user/login','Assigned permissions')]); ?>

        <?php echo GridView::widget([
            'dataProvider' => $permissionDataProvider,
            'columns' => [
                [
                    'attribute' => 'child',
                    'label' =>  Yii::t('wavecms/user/login','Permission')
                ],
                [
                    'class' => DataColumn::className(),
                    'content' => function ($model, $key, $index) {
                        return Html::a(FontAwesome::icon('trash'),
                            ['remove-permission', 'id' => $model->parent, 'permission' => $model->child],
                            [
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'class' => 'btb btn-xs btn-danger'
                            ]);
                    }
                ]
            ]
        ]); ?>

        <?php PanelWidget::end(); ?>

    </div>

</div>



