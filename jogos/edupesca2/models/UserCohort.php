<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_cohort}}".
 *
 * @property integer $cohort_id
 * @property integer $user_id
 *
 * @property User $user
 * @property Cohort $cohort
 */
class UserCohort extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_cohort}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cohort_id', 'user_id'], 'required'],
            [['cohort_id', 'user_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cohort_id' => Yii::t('app', 'Cohort ID'),
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
    public function getCohort()
    {
        return $this->hasOne(Cohort::className(), ['id' => 'cohort_id']);
    }
}
