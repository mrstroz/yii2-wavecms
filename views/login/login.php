<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;


$this->title = Yii::t('wavecms/user', 'Login to WaveCMS');

?>

<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

<h2><?php echo Yii::t('wavecms/user', 'Login to Wave<span>CMS</span>'); ?></h2>

<?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => Yii::t('wavecms/user', 'Email')])->label(false) ?>
<?= $form->field($model, 'password')->passwordInput(['placeholder' => Yii::t('wavecms/user', 'Password')])->label(false) ?>
<?= $form->field($model, 'rememberMe')->checkbox() ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('wavecms/user', 'Login'), ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
</div>

<?php echo Html::a(Yii::t('wavecms/user', 'Forgot password ?'), ['/request-password-reset']); ?>

<?php ActiveForm::end(); ?>
