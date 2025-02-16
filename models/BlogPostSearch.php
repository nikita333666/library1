<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BlogPostSearch represents the model behind the search form of `app\models\BlogPost`.
 */
class BlogPostSearch extends BlogPost
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'author_id', 'views'], 'integer'],
            [['title', 'content', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BlogPost::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'author_id' => $this->author_id,
            'views' => $this->views,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'author_firstname', $this->author_firstname])
            ->andFilterWhere(['like', 'author_lastname', $this->author_lastname]);

        return $dataProvider;
    }
}
