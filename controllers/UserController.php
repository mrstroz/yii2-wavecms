<?php

namespace mrstroz\wavecms\controllers;

use mrstroz\wavecms\components\grid\ActionColumn;
use mrstroz\wavecms\components\grid\BoolColumn;
use mrstroz\wavecms\components\grid\ButtonColumn;
use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\helpers\NavHelper;
use mrstroz\wavecms\components\web\Controller;
use mrstroz\wavecms\models\AssignRoleForm;
use mrstroz\wavecms\models\AuthAssignment;
use mrstroz\wavecms\models\User;
use mrstroz\wavecms\models\UserSearch;
use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\grid\DataColumn;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{

    public function init()
    {
        $this->heading = Yii::t('wavecms/user/login', 'Users');

        /** @var User $modelUser */
        $modelUser = Yii::createObject($this->module->models['User']);

        $this->query = $modelUser::find();

        $this->dataProvider = new ActiveDataProvider([
            'query' => $this->query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        $this->columns = array(
            'id',
            'first_name',
            'last_name',
            'email',
            [
                'class' => BoolColumn::className(),
                'attribute' => 'is_admin',
                'filter' => [
                    User::IS_ADMIN_NO => Yii::t('wavecms/user/login', 'No'),
                    User::IS_ADMIN_YES => Yii::t('wavecms/user/login', 'Yes')
                ],
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'status',
                'content' => function ($model, $key, $index, $column) {
                    if ($model->status === User::STATUS_ACTIVE) {
                        return '<span class="label label-success">' . Yii::t('wavecms/user/login', 'Active') . '</span>';
                    }

                    return '<span class="label label-light-gray">' . Yii::t('wavecms/user/login', 'Not active') . '</span>';
                },
                'filter' => [
                    User::STATUS_DELETED => Yii::t('wavecms/user/login', 'Not active'),
                    User::STATUS_ACTIVE => Yii::t('wavecms/user/login', 'Active')
                ]
            ],
            [
                'class' => ButtonColumn::className(),
                'faIcon' => 'exchange',
                'label' => Yii::t('wavecms/user/login', 'Assign role'),
                'url' => ['assign', 'id' => 'id']
            ],
            [
                'class' => ActionColumn::className(),
            ],
        );

        $this->filterModel = new UserSearch();

        $this->on(self::EVENT_BEFORE_MODEL_SAVE, function ($event) {
            if ($event->model->password) {
                $event->model->setPassword($event->model->password);
                $event->model->generateAuthKey();

                Flash::message('password_changed', 'success', ['message' => Yii::t('wavecms/user/login', 'Password has been changed')]);
            }
        });

        parent::init();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors(); // TODO: Change the autogenerated stub

        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => [
                'assign',
                'un-assign'
            ],
            'roles' => [
                '@'
            ],
        ];

        return $behaviors;
    }


    public function actionAssign($id)
    {

        $this->_checkConfig();
        $model = $this->_fetchOne($id);

        $this->returnUrl = ['index'];
        $this->view->params['h1'] = Yii::t('wavecms/user/login', 'Assign role to user <b>{user}</b>', ['user' => $model->email]);

        array_unshift($this->view->params['buttons_top'], Html::a('Return', $this->returnUrl, ['class' => 'btn btn-default']));
        NavHelper::$active[] = Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/index';


        $roleForm = new AssignRoleForm();

        $assignedRolesDataProvider = new ActiveDataProvider([
            'query' => AuthAssignment::find()->where(['user_id' => $id])
        ]);

        if (Yii::$app->request->isPost) {
            $roleForm->load(Yii::$app->request->post());
            if ($roleForm->validate()) {
                $auth = Yii::$app->authManager;
                $role = $auth->getRole($roleForm->role);

                if (!$role)
                    throw new NotFoundHttpException(Yii::t('wavecms/user/login', 'Role not found'));

                $auth->assign($role, $id);

                Flash::message(
                    'after_create',
                    'success',
                    ['message' => Yii::t('wavecms/user/login', 'Role has been assigned')]
                );

                return $this->redirect([
                    'assign',
                    'id' => $model->id
                ]);
            }
        }

        return $this->render('assign', [
            'model' => $model,
            'roleForm' => $roleForm,
            'assignedRolesDataProvider' => $assignedRolesDataProvider
        ]);

    }

    public function actionUnAssign($id, $role)
    {

        $this->_checkConfig();
        $model = $this->_fetchOne($id);

        $auth = Yii::$app->authManager;
        $role = $auth->getRole($role);

        if (!$role)
            throw new NotFoundHttpException(Yii::t('wavecms/user/login', 'Role not found'));

        $auth->revoke($role, $model->id);

        Flash::message('after_create', 'success', ['message' => Yii::t('wavecms/user/login', 'Role has been revoked')]);

        return $this->redirect([
            'assign',
            'id' => $model->id
        ]);
    }

    protected function _fetchOne($id)
    {
        $query = $this->query;
        $modelClass = $query->modelClass;
        /** @var ActiveRecord $model */
        $model = $query->andWhere([$modelClass::tableName() . '.id' => $id])->one();

        if (!$model)
            throw new NotFoundHttpException(Yii::t('wavecms/base/main', 'Element not found'));

        if ($this->scenario) {
            $model->scenario = $this->scenario;
        }

        return $model;
    }

    /**
     * Check if $this->query is set
     * @throws \yii\base\InvalidConfigException
     */
    protected function _checkConfig()
    {
        if (!$this->query)
            throw new InvalidConfigException(Yii::t('wavecms/base/main', 'The "query" property must be set.'));

        if (!$this->query instanceof ActiveQuery)
            throw new InvalidConfigException(Yii::t('wavecms/base/main', 'The "query" property is not instance of ActiveQuery.'));
    }

}