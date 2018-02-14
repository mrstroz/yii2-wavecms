<?php

namespace mrstroz\wavecms\controllers;

use dosamigos\editable\Editable;
use mrstroz\wavecms\components\grid\ActionColumn;
use mrstroz\wavecms\components\grid\CheckboxColumn;
use mrstroz\wavecms\components\web\Controller;
use mrstroz\wavecms\models\Message;
use mrstroz\wavecms\models\search\SourceMessagerSearch;
use mrstroz\wavecms\models\search\SourceMessageSearch;
use mrstroz\wavecms\models\SourceMessage;
use Yii;
use yii\data\ActiveDataProvider;
use yii\grid\DataColumn;

class TranslationController extends Controller
{

    public function init()
    {
        $this->heading = Yii::t('wavecms/main', 'Translations');

        $sourceMessageModel = Yii::createObject(SourceMessage::class);
        $messageModel = Yii::createObject(Message::class);

        $this->query = $sourceMessageModel::find()
            ->select([
                $sourceMessageModel::tableName() . '.*',
                $messageModel::tableName() . '.translation'
            ])
            ->leftJoin($messageModel::tableName(),
                $sourceMessageModel::tableName() . '.id = ' . $messageModel::tableName() . '.id 
            AND ' . $messageModel::tableName() . '.language = "' . Yii::$app->wavecms->editedLanguage . '"'
            );

        $this->dataProvider = new ActiveDataProvider([
            'query' => $this->query,
        ]);

        $this->dataProvider->sort->attributes['translation'] = [
            'asc' => [$messageModel::tableName() . '.translation' => SORT_ASC],
            'desc' => [$messageModel::tableName() . '.translation' => SORT_DESC],
        ];

        $this->filterModel = Yii::createObject(SourceMessageSearch::class);

        $this->columns = array(
            [
                'class' => CheckboxColumn::className()
            ],
            'category',
            'message',
            [
                'class' => DataColumn::className(),
                'attribute' => 'translation',
                'content' => function ($model, $key, $index, $column) {
                    return Editable::widget([
                        'model' => $model,
                        'attribute' => 'translation',
                        'type' => 'textarea',
                        'url' => ['translation-editable'],
                        'id' => $model->message_id,
                        'mode' => 'popup',
                        'placement' => 'left'
                    ]);
                }
            ],
            [
                'class' => ActionColumn::className(),
            ],
        );


        $this->on(self::EVENT_AFTER_MODEL_SAVE, function ($event) {

            $messageModel = Yii::createObject(Message::class);

            if ($event->model) {
                $message = $messageModel::find()->where(
                    ['id' => $event->model->id, 'language' => Yii::$app->wavecms->editedLanguage]
                )->one();

                if (!$message) {
                    $message = Yii::createObject(Message::class);
                    $message->id = $event->model->id;
                    $message->language = Yii::$app->wavecms->editedLanguage;
                }

                $message->translation = $event->model->translation;
                $message->save();
            }
        });


        parent::init();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => [
                'translation-editable',
            ],
            'roles' => [
                '@'
            ],
        ];

        return $behaviors;
    }

    public function actionTranslationEditable()
    {

        $pk = Yii::$app->request->post('pk');
        $pk = unserialize(base64_decode($pk));
        $attribute = Yii::$app->request->post('name');
        $value = Yii::$app->request->post('value');

        if ($attribute === null) {
            throw new BadRequestHttpException("'name' parameter cannot be empty.");
        }
        if ($value === null) {
            throw new BadRequestHttpException("'value' parameter cannot be empty.");
        }

        $messageModel = Yii::createObject(Message::class);

        /** @var \yii\db\ActiveRecord $model */
        $model = $messageModel::find()->where(['id' => $pk, 'language' => Yii::$app->wavecms->editedLanguage])->one();
        if (!$model) {
            $model = Yii::createObject(Message::class);
            $model->id = $pk;
            $model->language = Yii::$app->wavecms->editedLanguage;
        }

        if ($this->scenario !== null) {
            $model->setScenario($this->scenario);
        }
        $model->$attribute = $value;

        if ($model->validate([$attribute])) {
            // no need to specify which attributes as Yii2 handles that via [[BaseActiveRecord::getDirtyAttributes]]
            return $model->save(false);
        }
        throw new BadRequestHttpException($model->getFirstError($attribute));
    }


}