<?php

namespace app\models;

use Yii;
use app\components\LevelCallbackData;

/**
 * This is the model class for table "{{%level}}".
 *
 * @property integer $id
 * @property integer $game_id
 * @property integer $position
 * @property integer $created_by
 * @property integer $created_on
 * @property resource $data
 * @property string $title
 *
 * @property Filter $filter
 * @property User $createdBy
 * @property Game $game
 */
class Level extends \yii\db\ActiveRecord {
	public $levelData;
	public $levelInputCallback;
	public $loadLevelDataCallback;
	public $restrictions;
	public $unserializedData;
	private $levelFields;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%level}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['game_id'], 'required'],
            [['id', 'game_id', 'position', 'created_by', 'created_on'], 'integer'],
            [['data', 'title'], 'string'],
			[['levelData'], 'levelDataValidation'],
			[['restrictions'], 'safe'],
        ];
    }

	/**
	 */
	public function levelDataValidation($attribute, $params) {
		// Se n�o houver callback de valida��o, entende-se que n�o � necess�ria
		if (!$this->levelInputCallback) {
			$this->unserializedData = $this->$attribute;
			return;
		}

		// Constr�i o objeto que guarda as informa��es
		$data = new LevelCallbackData();
		$data->output = ($this->isNewRecord ? [] : $this->unserializedData);
		$data->errors = [];

		// Chama a fun��o de valiza��o
		call_user_func_array($this->levelInputCallback, [$this, $this->$attribute, &$data]);

		// Se a fun��o retornou true ou uma array vazia, salva o resultado
		if ($data->success()) {
			$this->unserializedData = $data->output;
		} else {
			// Caso contr�rio, adiciona todos os erros
			foreach ($data->errors as $error)
				$this->addError('', $error);
		}
    }

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		if (parent::beforeSave($insert)) {
			$this->data = serialize($this->unserializedData);
			return true;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function afterFind() {
		$this->unserializedData = unserialize($this->data);
	}

	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		if ($this->restrictions) {
			$transaction = $this->db->beginTransaction();
			try {
				foreach($this->restrictions['type'] as $index => $restriction) {
					$type = $restriction;
					if (!isset($this->restrictions['target'][$index])) continue;
					$target = $this->restrictions['target'][$index];

					$filter = new Filter();
					$filter->game_id = $this->game_id;
					$filter->level_id = $this->id;
					$filter->target_type = $type;
					$filter->target_id = $target;
					$filter->generateIdentifier();
					$filter->save();
				}
				$transaction->commit();
			} catch (Exception $e) {
				$transaction->rollBack();
			}
		}
		parent::afterSave($insert, $changedAttributes);
	}

	/**
	 * @return string
	 */
	public function getFieldName($key) {
		$pos = strpos($key, '[');
		if ($pos === false) {
			return ('levelData[' . $key . ']');
		} else {
			return ('levelData[' . substr($key, 0, $pos) . '][' . substr($key, $pos + 1));
		}
	}

	/**
	 * Carrega os dados do jogo que est�o salvos no banco de dados
	 */
	public function loadLevelFields() {
		// Se n�o houver callback de carregamento, entende-se que n�o � necess�rio
		if (!$this->loadLevelDataCallback) {
			$this->levelFields = $this->unserializedData;
			return;
		}

		// Chama a fun��o de valiza��o
		$this->levelFields = [];
		call_user_func_array($this->loadLevelDataCallback, [$this->unserializedData, &$this->levelFields]);

	}

	/**
	 * @return mixed
	 */
	public function getFieldValue($key) {
		return (isset($this->levelFields[$key]) ? $this->levelFields[$key] : null);
	}

	/**
	 * @return null|array
	 */
	public function getDetailViewAttributes() {
		return ($this->game ? $this->game->getDetailViewAttributes($this) : null);
	}

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'game_id' => Yii::t('app', 'Game ID'),
            'position' => Yii::t('app', 'Position'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_on' => Yii::t('app', 'Created On'),
            'data' => Yii::t('app', 'Data'),
            'title' => Yii::t('app', 'Title'),
        ];
    }

	/**
	 * @return boolean
	 */
	public function getRestricted() {
		return (count($this->filters) > 0);
	}

	/**
	 * @return boolean
	 */
	public function scoredBy($userId) {
		return ($this->hasMany(Score::className(), ['game_id' => 'game_id', 'level_id' => 'id'])->where(['user_id' => $userId])->count() > 0);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getScores() {
		return $this->hasMany(Score::className(), ['game_id' => 'game_id', 'level_id' => 'id']);
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFilters() {
        return $this->hasMany(Filter::className(), ['game_id' => 'game_id', 'level_id' => 'id']);
    }

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getCohortFilters() {
        return $this->hasMany(Filter::className(), ['game_id' => 'game_id', 'level_id' => 'id'])->where(['target_type' => Filter::TARGET_COHORT]);
    }

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentFilters() {
        return $this->hasMany(Filter::className(), ['game_id' => 'game_id', 'level_id' => 'id'])->where(['target_type' => Filter::TARGET_STUDENT]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGame() {
        return $this->hasOne(Game::className(), ['id' => 'game_id']);
    }

	/**
	 * Encontra as melhores pontuações do nível
	 * @return null|array
	 */
	public function getBestScores($limit = 3) {
		return $this->getScores()
			->distinct('user_id')
			->addSelect(['user_id', 'SUM(points) AS sumPoints'])
			->groupBy('user_id')
			->orderBy('sumPoints DESC')
			->limit($limit)
			->all();
	}
}
