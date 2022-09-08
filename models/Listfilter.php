<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

use app\models\query\ListfilterQuery;

/**
 * This is the model class for table "{{%listfilter}}".
 *
 * @property int $id
 * @property string $name
 * @property string $route
 * @property string $filterState
 * @property int $sortorder
 */
class Listfilter extends \yii\db\ActiveRecord
{
    // {{{ behaviors
    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'userId',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'userId',
                ]
            ],
        ];
    } // }}} 
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%listfilter}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'route'], 'required'],
            [['filterState'], 'string'],
            [['sortorder'], 'integer'], 
            [['name', 'route'], 'string', 'max' => 255],
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
            'route' => Yii::t('app', 'Route'),
            'filterState' => Yii::t('app', 'Filter State'),
            'sortorder' => Yii::t('app', 'Sort Order'), 
        ];
    }

    /**
     * @inheritdoc
     * @return ListfilterQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ListfilterQuery(get_called_class());
    }
}
