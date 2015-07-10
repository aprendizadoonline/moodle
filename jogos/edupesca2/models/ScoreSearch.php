<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Score;

/**
 * ScoreSearch represents the model behind the search form about `app\models\Scoe`.
 */
class ScoreSearch extends Score {
    /**
     * Variáveis adicionais
     */
    public $gameName;
    public $levelTitle;
    private $userId;

    /**
     * @inheritdoc
     */
    public function __construct($userId = null) {
        $this->userId = $userId;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'game_id', 'level_id', 'date', 'points'], 'integer'],
            [['gameName', 'levelTitle'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params) {
        $query = Score::find()
            ->joinWith(['level', 'game']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $dataProvider->sort->attributes['gameName'] = [
            'asc' => ['game.name' => SORT_ASC],
            'desc' => ['game.name' => SORT_DESC],
            'label' => Yii::t('app', 'Game Name'),
            'default' => SORT_ASC
        ];

        $dataProvider->sort->attributes['levelTitle'] = [
            'asc' => ['level.title' => SORT_ASC],
            'desc' => ['game.name' => SORT_DESC],
            'label' => Yii::t('app', 'Level Title'),
            'default' => SORT_ASC
        ];

        $this->load($params);

        if (!$this->validate())
            return $dataProvider;

        $query->andFilterWhere([
            'user_id' => ($this->userId !== null ? $this->userId : $this->user_id),
            'level_id' => $this->level_id,
            'game_id' => $this->game_id,
            'date' => $this->date,
            'points' => $this->points,
        ]);

        $query->andFilterWhere(['like', 'game.name', $this->gameName]);
        $query->andFilterWhere(['like', 'level.title', $this->levelTitle]);

        return $dataProvider;
    }
}
