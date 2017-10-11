<?php

namespace mrstroz\wavecms\components\web;

use himiklab\sortablegrid\SortableGridAction;
use mrstroz\wavecms\components\behaviors\TranslateBehavior;
use mrstroz\wavecms\components\event\ModelEvent;
use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\helpers\NavHelper;
use mrstroz\wavecms\models\AuthItem;
use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller as yiiController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class Controller
 * @package mrstroz\wavecms\components\web
 * Main controller class used to manage data in Wavecms modules
 */
class Controller extends yiiController
{

    /**
     * @var string Admin layout
     */
    public $layout = '@wavecms/views/layouts/admin.php';

    /**
     * @var ActiveQuery Query object
     */
    public $query;

    /**
     * @var string Scenario for model
     */
    public $scenario;

    /**
     * @var ActiveDataProvider DataProvider for index grid
     */
    public $dataProvider;

    /**
     * @var ActiveRecord filter model for GridViews on index action
     */
    public $filterModel;

    /**
     * @var array Columns displayed in GridView on index action
     */
    public $columns = [];

    /**
     * @var string Index view
     */
    public $viewIndex = '@wavecms/views/crud/index';

    /**
     * @var string Sublist view
     */
    public $viewSubList = '@wavecms/views/crud/subList';

    /**
     * @var string Form view used for create, update, page action
     */
    public $viewForm = 'form';

    /**
     * @var bool Set if GridView is sortable by drag & drop
     */
    public $sort = false;

    /**
     * @var string Model column used for publish
     */
    public $publishColumn = 'publish';

    /**
     * @var static Heading for index and create / update pages
     */
    public $heading;

    /**
     * @var array Url used for "Return" button and redirects to back to parent page
     */
    public $returnUrl;

    /**
     * @var array List of GET params that should be forwarded to each actions
     */
    public $forwardParams = [];

    /**
     * @var array List of active actions to set in left menu
     */
    public $activeActions = ['create', 'update'];

    /**
     * @var array Actions that are not checked in `beforeAction` function with RBAC permissions
     */
    public $actionsDisabledFromAccessControl = [];


    /**
     * @var string Event after model create
     */
    const EVENT_AFTER_MODEL_CREATE = 'eventAfterModelCreate';
    const EVENT_BEFORE_MODEL_SAVE = 'eventBeforeModelSave';
    const EVENT_AFTER_MODEL_SAVE = 'eventAfterModelSave';
    const EVENT_CONTROLLER_INIT = 'eventControllerInit';


