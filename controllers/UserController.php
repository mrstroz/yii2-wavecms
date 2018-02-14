<?php

namespace mrstroz\wavecms\controllers;

use dosamigos\editable\Editable;
use mrstroz\wavecms\components\grid\ActionColumn;
use mrstroz\wavecms\components\grid\CheckboxColumn;
use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\helpers\FontAwesome;
use mrstroz\wavecms\components\helpers\NavHelper;
use mrstroz\wavecms\components\web\Controller;
use mrstroz\wavecms\forms\AssignRoleForm;
use mrstroz\wavecms\models\AuthAssignment;
use mrstroz\wavecms\models\search\UserSearch;
use mrstroz\wavecms\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{

    public function init()
    {
        $this->heading = Yii::t('wavecms/user', 'Users');

        /** @var User $modelUser */
        $userModel = Yii::createObject(User::className());

        $this->query = $userModel::find()
            ->select(['user.*', 'roles' => 'GROUP_CONCAT(auth_assignment.item_name SEPARATOR ",")'])
            ->leftJoin('auth_assignment', 'user.id = auth_assignment.user_id')
            ->groupBy('user.id');

        $this->dataProvider = new ActiveDataProvider([
            'query' => $this->query,
        ]);

        $this->columns = array(
            [
                'class' => CheckboxColumn::className()
            ],
            'id',
            [
                'class' => DataColumn::className(),
                'attribute' => 'first_name',
                'content' => function ($model, $key, $index, $column) {
                    return Editable::widget([
                        'model' => $model,
                        'attribute' => 'first_name',
                        'type' => 'text',
                        'url' => ['editable'],
                        'mode' => 'popup',
                        'placement' => 'top',
                    ]);
                }
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'last_name',
                'content' => function ($model, $key, $index, $column) {
                    return Editable::widget([
                        'model' => $model,
                        'attribute' => 'last_name',
                        'type' => 'text',
                        'url' => ['editable'],
                        'mode' => 'popup',
                        'placement' => 'top',
                    ]);
                }
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'email',
                'content' => function ($model, $key, $index, $column) {
                    return Editable::widget([
                        'model' => $model,
                        'attribute' => 'email',
                        'type' => 'text',
                        'url' => ['editable'],
                        'mode' => 'popup',
                        'placement' => 'top',
                    ]);
                }
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'is_admin',
                'content' => function ($model, $key, $index, $column) {
                    $userModel = Yii::createObject(User::className());

                    $value = Yii::t('wavecms/user', 'No');
                    if ($model->is_admin === $userModel::IS_ADMIN_YES) {
                        $value = Yii::t('wavecms/user', 'Yes');
                    }

                    return Editable::widget([
                        'model' => $model,
                        'attribute' => 'is_admin',
                        'type' => 'select',
                        'url' => ['editable'],
                        'mode' => 'popup',
                        'placement' => 'top',
                        'value' => $value,

                        'clientOptions' => [
                            'value' => $model->is_admin,
                            'source' => [
                                ['value' => $userModel::IS_ADMIN_NO, 'text' => Yii::t('wavecms/user', 'No')],
                                ['value' => $userModel::IS_ADMIN_YES, 'text' => Yii::t('wavecms/user', 'Yes')]
                            ],
                        ]
                    ]);
                },
                'filter' => [
                    $userModel::IS_ADMIN_NO => Yii::t('wavecms/user', 'No'),
                    $userModel::IS_ADMIN_YES => Yii::t('wavecms/user', 'Yes')
                ],
            ],
            [
                'class' => DataColumn::className(),
                'attribute' => 'status',
                'content' => function ($model, $key, $index, $column) {
                    $userModel = Yii::createObject(User::className());

                    $value = Yii::t('wavecms/user', 'Not active');
                    if ($model->status === $userModel::STATUS_ACTIVE) {
                        $value = Yii::t('wavecms/user', 'Active');
                    }

                    return Editable::widget([
                        'model' => $model,
                        'attribute' => 'status',
                        'type' => 'select',
                        'url' => ['editable'],
                        'mode' => 'popup',
                        'placement' => 'top',
                        'value' => $value,

                        'clientOptions' => [
                            'value' => $model->status,
                            'source' => [
                                ['value' => $userModel::STATUS_DELETED, 'text' => Yii::t('wavecms/user', 'Not active')],
                                ['value' => $userModel::STATUS_ACTIVE, 'text' => Yii::t('wavecms/user', 'Active')]
                            ],
                        ]
                    ]);
                },
                'filter' => [
                    $userModel::IS_ADMIN_NO => Yii::t('wavecms/user', 'No'),
                    $userModel::IS_ADMIN_YES => Yii::t('wavecms/user', 'Yes')
                ],
            ],

            [
                'class' => DataColumn::className(),
                'attribute' => 'roles',
                'content' => function ($model, $key, $index, $column) {
                    $roles = explode(',', $model->roles);

                    $column = [];
                    foreach (explode(',', $model->roles) as $role) {
                        $class = 'label-light-gray';

                        if ($role === 'Super admin') {
                            $class = 'label-primary';
                        }

                        $column[] = '<span class="label ' . $class . '">' . $role . '</span>';
                    }

                    return Html::a(
                            FontAwesome::icon('exchange'),
                            ['assign', 'id' => $model->id],
                            [
                                'class' => 'btn btn-xs btn-default'
                            ]) . ' ' . implode(' ', $column);
                },
                'filter' => ArrayHelper::map(AuthAssignment::find()->select('item_name')->asArray()->all(), 'item_name', 'item_name')
            ],
            [
                'class' => ActionColumn::className(),
            ],
        );

        $this->filterModel = Yii::createObject(UserSearch::className());

        $this->on(self::EVENT_BEFORE_MODEL_SAVE, function ($event) {
            if ($event->model->password) {
                $event->model->setPassword($event->model->password);
                $event->model->generateAuthKey();

                Flash::message('password_changed', 'success', ['message' => Yii::t('wavecms/user', 'Password has been changed')]);
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
        $this->view->params['h1'] = Yii::t('wavecms/user', 'Assign role to user <b>{user}</b>', ['user' => $model->email]);
        $this->view->title = $this->view->params['h1'];

        array_unshift($this->view->params['buttons_top'], Html::a('Return', $this->returnUrl, ['class' => 'btn btn-default']));
        NavHelper::$active[] = 'assign';


        $roleForm = Yii::createObject(AssignRoleForm::className());
        $authAssignmentModel = Yii::createObject(AuthAssignment::className());

        $assignedRolesDataProvider = new ActiveDataProvider([
            'query' => $authAssignmentModel::find()->where(['user_id' => $id])
        ]);

        if (Yii::$app->request->isPost) {
            $roleForm->load(Yii::$app->request->post());
            if ($roleForm->validate()) {
                $auth = Yii::$app->authManager;
                $role = $auth->getRole($roleForm->role);

                if (!$role)
                    throw new NotFoundHttpException(Yii::t('wavecms/user', 'Role not found'));

                $auth->assign($role, $id);

                Flash::message(
                    'after_create',
                    'success',
                    ['message' => Yii::t('wavecms/user', 'Role has been assigned')]
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
            throw new NotFoundHttpException(Yii::t('wavecms/user', 'Role not found'));

        $auth->revoke($role, $model->id);

        Flash::message('after_create', 'success', ['message' => Yii::t('wavecms/user', 'Role has been revoked')]);

        return $this->redirect([
            'assign',
            'id' => $model->id
        ]);
    }

    protected function _fetchOne($id)
    {
        $query = $this->query;
        $modelClass = Yii::createObject($query->modelClass);

        /** @var ActiveRecord $model */
        $model = $query->andWhere([$modelClass::tableName() . '.id' => $id])->one();

        if (!$model)
            throw new NotFoundHttpException(Yii::t('wavecms/main', 'Element not found'));

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
            throw new InvalidConfigException(Yii::t('wavecms/main', 'The "query" property must be set.'));

        if (!$this->query instanceof ActiveQuery)
            throw new InvalidConfigException(Yii::t('wavecms/main', 'The "query" property is not instance of ActiveQuery.'));
    }

}