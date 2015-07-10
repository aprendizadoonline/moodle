<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%score}}".
 *
 * @property integer $user_id
 * @property integer $game_id
 * @property integer $level_id
 * @property integer $date
 * @property integer $points
 *
 * @property User $level
 * @property Game $game
 * @property User $user
 */
class Score extends ActiveRecord {
    private $_sumPoints = null;

    public function getSumPoints() {
        return $this->_sumPoints;
    }
    
    public function setSumPoints($points) {
        $this->_sumPoints = $points;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        // Não permite salvar se a soma dos pontos estiver definida
        if ($this->_sumPoints !== null)
            return false;

        // Passa para o parente
        parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%score}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'game_id', 'level_id'], 'required'],
            [['user_id', 'game_id', 'level_id', 'date', 'points'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'game_id' => Yii::t('app', 'Game ID'),
            'level_id' => Yii::t('app', 'Level ID'),
            'points' => Yii::t('app', 'Points'),
            'date' => Yii::t('app', 'Date'),
            'gameName' => Yii::t('app', 'Game Name'),
            'levelTitle' => Yii::t('app', 'Level Title'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSession() {
        return $this->hasOne(GameSession::className(), ['id' => 'session_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLevel() {
        return $this->hasOne(Level::className(), ['id' => 'level_id']);
    }

    /**
     * @return string
     */
    public function getLevelTitle() {
        return $this->level->title;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGame() {
        return $this->hasOne(Game::className(), ['id' => 'game_id']);
    }

    /**
     * @return string
     */
    public function getGameName() {
        return $this->game->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
