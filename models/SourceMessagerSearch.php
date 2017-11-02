<?php

namespace mrstroz\wavecms\models;

use Yii;
use yii\data\ActiveDataProvider;

class SourceMessagerSearch extends SourceMessage
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
            ['like', SourceMessage::tableName().'.category', $this->category],
            ['like', SourceMessage::tableName().'.message', $this->message],
            ['like', MEssage::tableName().'.translation', $this->translation]
        ]);


        return $dataProvider;
    }


}
