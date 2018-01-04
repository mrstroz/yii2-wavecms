<?php

namespace mrstroz\wavecms\controllers;

use mrstroz\wavecms\components\actions\IndexAction;
use mrstroz\wavecms\components\grid\ActionColumn;
use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\helpers\FontAwesome;
use mrstroz\wavecms\components\helpers\NavHelper;
use mrstroz\wavecms\components\web\Controller;
use mrstroz\wavecms\forms\AddPermissionForm;
use mrstroz\wavecms\models\AuthItem;
use mrstroz\wavecms\models\AuthItemChild;
use Yii;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\DataColumn;
use yii\web\NotFoundHttpException;

class RoleController extends Controller
{

    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className()
            ]
        ];
    }

    public function init()
    {
        $authItemModel = Yii::createObject(AuthItem::class);

        $this->bulkActions = false;

        $this->heading = Yii::t('wavecms/user', 'Roles');
        $this->query = $authItemModel::find()->where(['type' => 1]);
//
        $this->dataProvider = new ActiveDataProvider([
            'query' => $this->query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        $this->columns = array(
            'name',
            [
                'class' => DataColumn::className(),
                'label' => Yii::t('wavecms/user', 'Add permissions'),
                'content' => function ($model, $key, $index) {
                    if ($model->name !== 'Super admin') {
                        return Html::a(FontAwesome::icon('plus'),
                            ['add-permission', 'id' => $model->name],
                            ['class' => 'btb btn-xs btn-default']);
                    }
                    return false;
                }
            ],
            [
                'class' => ActionColumn::className(),
            ]
        );


        parent::init();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => [
                'add-permission',
                'remove-permission'
            ],
            'roles' => [
                '@'
            ],
        ];

        return $behaviors;
    }

    public function actionCreate($parentField = null, $parentId = null, $parentRoute = null)
    {

        $this->returnUrl = ['index'];
        $this->view->params['h1'] = $this->heading;

        array_unshift($this->view->params['buttons_top'], Html::a('Return', $this->returnUrl, ['class' => 'btn btn-default']));
        NavHelper::$active[] = 'create';

        $model = Yii::createObject($this->query->modelClass);

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            if ($model->validate()) {

                $auth = Yii::$app->authManager;
                $role = $auth->createRole($model->name);
                $auth->add($role);

                Flash::message('after_create', 'success', ['message' => Yii::t('wavecms/user', 'Role has been created')]);

                if (Yii::$app->request->post('save_and_return')) {
                    return $this->redirect($this->returnUrl);
                }

                return $this->redirect([
                    'update',
                    'id' => $model->name
                ]);
            }
        }

        return $this->render($this->viewForm, array(
            'model' => $model
        ));
    }

    public function actionUpdate($id, $parentField = null, $parentId = null, $parentRoute = null)
    {

        $this->returnUrl = ['index'];

        if ($id === AuthItem::SUPER_ADMIN) {
            Flash::message('delete', 'warning', ['message' => Yii::t('wavecms/user', '{super_admin} role cannot be changed', ['super_admin' => AuthItem::SUPER_ADMIN])]);
            return $this->redirect($this->returnUrl);
        }

        $this->view->params['h1'] = $this->heading;

        array_unshift($this->view->params['buttons_top'], Html::a('Return', $this->returnUrl, ['class' => 'btn btn-default']));
        NavHelper::$active[] = 'update';

        $model = $this->query->andWhere(['name' => $id])->one();

        if (!$model)
            throw new NotFoundHttpException(Yii::t('wavecms/user', 'Role not found'));

        if (Yii::$app->request->isPost) {

            $model->load(Yii::$app->request->post());

            if ($model->validate()) {

                $model->save();


                Flash::message('after_update', 'success', ['message' => Yii::t('wavecms/user', 'Role has been updated')]);

                if (Yii::$app->request->post('save_and_return')) {
                    return $this->redirect($this->returnUrl);
                }

                return $this->redirect([
                    'update',
                    'id' => $model->name
                ]);
            }
        }

        return $this->render($this->viewForm, array(
            'model' => $model
        ));
    }

    public function actionDelete($id, $parentField = null, $parentId = null, $parentRoute = null)
    {
        $this->returnUrl = ['index'];

        $authItemModel = Yii::createObject(AuthItem::class);
        if ($id === $authItemModel::SUPER_ADMIN) {
            Flash::message('delete', 'warning', ['message' => Yii::t('wavecms/user', '{super_admin} role cannot be deleted', ['super_admin' => $authItemModel::SUPER_ADMIN])]);
            return $this->redirect($this->returnUrl);
        }

        $model = $this->query->andWhere(['name' => $id])->one();

        if (!$model)
            throw new NotFoundHttpException(Yii::t('wavecms/user', 'Role not found'));

        $model->delete();
        Flash::message('delete', 'success', ['message' => Yii::t('wavecms/user', 'Role has been deleted')]);

        return $this->redirect($this->returnUrl);
    }

    public function actionAddPermission($id)
    {

        $authItemModel = Yii::createObject(AuthItem::class);
        $authItemChildModel = Yii::createObject(AuthItemChild::class);

        if ($id === $authItemModel::SUPER_ADMIN) {
            Flash::message('delete', 'warning', ['message' => Yii::t('wavecms/user', '{super_admin} role cannot be changed', ['super_admin' => $authItemModel::SUPER_ADMIN])]);
            return $this->redirect(['index']);
        }

        $model = $this->query->andWhere(['name' => $id])->one();

        if (!$model)
            throw new NotFoundHttpException(Yii::t('wavecms/user', 'Role not found'));

        $this->view->params['h1'] = Yii::t('wavecms/user', 'Add permission to role <b>{name}</b>', ['name' => $id]);
        NavHelper::$active[] = 'add-permission';

        $permissionForm = Yii::createObject(AddPermissionForm::class);

        $permissionDataProvider = new ActiveDataProvider([
            'query' => $authItemChildModel::find()->where(['parent' => $id])
        ]);

        if (Yii::$app->request->isPost) {

            $permissionForm->load(Yii::$app->request->post());

            if ($permissionForm->validate()) {

                $auth = Yii::$app->authManager;
                $permission = $auth->getPermission($permissionForm->name);
                if (!$permission) {
                    $permission = $auth->createPermission($permissionForm->name);
                    $auth->add($permission);
                }
                $role = $auth->getRole($permissionForm->role);

                if (!$role)
                    throw new NotFoundHttpException('Role not found');

                $auth->addChild($role, $permission);

                Flash::message('after_create', 'success', ['message' => Yii::t('wavecms/user', 'Permission has been assigned')]);

                return $this->redirect([
                    'add-permission',
                    'id' => $model->name
                ]);

            }
        }


        return $this->render('add-permission', [
            'model' => $model,
            'permissionForm' => $permissionForm,
            'permissionDataProvider' => $permissionDataProvider
        ]);
    }

    public function actionRemovePermission($id, $permission)
    {
        $authItemModel = Yii::createObject(AuthItem::class);

        if ($id === $authItemModel::SUPER_ADMIN) {
            Flash::message('delete', 'warning', ['message' => Yii::t('wavecms/user', '{super_admin} role cannot be changed', ['super_admin' => $authItemModel::SUPER_ADMIN])]);
            return $this->redirect(['index']);
        }

        $auth = Yii::$app->authManager;

        $permission = $auth->getPermission($permission);
        if (!$permission)
            throw new NotFoundHttpException(Yii::t('wavecms/user', 'Permission not found'));

        $role = $auth->getRole($id);
        if (!$role)
            throw new NotFoundHttpException(Yii::t('wavecms/user', 'Role not found'));

        $auth->removeChild($role, $permission);

        Flash::message('after_create', 'success', ['message' => Yii::t('wavecms/user', 'Permission has been revoked')]);

        return $this->redirect([
            'add-permission',
            'id' => $id
        ]);

    }


}