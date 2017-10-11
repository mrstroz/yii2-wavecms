<?php

use mrstroz\wavecms\components\helpers\FontAwesome;
use mrstroz\wavecms\components\helpers\WavecmsForm;
use mrstroz\wavecms\components\widgets\PanelWidget;
use mrstroz\wavecms\models\AuthItem;
use yii\bootstrap\Html;
use yii\grid\DataColumn;
use yii\grid\GridView;

?>


<div class="row">


    <?php $form = WavecmsForm::begin(); ?>

    <?php echo Html::activeHiddenInput($roleForm, 'user_id', ['value' => $model->id]); ?>

    <div class="col-md-4">
        <?php PanelWidget::begin(['heading' => Yii::t('wavecms/user/login','Choose role'), 'panel_class' => 'panel-primary']); ?>

        <?php echo $form->field($roleForm, 'role')->dropDownList(
            AuthItem::find()->select(['name'])->where(['type' => 1])->indexBy('name')->column(),
            ['prompt' => Yii::t('wavecms/user/login','Select role')]
        ); ?>

        <?php echo Html::button(Yii::t('wavecms/user/login','Assign role'), ['class' => 'btn btn-primary', 'type' => 'submit']); ?>


        <?php PanelWidget::end(); ?>

    </div>

    <?php WavecmsForm::end(); ?>

    <div class="col-md-8">
        <?php PanelWidget::begin(['heading' => Yii::t('wavecms/user/login','Assigned roles')]); ?>

        <?php echo GridView::widget([
            'dataProvider' => $assignedRolesDataProvider,
            'columns' => [
                [
                    'attribute' => 'item_name',
                    'label' => Yii::t('wavecms/user/login','Role')
                ],
                [
                    'class' => DataColumn::className(),
                    'content' => function ($model, $key, $index) {
                        return Html::a(FontAwesome::icon('trash'),
                            ['un-assign', 'id' => $model->user_id, 'role' => $model->item_name],
                            [
                                'data-confirm' => Yii::t('yii', Yii::t('yii','Are you sure you want to delete this item?')),
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


