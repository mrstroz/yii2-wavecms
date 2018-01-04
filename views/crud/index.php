<?php

use himiklab\sortablegrid\SortableGridView;
use yii\grid\GridView;

?>


<?php

if ($sort) {

//    Pjax::begin(['id' => 'grid']);

    echo SortableGridView::widget(array(
        'dataProvider' => $dataProvider,
        'filterModel' => $filterModel,
        'columns' => $columns,
        'layout' => '{items}{summary}{pager}',
        'tableOptions' => [
            'class' => 'table table-striped table-bordered table-hover'
        ]
    ));

//    Pjax::end();

} else {

//    Pjax::begin(['id' => 'grid']);

    echo GridView::widget(array(
        'dataProvider' => $dataProvider,
        'filterModel' => $filterModel,
        'columns' => $columns,
        'layout' => '{items}{summary}{pager}',
        'tableOptions' => [
            'class' => 'table table-striped table-bordered table-hover'
        ]
    ));

//    Pjax::end();

}


?>

