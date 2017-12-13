<?php

namespace mrstroz\wavecms\models\search;

use mrstroz\wavecms\models\Message;
use mrstroz\wavecms\models\SourceMessage;
use Yii;
use yii\data\ActiveDataProvider;

class SourceMessageSearch extends SourceMessage
{


    public function rules()
    {
        return [
            [['category', 'message', 'translation'], 'string'],
        ];
    }

    /**
     * @param $dataProvider ActiveDataProvider
     * @return mixed
     */
    public function search($dataProvider)
    {
        $params = Yii::$app->request->get();

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $dataProvider->query->andFilterWhere(['or',
            ['like', SourceMessage::tableName() . '.category', $this->category],
            ['like', SourceMessage::tableName() . '.message', $this->message],
            ['like', Message::tableName() . '.translation', $this->translation]
        ]);


        return $dataProvider;
    }


}
