<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%expense}}".
 *
 * @property int $id
 * @property string $title
 * @property int $costprojectId
 * @property string $payedBy
 * @property string|null $itemDate
 * @property float|null $amount
 * @property string $splitting
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property Costproject $costproject
 */
class Expense extends \yii\db\ActiveRecord
{
    Const SPLITTING_EQUAL = 'EQUAL';
    // {{{ tableName
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%expense}}';
    } // }}} 
    // {{{ behaviors
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                //'value' => new Expression('NOW()'),
            ],
        ];
    } // }}} 
    // {{{ rules
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'costprojectId', 'splitting'], 'required'],
            [['costprojectId'], 'integer'],
            [['itemDate'], 'safe'],
            [['amount'], 'number'],
            [['title'], 'string', 'max' => 255],
            [['payedBy'], 'string', 'max' => 30],
            [['splitting'], 'safe'],
            [['costprojectId'], 'exist', 'skipOnError' => true, 'targetClass' => Costproject::class, 'targetAttribute' => ['costprojectId' => 'id']],
        ];
    } // }}}
    // {{{ attributeLabels
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'payedBy' => Yii::t('app', 'Payed By'),
            'costprojectId' => Yii::t('app', 'Cost Project'),
            'itemDate' => Yii::t('app', 'Item Date'),
            'amount' => Yii::t('app', 'Amount'),
            'splitting' => Yii::t('app', 'Splitting'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'createUserName' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updateUserName' => Yii::t('app', 'Created By'),
        ];
    } // }}} 
    // {{{ getCostitems
    /**
     * Gets query for [[Costitems]]. 
     * 
     * @return \yii\db\ActiveQuery|\app\models\queries\CostitemQuery 
     */ 
    public function getCostitems() 
    { 
        return $this->hasMany(Costitem::class, ['expenseId' => 'id']); 
    } // }}}
    // {{{ getCostproject
    /**
     * Gets query for [[Costproject]].
     *
     * @return \yii\db\ActiveQuery|\app\models\queries\CostprojectQuery
     */
    public function getCostproject()
    {
        return $this->hasOne(Costproject::class, ['id' => 'costprojectId']);
    } // }}} 
    // {{{ find
    /**
     * {@inheritdoc}
     * @return \app\models\queries\ExpenseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\ExpenseQuery(get_called_class());
    } // }}}
    // {{{ afterSave
    public function afterSave( $insert, $changedAttributes )
    {
        $this->recreateCostitems();
    } // }}} 
    // {{{ recreateCostitems
    public function recreateCostitems()
    {
        // Delete all previously attached cost items
        foreach($this->costitems as $costitem) 
            $costitem->delete();

        // Create cost item for payer with whole amount
        $costitem = new Costitem;
        $costitem->expenseId = $this->id;
        $costitem->participant = $this->payedBy;
        $costitem->amount = $this->amount;
        if(!$costitem->save())
            die(\yii\helpers\VarDumper::dumpAsString($costitem->errors, 10, true));

        $participants = explode("\n", str_replace("\r\n", "\n", $this->costproject->participants));

        switch($this->splitting)
        {
        case self::SPLITTING_EQUAL:
            foreach($participants as $participant) {
                $costitem = new Costitem;
                $costitem->expenseId = $this->id;
                $costitem->participant = $participant;
                $costitem->amount = -$this->amount/count($participants);
                $costitem->save();
            }
            break;
        }
    } // }}}
    // {{{ getSplittingOptions
    public static function getSplittingOptions()
    {
        return [
            self::SPLITTING_EQUAL => Yii::t('app', 'Divided equally'),
        ];
    }} // }}}
