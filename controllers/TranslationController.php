<?php

namespace mrstroz\wavecms\controllers;

use mrstroz\wavecms\components\grid\ActionColumn;
use mrstroz\wavecms\components\grid\CheckboxColumn;
use mrstroz\wavecms\components\web\Controller;
use mrstroz\wavecms\models\Message;
use mrstroz\wavecms\models\search\SourceMessagerSearch;
use mrstroz\wavecms\models\search\SourceMessageSearch;
use mrstroz\wavecms\models\SourceMessage;
use Yii;
use yii\data\ActiveDataProvider;

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
            'translation',
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


}