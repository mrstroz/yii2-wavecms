<?php

?>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin();

echo Html::submitButton(Yii::t('wavecms/main', 'Clear assets folders and cache'), [
    'class' => 'btn btn-primary'
]);

ActiveForm::end();
?>

