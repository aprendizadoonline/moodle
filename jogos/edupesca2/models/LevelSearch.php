<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Level;

/**
 * LevelSearch represents the model behind the search form about `app\models\Level`.
 */
class LevelSearch extends Level
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'game_id', 'position', 'created_by', 'created_on', 'restricted'], 'integer'],
            [['data'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
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
        $query = Level::find()->orderBy('position ASC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=>false
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'game_id' => $this->game_id,
            'position' => $this->position,
            'created_by' => $this->created_by,
            'created_on' => $this->created_on,
        ]);

        $query
			->andFilterWhere(['like', 'title', $this->title])
			->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
