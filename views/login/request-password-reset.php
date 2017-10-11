<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;


$this->title = Yii::t('wavecms/user/login', 'Reset password');

?>

        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <h2><?php echo $this->title; ?></h2>

        <p><?php echo Yii::t('wavecms/user/login', 'Please fill out your email. A link to reset password will be sent there.'); ?></p>

        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('wavecms/user/login', 'Send'), ['class' => 'btn btn-primary btn-block']) ?>
        </div>

        <?php echo Html::a(Yii::t('wavecms/user/login', 'Back to login page'), ['/login']); ?>

        <?php ActiveForm::end(); ?>
