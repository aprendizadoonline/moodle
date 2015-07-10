<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%game_session_step}}".
 *
 * @property integer $id
 * @property string $session_id
 * @property integer $time
 * @property string $action
 * @property resource $data
 *
 * @property GameSession $session
 */
class GameSessionStep extends \yii\db\ActiveRecord
{
	const ACTION_STARTPLAY = 'startPlay';
	const ACTION_ENDPLAY = 'endPlay';

	private $unserializedData;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%game_session_step}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['session_id'], 'required'],
            [['time'], 'integer'],
            [['data'], 'string'],
            [['session_id', 'action'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'session_id' => Yii::t('app', 'Session ID'),
            'time' => Yii::t('app', 'Time'),
            'action' => Yii::t('app', 'Action'),
            'data' => Yii::t('app', 'Data'),
			'userFriendlyAction' => Yii::t('app', 'Action'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSession() {
        return $this->hasOne(GameSession::className(), ['id' => 'session_id']);
    }

	/**
	 * @inheritdoc
	 */
	public function afterFind() {
		$this->unserializedData = unserialize($this->data);
	}

	/**
     * @return string
     */
    public function getUserFriendlyAction() {
		if ($this->action == static::ACTION_STARTPLAY) {
			return Yii::t('app', 'Started to play');
		} elseif ($this->action == static::ACTION_ENDPLAY) {
			return Yii::t('app', 'End of the game');
		} else {
			$action = $this->session->game->getUserFriendlyAction($this->action, $this->unserializedData);
			return ($action ? $action : Yii::t('app', 'Unknown Action'));
		}
    }
}
