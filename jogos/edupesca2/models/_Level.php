<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%level}}".
 *
 * @property integer $game_id
 * @property integer $id
 * @property integer $order
 * @property integer $created_by
 * @property integer $created_on
 * @property resource $data
 * @property integer $filter_id
 */
class Level extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%level}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['game_id'], 'required'],
            [['game_id', 'order', 'created_by', 'created_on', 'filter_id'], 'integer'],
            [['data'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'game_id' => Yii::t('app', 'Game ID'),
            'id' => Yii::t('app', 'ID'),
            'order' => Yii::t('app', 'Order'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_on' => Yii::t('app', 'Created On'),
            'data' => Yii::t('app', 'Data'),
            'filter_id' => Yii::t('app', 'Filter ID'),
        ];
    }
}
