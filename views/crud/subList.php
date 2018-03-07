<?php

use himiklab\sortablegrid\SortableGridView;
use yii\bootstrap\ButtonGroup;
use yii\grid\GridView;

?>

<div class="sub-list">
    <?php

    if ($sort) {

//        Pjax::begin(['id' => 'grid']);

        echo SortableGridView::widget(array(
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
            'columns' => $columns,
            'layout' => '{items}{summary}{pager}',
            'tableOptions' => [
                'class' => 'table table-striped table-bordered table-hover table-sublist'
            ]
        ));

//        Pjax::end();

    } else {

//        Pjax::begin(['id' => 'grid']);

        echo GridView::widget(array(
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
            'columns' => $columns,
            'layout' => '{items}{summary}{pager}',
            'tableOptions' => [
                'class' => 'table table-striped table-bordered table-hover table-sublist'
            ]
        ));

//        Pjax::end();

    }

    ?>

    <?php if ($this->params['buttons_sublist']): ?>
        <?php if (isset($this->params['buttons_sublist'][Yii::$app->controller->id])): ?>
            <?= ButtonGroup::widget([
                'buttons' => $this->params['buttons_sublist'][Yii::$app->controller->id],
            ]); ?>
        <?php endif; ?>
    <?php endif; ?>

</div>

