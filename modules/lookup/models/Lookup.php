<?php

namespace app\modules\lookup\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

use app\modules\lookup\Module;
/**
 * This is the model class for table "lookup".
 *
 * @property integer $id
 * @property string $type
 * @property string $name
 * @property string $name_de
 * @property integer $code
 * @property string $comment
 * @property integer $active
 * @property integer $sort_order
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $updated_at
 *
 * @property User $updatedBy
 * @property User $createdBy
 */
class Lookup extends \yii\db\ActiveRecord
{
    private $_items=[];
    public $saveAsNew;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lookup}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'name', 'code', 'active', 'sort_order'], 'required'],
            [['active', 'sort_order', 'created_at', 'created_by', 'updated_by', 'updated_at'], 'integer'],
            [['comment'], 'string'],
            [['type', 'code', 'name'], 'string', 'max' => 255],
            [['type', 'name'], 'unique', 'targetAttribute' => ['type', 'name'], 'message' => 'The combination of Type and Name has already been taken.'],
            [['saveAsNew'], 'boolean'],
            [['name_de'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => Yii::t('lookup','ID'),
            'type'          => Yii::t('lookup','Type'),
            'name'          => Yii::t('lookup','Label'),
            'name_de'       => Yii::t('lookup','Label DE'),
            'code'          => Yii::t('lookup','Code'),
            'comment'       => Yii::t('lookup','Comment'),
            'active'        => Yii::t('lookup','Active'),
            'sort_order'    => Yii::t('lookup','Sort Order'),
            'created_at'    => Yii::t('lookup','Created At'),
            'created_by'    => Yii::t('lookup','Created By'),
            'updated_by'    => Yii::t('lookup','Updated By'),
            'updated_at'    => Yii::t('lookup','Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Returns the items for the specified type.
     * @param string item type (e.g. 'PostStatus').
     * @param bool active/not active
     * @param string sort order
     * @return array item names indexed by item code. The items are order by their sort_order values.
     * An empty array is returned if the item type does not exist.
     */
    public function items($type=null, $onlyActive=false, $order='sort_order,name')
    {
        if(!isset($this->_items[$type]))
            $this->loadItems($type, $onlyActive, $order);
        return $this->_items[$type];
    }

    /**
     * Returns the item name for the specified type and code.
     * @param string the item type (e.g. 'PostStatus').
     * @param integer the item code (corresponding to the 'code' column value)
     * @param string the language id. There must exist an attribute name_{language}! If null, current language is used 
     * @return string the item name for the specified the code. False is returned if the item type or code does not exist.
     */
    public function item($type,$code, $language=null)
    {
        if(!is_null($language)) {
            $where = [
                'type'=>$type,
                'code'=>$code,
            ];

            $model = $this->find()
            ->where($where)
            ->orderBy($order)
            ->one();
            if(!is_null($item)) {
                $result = $item->name;
                $attribute = 'name_'.$language;
                if(!empty($item->$attribute))
                    $result = $item->$attribute;
                return $result;
            }
            return false;
        };
        if(!isset($this->_items[$type]))
            $this->loadItems($type);
        return isset($this->_items[$type][$code]) ? $this->_items[$type][$code] : false;
    }

    /**
     * Loads the lookup items for the specified type from the database.
     * @param string the item type
     * @param boolean $onlyActive
     * @param string $order
     */
    private function loadItems($type, $onlyActive=false, $order='sort_order,name')
    {
        $this->_items[$type]=array();
        $language = \Yii::$app->language;
        $attribute = 'name_'.$language;
        $where = [
            'type'=>$type,
        ];
        if($onlyActive===true) {
            $where['active'] = 1;
        }

        $models=$this->find()
        ->where($where)
        ->orderBy($order)
        ->all();

        foreach($models as $model) {
            $this->_items[$type][$model->code]=$model->name;
            if(!empty($model->$attribute))
                $this->_items[$type][$model->code]=$model->$attribute;
            
        }
    }

}
