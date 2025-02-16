<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SeoSettings;

class SeoSettingsSearch extends Model
{
    public $page_url;
    public $title;
    public $description;

    public function rules()
    {
        return [
            [['page_url', 'title', 'description'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = SeoSettings::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'page_url' => SORT_ASC,
                ]
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'page_url', $this->page_url])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
