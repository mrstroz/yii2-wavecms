<?php

use mrstroz\wavecms\components\helpers\WavecmsForm;
use yii\bootstrap\Html;


$this->title = Yii::t('wavecms/user', 'Login to WaveCMS');

?>

<?php $form = WavecmsForm::begin(['options' => ['id' => 'login-form']]); ?>

<div class="logo-outer">
    <div class="logo">
        <?php
        $asset = \mrstroz\wavecms\asset\WavecmsAsset::register($this);
        echo Html::img($asset->baseUrl . '/img/logo.svg', ['alt' => 'waveCMS']);
        ?>
        <h2>wave<strong>CMS</strong></h2>
    </div>
</div>


<?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => Yii::t('wavecms/user', 'Email')])->label(false) ?>
<?= $form->field($model, 'password')->passwordInput(['placeholder' => Yii::t('wavecms/user', 'Password')])->label(false) ?>
<?= $form->field($model, 'rememberMe')->checkbox() ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('wavecms/user', 'Login'), ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
</div>

<?php echo Html::a(Yii::t('wavecms/user', 'Forgot password ?'), ['/request-password-reset']); ?>

<?php WavecmsForm::end(); ?>
