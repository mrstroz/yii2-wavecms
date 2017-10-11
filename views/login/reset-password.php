<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;


$this->title = Yii::t('wavecms/user/login', 'Reset password');

?>

<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

<h2><?php echo $this->title; ?></h2>

<p><?php echo Yii::t('wavecms/user/login', 'Please choose your new password:'); ?></p>

<?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('wavecms/user/login', 'Save'), ['class' => 'btn btn-primary btn-block']) ?>
</div>

<?php echo Html::a(Yii::t('wavecms/user/login', 'Back to login page'), ['/login']); ?>

<?php ActiveForm::end(); ?>

