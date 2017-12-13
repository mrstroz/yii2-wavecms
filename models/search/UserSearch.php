<?php

namespace mrstroz\wavecms\models\search;

use mrstroz\wavecms\models\User;
use Yii;
use yii\data\ActiveDataProvider;

class UserSearch extends User
{


    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['is_admin'], 'boolean'],
            [['first_name', 'last_name', 'email', 'status'], 'safe'],
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
            ['user.id' => $this->id],
            ['like', 'user.first_name', $this->first_name],
            ['like', 'user.last_name', $this->last_name],
            ['like', 'user.email', $this->email]
        ]);

        $dataProvider->query->andFilterWhere(['user.is_admin' => $this->is_admin]);
        $dataProvider->query->andFilterWhere(['user.status' => $this->status]);

        return $dataProvider;
    }


}
