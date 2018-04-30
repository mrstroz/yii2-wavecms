<?php

use himiklab\sortablegrid\SortableGridView;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;


/** @var array $bulkActions */
/** @var \yii\data\ActiveDataProvider $dataProvider */
/** @var \yii\base\Model $filterModel */
/** @var array $columns */

?>


<?php

$form = ActiveForm::begin(['id' => 'bulk_actions_form']);

?>

<?php if ($bulkActions): ?>
    <div class="row bulk-actions">
        <div class="col-md-12">
            <i class="fas fa-fw fa-long-arrow-alt-down" aria-hidden="true"></i>
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

            echo Html::hiddenInput('selection', null, [
                'id' => 'selection'
            ]);

            $this->registerJs('
                $("#bulk_action").change(function(){
                    $(this).parents("form").attr("action",$(this).val());
                });
                
                $("#bulk_actions_form").submit(function(e){
                    $("#selection").val($("#grid").yiiGridView("getSelectedRows").join(","));
                });
                
            ');

            ?>
            <?php echo Html::submitButton(Yii::t('wavecms/main', 'Submit'), ['class' => 'btn btn-light btn-sm', 'data-confirm' => Yii::t('wavecms/main', 'Are you sure ?')]); ?>
        </div>

    </div>
<?php endif; ?>

<?php ActiveForm::end(); ?>

<?php

if ($sort) {

    echo SortableGridView::widget(array(
        'dataProvider' => $dataProvider,
        'filterModel' => $filterModel,
        'columns' => $columns,
        'layout' => '{items}{summary}{pager}',
        'id' => 'grid',
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
        'id' => 'grid',
        'tableOptions' => [
            'class' => 'table table-striped table-bordered table-hover'
        ]
    ));


}


?>





