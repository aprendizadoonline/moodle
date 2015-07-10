<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_tutor}}".
 *
 * @property integer $tutor_id
 * @property integer $user_id
 *
 * @property User $user
 * @property User $tutor
 */
class UserTutor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_tutor}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tutor_id', 'user_id'], 'required'],
            [['tutor_id', 'user_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tutor_id' => Yii::t('app', 'Tutor ID'),
            'user_id' => Yii::t('app', 'User ID'),
        ];
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
    public function getTutor()
    {
        return $this->hasOne(User::className(), ['id' => 'tutor_id']);
    }
}
