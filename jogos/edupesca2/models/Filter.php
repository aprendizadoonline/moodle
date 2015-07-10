<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%filter}}".
 *
 * @property integer $game_id
 * @property integer $level_id
 * @property integer $target_type
 * @property integer $target_id
 */
class Filter extends \yii\db\ActiveRecord
{
	const TARGET_COHORT = 1;
	const TARGET_STUDENT = 2;
	static $identifierLen;
	
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%filter}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['identifier'], 'string', 'length' => static::$identifierLen],
			[['game_id', 'level_id', 'target_type', 'target_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'game_id' => Yii::t('app', 'Game ID'),
            'level_id' => Yii::t('app', 'Level ID'),
            'target_type' => Yii::t('app', 'Target Type'),
            'target_id' => Yii::t('app', 'Target ID'),
        ];
    }
	
	/**
	 * Calcula o novo identificador
	 */
	public function generateIdentifier() {
		$this->identifier = hash('sha512', 
			'gid:' . $this->game_id     . ';' .
			'lid:' . $this->level_id    . ';' .
			'ttp:' . $this->target_type . ';' .
			'tid:' . $this->target_id   . ';'
		);
	}
}

Filter::$identifierLen = strlen(hash('sha512', ''));