    /**
     * @return array
     * Additional actions
     */
    public function actions()
    {
        $actions = [];

        if (isset($this->query)) {
            $actions['sort'] = [
                'class' => SortableGridAction::className(),
                'modelName' => $this->query->modelClass
            ];
        }

        return $actions;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index',
                            'sub-list',
                            'create',
                            'update',
                            'page',
                            'delete',
                            'delete-sub-list',
                            'publish',
                            'up-down',
                            'sort'
                        ],
                        'roles' => [
                            '@'
                        ],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }

    public function init()
    {
        parent::init();
    }

    public function beforeAction($action)
    {
        if (!in_array(Yii::$app->controller->action->id, $this->actionsDisabledFromAccessControl)) {
            if (!Yii::$app->user->can(AuthItem::SUPER_ADMIN)) {
                if (!Yii::$app->user->can(Yii::$app->controller->module->id . '/' . Yii::$app->controller->id)) {
                    if (!Yii::$app->user->isGuest) {
                        throw new ForbiddenHttpException(Yii::t('wavecms/base/main', 'You are not allowed to perform this action'));
                    }
                }
            }
        }

        return parent::beforeAction($action);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidParamException
     * Index action - display GridView with all entries form $this->query model.
     */
    public function actionIndex()
    {
        $this->_checkConfig();

        $this->view->params['h1'] = $this->heading;
        array_unshift($this->view->params['buttons_top'],
            Html::a(Yii::t('wavecms/base/main', 'Create new'), array_merge(['create'], $this->_forwardParams()), ['class' => 'btn btn-primary']));

        /**
         * Create dataProvider based on $this->query if not exist
         */
        if (!$this->dataProvider) {
            $this->dataProvider = new ActiveDataProvider([
                'query' => $this->query
            ]);
        }

        if ($this->sort) {
            $modelClass = $this->query->modelClass;
            $this->query->orderBy($modelClass::tableName() . '.sort');

            $this->dataProvider->sort = false;
        }

        if ($this->filterModel) {
            $this->filterModel->search($this->dataProvider);
        }

        $this->view->title = $this->view->params['h1'];
        return $this->render($this->viewIndex, array(
            'dataProvider' => $this->dataProvider,
            'filterModel' => $this->filterModel,
            'columns' => $this->columns,
            'sort' => $this->sort
        ));
    }

    public function actionSubList($parentField, $parentId, $parentRoute)
    {
        $this->_checkConfig();
        $this->layout = false;

        $this->query->andWhere([$parentField => $parentId]);

        array_unshift($this->view->params['buttons_sublist'],
            Html::a(Yii::t('wavecms/base/main', 'Create new'),
                array_merge(['create', 'parentField' => $parentField, 'parentId' => $parentId, 'parentRoute' => $parentRoute], $this->_forwardParams()), ['class' => 'btn btn-primary btn-sm btn-sub-list']));

        /**
         * Create dataProvider based on $this->query if not exist
         */
        if (!$this->dataProvider) {
            $this->dataProvider = new ActiveDataProvider([
                'query' => $this->query,
                'sort' => false
            ]);
        }

        if ($this->sort) {
            $modelClass = $this->query->modelClass;
            $this->query->orderBy($modelClass::tableName() . '.sort');

            $this->dataProvider->sort = false;
        }

        if ($this->filterModel) {
            $this->filterModel->search($this->dataProvider);
        }


        return $this->render($this->viewSubList, array(
            'dataProvider' => $this->dataProvider,
            'filterModel' => $this->filterModel,
            'columns' => $this->columns,
            'sort' => $this->sort
        ));
    }

    public function actionCreate($parentField = null, $parentId = null, $parentRoute = null)
    {
        $this->_checkConfig();

        $this->view->params['h1'] = $this->heading;

        if (!$this->returnUrl) {
            if ($parentField && $parentId && $parentRoute) {
                $this->returnUrl = array_merge(['/' . ltrim(urldecode($parentRoute), '/'), 'id' => $parentId], $this->_forwardParams());
            } else {
                $this->returnUrl = array_merge(['index'], $this->_forwardParams());
            }
        }
        array_unshift($this->view->params['buttons_top'], Html::a(Yii::t('wavecms/base/main', 'Return'), $this->returnUrl, ['class' => 'btn btn-default']));


        /***
         * Set index element as active in left navigation
         */
        NavHelper::$active = $this->activeActions;

        $modelClass = $this->query->modelClass;
        $model = new $modelClass();
        if ($this->scenario) {
            $model->scenario = $this->scenario;
        }

        foreach ($model->behaviors as $behavior) {
            if ($behavior instanceof TranslateBehavior) {
                $behavior->setLanguage(Yii::$app->wavecms->editedLanguage);
            }
        }

        $eventModel = new ModelEvent();
        $eventModel->model = $model;
        $this->trigger(self::EVENT_AFTER_MODEL_CREATE, $eventModel);

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->validate()) {

                if ($parentField && $parentId && $parentRoute) {
                    $model->{$parentField} = $parentId;
                }

                $this->trigger(self::EVENT_BEFORE_MODEL_SAVE, $eventModel);
                $model->save();
                $this->trigger(self::EVENT_AFTER_MODEL_SAVE, $eventModel);

                Flash::message('after_create', 'success', ['message' => Yii::t('wavecms/base/main', 'Element has been created')]);

                if (Yii::$app->request->post('save_and_return')) {
                    return $this->redirect($this->returnUrl);
                }

                return $this->redirect(array_merge([
                    'update',
                    'id' => $model->id,
                    'parentField' => $parentField,
                    'parentId' => $parentId,
                    'parentRoute' => $parentRoute,
                ], $this->_forwardParams()));

            }

            Flash::message('create_error', 'danger', ['message' => Html::errorSummary($model)]);
        }

        $this->view->title = $this->view->params['h1'];
        return $this->render($this->viewForm, array(
            'model' => $model
        ));
    }

    public function actionUpdate($id, $parentField = null, $parentId = null, $parentRoute = null)
    {
        $this->_checkConfig();

        $this->view->params['h1'] = $this->heading;

        if (!$this->returnUrl) {
            if ($parentField && $parentId && $parentRoute) {
                $this->returnUrl = array_merge(['/' . ltrim(urldecode($parentRoute), '/'), 'id' => $parentId], $this->_forwardParams());
            } else {
                $this->returnUrl = array_merge(['index'], $this->_forwardParams());
            }
        }

        array_unshift($this->view->params['buttons_top'], Html::a(Yii::t('wavecms/base/main', 'Return'), $this->returnUrl, ['class' => 'btn btn-default']));

        /***
         * Set index element as active in left navigation
         */
        NavHelper::$active = $this->activeActions;

        $model = $this->_fetchOne($id);

        foreach ($model->behaviors as $behavior) {
            if ($behavior instanceof TranslateBehavior) {
                $behavior->setLanguage(Yii::$app->wavecms->editedLanguage);
            }
        }

        $eventModel = new ModelEvent();
        $eventModel->model = $model;
        $this->trigger(self::EVENT_AFTER_MODEL_CREATE, $eventModel);

        if (Yii::$app->request->isPost) {

            $model->load(Yii::$app->request->post());

            if ($model->validate()) {

                $this->trigger(self::EVENT_BEFORE_MODEL_SAVE, $eventModel);
                $model->save();
                $this->trigger(self::EVENT_AFTER_MODEL_SAVE, $eventModel);

                Flash::message('after_update', 'success', ['message' => Yii::t('wavecms/base/main', 'Element has been updated')]);

                if (Yii::$app->request->post('save_and_return')) {
                    return $this->redirect($this->returnUrl);
                }

                return $this->redirect(array_merge([
                    'update',
                    'id' => $model->id,
                    'parentField' => $parentField,
                    'parentId' => $parentId,
                    'parentRoute' => $parentRoute,
                ], $this->_forwardParams()));
            }

            Flash::message('create_error', 'danger', ['message' => Html::errorSummary($model)]);
        }

        $this->view->title = $this->view->params['h1'];
        return $this->render($this->viewForm, array(
            'model' => $model
        ));
    }

    public function actionPage()
    {
        $this->_checkConfig();

        $this->view->params['h1'] = $this->heading;

        $query = $this->query;
        $model = $query->one();

        if (!$model) {
            $modelClass = $query->modelClass;
            $model = new $modelClass();
        }

        if ($this->scenario) {
            $model->scenario = $this->scenario;
        }

        foreach ($model->behaviors as $behavior) {
            if ($behavior instanceof TranslateBehavior) {
                $behavior->setLanguage(Yii::$app->wavecms->editedLanguage);
            }
        }

        $eventModel = new ModelEvent();
        $eventModel->model = $model;
        $this->trigger(self::EVENT_AFTER_MODEL_CREATE, $eventModel);

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->validate()) {

                $this->trigger(self::EVENT_BEFORE_MODEL_SAVE, $eventModel);
                $model->save();
                $this->trigger(self::EVENT_AFTER_MODEL_SAVE, $eventModel);

                Flash::message('after_update', 'success', ['message' => Yii::t('wavecms/base/main', 'Element has been updated')]);
            } else {
                Flash::message('create_error', 'danger', ['message' => Html::errorSummary($model)]);
            }
        }

        $this->view->title = $this->view->params['h1'];
        return $this->render($this->viewForm, array(
            'model' => $model
        ));

    }

    public function actionDelete($id, $parentField = null, $parentId = null, $parentRoute = null)
    {

        if (!$this->returnUrl) {
            if ($parentField && $parentId && $parentRoute) {
                $this->returnUrl = array_merge(['/' . ltrim(urldecode($parentRoute), '/'), 'id' => $parentId], $this->_forwardParams());
            } else {
                $this->returnUrl = array_merge(['index'], $this->_forwardParams());
            }
        }

        $this->_checkConfig();
        $model = $this->_fetchOne($id);

        $model->delete();
        Flash::message('delete', 'success', ['message' => Yii::t('wavecms/base/main', 'Element has been deleted')]);
        return $this->redirect($this->returnUrl);
    }

    public function actionDeleteSubList($parentField, $parentId, $parentRoute)
    {
        $this->_checkConfig();
        $query = $this->query;
        $modelClass = $query->modelClass;
        /** @var ActiveRecord $model */
        $models = $query->andWhere([$modelClass::tableName() . '.' . $parentField => $parentId])->all();

        if ($models) {
            foreach ($models as $model) {
                if ($this->scenario) {
                    $model->scenario = $this->scenario;
                }
                $model->delete();
            }
        }
        Flash::message('delete_sub_list', 'success', ['message' => Yii::t('wavecms/base/main', 'Elements from list "{heading}" has been deleted', ['heading' => $this->heading])]);
    }

    public function actionPublish($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->_checkConfig();
        $model = $this->_fetchOne($id);


        if ($model->{$this->publishColumn} === 1) {
            $model->{$this->publishColumn} = 0;
        } else {
            $model->{$this->publishColumn} = 1;
        }

        $model->save(false);

        return ['publish' => $model->{$this->publishColumn}, 'model' => $model->toArray()];

    }

    public function actionUpDown($id, $dir)
    {
        $upDownQuery = clone $this->query;
        $model = $this->_fetchOne($id);

        if (!$model)
            throw new NotFoundHttpException(Yii::t('wavecms/base/main', 'Element not found'));

        $compare = '<';
        $order = 'sort DESC';
        if ($dir === 'down') {
            $compare = '>';
            $order = 'sort ASC';
        }

        $query = $this->query;
        $modelClass = $query->modelClass;
        $modelSort = $upDownQuery->andWhere([$compare, $modelClass::tableName() . '.sort', $model->sort])->orderBy($order)->one();

        if ($modelSort) {
            $sort = $modelSort->sort;
            $modelSort->sort = $model->sort;
            $model->sort = $sort;
            $modelSort->save();
            $model->save();

            if ($dir == 'up') {
                Flash::message('up_down', 'success', ['message' => Yii::t('wavecms/base/main', 'Elements has been moved up')]);
            } else {
                Flash::message('up_down', 'success', ['message' => Yii::t('wavecms/base/main', 'Elements has been moved down')]);
            }

        } else {
            if ($dir == 'up') {
                Flash::message('up_down', 'warning', ['message' => Yii::t('wavecms/base/main', 'Elements cannot be moved up')]);
            } else {
                Flash::message('up_down', 'warning', ['message' => Yii::t('wavecms/base/main', 'Elements cannot be moved down')]);
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
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

    protected function _checkConfig()
    {
        if (!$this->query)
            throw new InvalidConfigException(Yii::t('wavecms/base/main', 'The "query" property must be set.'));

        if (!$this->query instanceof ActiveQuery)
            throw new InvalidConfigException(Yii::t('wavecms/base/main', 'The "query" property is not instance of ActiveQuery.'));
    }

    protected function _forwardParams()
    {
        $return = [];

        foreach ($this->forwardParams as $param) {
            $return[$param] = Yii::$app->request->get($param);
        }

        return $return;
    }

}