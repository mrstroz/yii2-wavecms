<?php

namespace mrstroz\wavecms\components\grid;


use mrstroz\wavecms\components\helpers\FontAwesome;
use Yii;
use yii\helpers\Html;

class ActionColumn extends \yii\grid\ActionColumn
{

    protected function initDefaultButtons()
    {
//        $this->initDefaultButton('view', 'eye');
        $this->initDefaultButton('update', 'pencil-alt', ['class' => 'btn btn-primary btn-xs']);
        $this->initDefaultButton('delete', 'trash-alt', [
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
            'class' => ['btn btn-danger btn-xs']
        ]);
    }

    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                switch ($name) {
                    case 'view':
                        $title = Yii::t('yii', 'View');
                        break;
                    case 'update':
                        $title = Yii::t('yii', 'Update');
                        break;
                    case 'delete':
                        $title = Yii::t('yii', 'Delete');
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);
                $icon = FontAwesome::icon($iconName);

                $params = [];
                $actionParams = Yii::$app->controller->actionParams;

                if (isset($actionParams['parentField']) && isset($actionParams['parentId']) && isset($actionParams['parentRoute'])) {
                    $params['parentField'] = $actionParams['parentField'];
                    $params['parentId'] = $actionParams['parentId'];
                    $params['parentRoute'] = $actionParams['parentRoute'];
                }

                foreach (Yii::$app->controller->forwardParams as $forwardParam) {
                    $params[$forwardParam] = Yii::$app->request->get($forwardParam);
                }

                if (count($params)) {
                    $url .= '&' . http_build_query($params);
                }

                return Html::a($icon, $url, $options);
            };
        }
    }

}