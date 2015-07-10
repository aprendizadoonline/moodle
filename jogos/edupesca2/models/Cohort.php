<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%cohort}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property UserCohort[] $userCohorts
 * @property User[] $users
 */
class Cohort extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cohort}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCohorts()
    {
        return $this->hasMany(UserCohort::className(), ['cohort_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('{{%user_cohort}}', ['cohort_id' => 'id']);
    }
}
