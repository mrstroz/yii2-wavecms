<?php

use himiklab\sortablegrid\SortableGridView;
use yii\bootstrap\ButtonGroup;
use yii\grid\GridView;
use yii\widgets\Pjax;

?>

<div class="sub-list">
    <?php

    if ($sort) {

        Pjax::begin(['id' => 'grid']);

        echo SortableGridView::widget(array(
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
            'columns' => $columns,
            'layout' => '{items}{summary}{pager}',
            'tableOptions' => [
                'class' => 'table table-striped table-bordered table-hover table-sublist'
            ]
        ));

        Pjax::end();

    } else {

        Pjax::begin(['id' => 'grid']);

        echo GridView::widget(array(
            'dataProvider' => $dataProvider,
            'filterModel' => $filterModel,
            'columns' => $columns,
            'layout' => '{items}{summary}{pager}',
            'tableOptions' => [
                'class' => 'table table-striped table-bordered table-hover table-sublist'
            ]
        ));

        Pjax::end();

    }

    ?>

    <?php if ($this->params['buttons_sublist']): ?>
        <?= ButtonGroup::widget([
            'buttons' => $this->params['buttons_sublist'],
        ]); ?>
    <?php endif; ?>

</div>

