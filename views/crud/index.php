<?php

use himiklab\sortablegrid\SortableGridView;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;


/** @var array $bulkActions */
/** @var \yii\data\ActiveDataProvider $dataProvider */
/** @var \yii\base\Model $filterModel */
/** @var array $columns */

?>


<?php

$form = ActiveForm::begin();

?>

<?php if ($bulkActions): ?>
    <div class="row bulk-actions">
        <div class="col-md-12">
            <i class="fa fa-long-arrow-down" aria-hidden="true"></i>
            <?php

            $items = [];
            $items[''] = Yii::t('wavecms/main', '... choose action');
            foreach ($bulkActions as $key => $name) {
                $items[Url::to([$key])] = $name;
            }
            ?>
            <?php echo Html::dropDownList('bulk_action',
                null,
                $items,
                [
                    'class' => 'form-control input-sm bulk-drop-down',
                    'id' => 'bulk_action'
                ]
            );

            $this->registerJs('
                $("#bulk_action").change(function(){
                    $(this).parents("form").attr("action",$(this).val());
                });
            ');

            ?>
            <?php echo Html::submitButton(Yii::t('wavecms/main', 'Submit'), ['class' => 'btn btn-light btn-sm','data-confirm' => Yii::t('wavecms/main','Are you sure ?')]); ?>
        </div>

    </div>
<?php endif; ?>


<?php

if ($sort) {

    echo SortableGridView::widget(array(
        'dataProvider' => $dataProvider,
        'filterModel' => $filterModel,
        'columns' => $columns,
        'layout' => '{items}{summary}{pager}',
        'tableOptions' => [
            'class' => 'table table-striped table-bordered table-hover'
        ]
    ));

} else {

    echo GridView::widget(array(
        'dataProvider' => $dataProvider,
        'filterModel' => $filterModel,
        'columns' => $columns,
        'layout' => '{items}{summary}{pager}',
        'tableOptions' => [
            'class' => 'table table-striped table-bordered table-hover'
        ]
    ));


}


?>


<?php ActiveForm::end(); ?>


