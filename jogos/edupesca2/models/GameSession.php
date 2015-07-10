<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%game_session}}".
 *
 * @property string $id
 * @property integer $user_id
 * @property integer $game_id
 * @property integer $level_id
 *
 * @property Level $level
 * @property Game $game
 * @property User $user
 * @property GameSessionStep[] $gameSessionSteps
 */
class GameSession extends \yii\db\ActiveRecord
{
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%game_session}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['user_id', 'game_id', 'level_id'], 'integer'],
            [['id'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'game_id' => Yii::t('app', 'Game ID'),
            'level_id' => Yii::t('app', 'Level ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevel()
    {
        return $this->hasOne(Level::className(), ['id' => 'level_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(Game::className(), ['id' => 'game_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSteps()
    {
        return $this->hasMany(GameSessionStep::className(), ['session_id' => 'id']);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getStartStep()
    {
        return $this->hasOne(GameSessionStep::className(), ['session_id' => 'id'])->where(['action' => GameSessionStep::ACTION_STARTPLAY]);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getEndStep()
    {
        return $this->hasOne(GameSessionStep::className(), ['session_id' => 'id'])->where(['action' => GameSessionStep::ACTION_ENDPLAY]);
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getLastStep()
    {
        return $this->hasOne(GameSessionStep::className(), ['session_id' => 'id'])->orderBy('time DESC');
    }
	
	/**
	 * @return boolean
	 */
	public function getActive() {
		return ($this->endStep === null);
	}
	
	/**
	 * @return int
	 */
	public function getDuration() {
		return ($this->lastStep->time - $this->startStep->time);
	}
}
