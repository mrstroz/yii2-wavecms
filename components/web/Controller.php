<?php

namespace mrstroz\wavecms\components\web;

use himiklab\sortablegrid\SortableGridAction;
use mrstroz\wavecms\components\actions\BulkDeleteAction;
use mrstroz\wavecms\components\actions\BulkPublishAction;
use mrstroz\wavecms\components\actions\BulkUnpublishAction;
use mrstroz\wavecms\components\actions\CreateAction;
use mrstroz\wavecms\components\actions\DeleteAction;
use mrstroz\wavecms\components\actions\DeleteSubListAction;
use mrstroz\wavecms\components\actions\IndexAction;
use mrstroz\wavecms\components\actions\PageAction;
use mrstroz\wavecms\components\actions\PublishAction;
use mrstroz\wavecms\components\actions\SettingsAction;
use mrstroz\wavecms\components\actions\SubListAction;
use mrstroz\wavecms\components\actions\UpdateAction;
use mrstroz\wavecms\components\actions\UpDownAction;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Class Controller
 * @package mrstroz\wavecms\components\web
 * Main controller class used to manage data in Wavecms modules
 */
class Controller extends BaseController
{

    /**
     * @var string Type of controller (list,page,settings)
     */
    public $type = 'list';

    /**
     * @var ActiveQuery Query object
     */
    public $query;

    /**
     * @var string Class name of the model which will be used to validate the attributes in settings action
     */
    public $modelClass;

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
     * @var string Form view used for create, update, page, settings action
     */
    public $viewForm = 'form';

    /**
     * @var bool Set if GridView is sortable by drag & drop
     */
    public $sort = false;

    /** @var bool Automatically set to true when Model has publishColumn */
    public $publish = false;

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
    public $activeActions = ['create', 'update', 'page'];

    /**
     * @var array Available bulk actions
     */
    public $bulkActions;


    /**
     * @var string Event after model create
     */
    const EVENT_AFTER_MODEL_CREATE = 'eventAfterModelCreate';
    const EVENT_BEFORE_MODEL_SAVE = 'eventBeforeModelSave';
    const EVENT_AFTER_MODEL_SAVE = 'eventAfterModelSave';
    const EVENT_CONTROLLER_INIT = 'eventControllerInit';
    const EVENT_BEFORE_RENDER = 'eventBeforeRender';

    public function init()
    {
        if ($this->query) {
            if ($this->bulkActions === null) {
                $this->bulkActions['bulk-delete'] = Yii::t('wavecms/main', 'Delete');

                /** @var ActiveRecord $modelClass */
                $modelClass = Yii::createObject($this->query->modelClass);

                if ($modelClass->hasAttribute($this->publishColumn)) {
                    $this->bulkActions['bulk-publish'] = Yii::t('wavecms/main', 'Publish');
                    $this->bulkActions['bulk-unpublish'] = Yii::t('wavecms/main', 'Unpublish');
                }
            }
        }

        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * Actions
     * @return array
     */
    public function actions()
    {
        $actions = [];

        if ($this->type === 'list') {
            if (isset($this->query)) {
                $actions['sort'] = [
                    'class' => SortableGridAction::className(),
                    'modelName' => $this->query->modelClass
                ];
            }

            $actions['index'] = [
                'class' => IndexAction::className()
            ];

            $actions['sub-list'] = [
                'class' => SubListAction::className()
            ];

            $actions['create'] = [
                'class' => CreateAction::className()
            ];

            $actions['update'] = [
                'class' => UpdateAction::className()
            ];

            $actions['delete'] = [
                'class' => DeleteAction::className()
            ];

            $actions['delete-sub-list'] = [
                'class' => DeleteSubListAction::className()
            ];

            $actions['publish'] = [
                'class' => PublishAction::className()
            ];

            $actions['up-down'] = [
                'class' => UpDownAction::className()
            ];

            if (isset($this->bulkActions['bulk-delete'])) {
                $actions['bulk-delete'] = [
                    'class' => BulkDeleteAction::className()
                ];
            }

            if (isset($this->bulkActions['bulk-publish'])) {
                $actions['bulk-publish'] = [
                    'class' => BulkPublishAction::className()
                ];
            }

            if (isset($this->bulkActions['bulk-unpublish'])) {
                $actions['bulk-unpublish'] = [
                    'class' => BulkUnpublishAction::className()
                ];
            }
        }

        if ($this->type === 'page') {
            $actions['page'] = [
                'class' => PageAction::className()
            ];
        }

        if ($this->type === 'settings') {
            $actions['settings'] = [
                'class' => SettingsAction::className()
            ];
        }

        return $actions;
    }

    /**
     * Behaviors
     * @return array
     */
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
                            'delete',
                            'delete-sub-list',
                            'publish',
                            'up-down',
                            'bulk-delete',
                            'bulk-publish',
                            'bulk-unpublish',
                            'sort',
                            'page',
                            'settings',
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
}