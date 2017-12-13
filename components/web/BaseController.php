<?php

namespace mrstroz\wavecms\components\web;

use mrstroz\wavecms\models\AuthItem;
use Yii;
use yii\web\Controller as yiiController;
use yii\web\ForbiddenHttpException;

/**
 * Class Controller
 * @package mrstroz\wavecms\components\web
 * Main controller class used to manage data in Wavecms modules
 */
class BaseController extends yiiController
{

    /**
     * @var string Admin layout
     */
    public $layout = '@wavecms/views/layouts/admin.php';

    /**
     * @var array Actions that are not checked in `beforeAction` function with RBAC permissions
     */
    public $actionsDisabledFromAccessControl = [];

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws \yii\web\ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        /** Check access to admin panel */
        if (!in_array(Yii::$app->controller->action->id, $this->actionsDisabledFromAccessControl)) {
            if (!Yii::$app->user->can(AuthItem::SUPER_ADMIN)) {
                if (!Yii::$app->user->can(Yii::$app->controller->module->id . '/' . Yii::$app->controller->id)) {
                    if (!Yii::$app->user->isGuest) {
                        throw new ForbiddenHttpException(Yii::t('wavecms/main', 'You are not allowed to perform this action'));
                    }
                }
            }
        }

        return parent::beforeAction($action);
    }
}