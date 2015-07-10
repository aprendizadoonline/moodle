<?php

namespace app\models;

use Yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "{{%game}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $path
 *
 * @property Level[] $levels
 * @property Score[] $scores
 */
class Game extends \yii\db\ActiveRecord
{
	const FIELD_STRING	= 'str';
	const FIELD_BOOL	= 'bool';
	const FIELD_SELECT	= 'slt';
	const FIELD_FILE	= 'file';
	const FIELD_CUSTOM  = 'cst';

	static $_languageLoaded = false;

	private $languageConfig;
	private $bundle;

	public function init($languageConfig = []) {
        parent::init();
		$this->languageConfig = $languageConfig;
    }

	public function registerTranslations() {
		if (static::$_languageLoaded) return;

		$i18n = Yii::$app->i18n;
		$i18n->translations[$this->languageId . '*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
			'basePath' => $this->messagesPath,
			'sourceLanguage' => (isset($this->languageConfig['sourceLanguage']) ? $this->languageConfig['sourceLanguage'] : 'en-US'),
            'fileMap' => (isset($this->languageConfig['fileMap']) ? $this->languageConfig['fileMap'] : [
				$this->languageId => ($this->path . '.php')
			])
        ];
		static::$_languageLoaded = true;
    }

	public function getLevelAttributeLabel($key) {
		$labels = $this->levelAttributeLabels();

		return (isset($labels[$key]) ? $labels[$key] : Inflector::camel2words($key));
	}

	/**
     * @inheritdoc
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
	public static function instantiate($row) {
		$class = ('app\games\\' . $row['path'] . '\Game');
		return new $class;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%game}}';
    }

	/**
     * @inheritdoc
     */
	public function afterFind() {
		$this->registerTranslations();
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'path'], 'string', 'max' => 255]
        ];
    }

	public function getValidActions() {
		return [ ];
	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'path' => Yii::t('app', 'Path'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevels()
    {
        return $this->hasMany(Level::className(), ['game_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScores()
    {
        return $this->hasMany(Score::className(), ['game_id' => 'id']);
    }

	/**
	 * @return string
	 */
	public function getClassName() {
		return ('app\games\\' . $this->path . '\Game');
	}

	/**
	 * @return string
	 */
	public function getMessagesPath() {
		return ('@app/games/' . $this->path . '/messages');
	}

	/**
	 * @return string
	 */
	public function getViewsPath() {
		return ('@app/games/' . $this->path . '/views');
	}

	/**
	 * @return string
	 */
	public function getLanguageId() {
		return ('games/' . $this->path);
	}

	/**
	 * @return string
	 */
	public function getImageUrl() {
		if (isset($this->bundle)) {
			$imageBase = '/img/' . Yii::$app->params['gameImageName'];
			$imageURL = $this->bundle->baseUrl . $imageBase;
			$imageFile = $this->bundle->basePath . $imageBase;

			return (file_exists($imageFile) ? $imageURL : '');
		} else {
			return '';
		}
	}

	/**
	 * @return string
	 */
	public function getAssetsPath() {
		if (isset($this->bundle)) {
			return $this->bundle->baseUrl . '/';
		} else {
			return '';
		}
	}

	/**
	 * @return array
	 */
	public function getLevelFields() {
		return [];
	}

	/**
	 * @return array
	 */
	public function getViewData($level) {
		return [];
	}

	/**
	 * @return boolean|array
	 */
	public function validateLevelInput($inputData) {
		return true;
	}

	/**
	 * Registra os assets do jogo
	 * @param app\components\View
	 */
	public function registerAssets($view) {
		if (class_exists($this->assetBundle)) {
			$this->bundle = call_user_func_array([$this->assetBundle, 'register'], [$view]);
		}
	}

	/**
	 * @return string
	 */
	public function getAssetBundle() {
		return '';
	}

	/**
	 * @return int
	 */
	public function calculateScore($session) {
		return 0;
	}

	/**
	 * @return string
	 */
	public function getUserFriendlyActions() {
		return [];
	}

	/**
	 * Encontra as melhores pontuações do jogo
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
