<?php

namespace mrstroz\wavecms\controllers;

use mrstroz\wavecms\components\grid\ActionColumn;
use mrstroz\wavecms\components\web\Controller;
use mrstroz\wavecms\models\Message;
use mrstroz\wavecms\models\SourceMessage;
use mrstroz\wavecms\models\SourceMessagerSearch;
use Yii;
use yii\data\ActiveDataProvider;

class TranslationController extends Controller
{

    public function init()
    {
        $this->heading = Yii::t('wavecms/base/main', 'Translations');


        $this->query = SourceMessage::find()
            ->select([
                SourceMessage::tableName() . '.*',
                Message::tableName() . '.translation'
            ])
            ->leftJoin(Message::tableName(),
                SourceMessage::tableName() . '.id = ' . Message::tableName() . '.id 
            AND ' . Message::tableName() . '.language = "' . Yii::$app->wavecms->editedLanguage . '"'
            );

        $this->dataProvider = new ActiveDataProvider([
            'query' => $this->query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        $this->dataProvider->sort->attributes['translation'] = [
            'asc' => [Message::tableName() . '.translation' => SORT_ASC],
            'desc' => [Message::tableName() . '.translation' => SORT_DESC],
        ];

        $this->filterModel = new SourceMessagerSearch();

        $this->columns = array(
            'category',
            'message',
            'translation',
            [
                'class' => ActionColumn::className(),
            ],
        );


        $this->on(self::EVENT_AFTER_MODEL_SAVE, function ($event) {

            if ($event->model) {
                $message = Message::find()->where(
                    ['id' => $event->model->id, 'language' => Yii::$app->wavecms->editedLanguage]
                )->one();

                if (!$message) {
                    $message = new Message();
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