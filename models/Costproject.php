<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\components\BaseActiveRecord;

/**
 * This is the model class for table "{{%costproject}}".
 *
 * @property int $id
 * @property string|null $title
 * @property string $participants
 * @property boolean $useCurrency
 * @property string $currency
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 *
 * @property Expense[] $expenses 
 */
class Costproject extends BaseActiveRecord
{
    public $recordNameTemplate = '{title} (#{id})';

    // {{{ tableName
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%costproject}}';
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
            [['title', 'participants', 'currency'], 'required'],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['useCurrency'], 'boolean'],
            [['currency'], 'string', 'min'=>3, 'max' => 255],
            [['participants'], 'trim'],
            [['participants'], 'safe'],
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
            'participants' => Yii::t('app', 'Participants'),
            'useCurrency' => Yii::t('app', 'Use Currency'),
            'currency' => Yii::t('app', 'Currency'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'createUserName' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updateUserName' => Yii::t('app', 'Created By'),
        ];
    } // }}}
    // {{{ getBreakdown
    public function getBreakdown()
    {
        $result = array();
        foreach($this->getExpenses()->orderBy(['itemDate'=>SORT_ASC, 'id'=>SORT_ASC])->all() as $expense)
            $result[$expense->id] = $expense;

        return $result;
    } // }}}
    // {{{ getParticipantsList
    public function getParticipantsList()
    {
        $participants = explode("\n", preg_replace('~\R~u', "\n", $this->participants));
        $result = [];
        foreach($participants as $participant)
            $result[$participant] = $participant;
        return $result;
    } // }}}
    // {{{ getAllParticipants
    /**
     * Returns a list of all projects' participants
     *
     * @return mixed
     */
    public static function getAllParticipants()
    {
        $result = [];
        $projects = self::find()->all();
        foreach($projects as $project)
            $result = array_merge($result, $project->participantsList);
        asort($result);
        return $result;
    } // }}} 
    // {{{ find
    /**
     * {@inheritdoc}
     * @return \app\models\queries\CostprojectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\CostprojectQuery(get_called_class());
    } // }}}
    // {{{ getExpenses
    /**
     * Gets query for [[Expenses]].
     *
     * @return \yii\db\ActiveQuery|\app\models\queries\ExpenseQuery
     */
    public function getExpenses()
    {
        return $this->hasMany(Expense::class, ['costprojectId' => 'id']);
    } // }}} 
    public function getTotalExpenses()
    {
        $total = 0;
        foreach($this->expenses as $expense) {
            if($expense->expenseType === \app\dictionaries\ExpenseTypesDict::EXPENSETYPE_TRANSFER)
                continue;
            $total += $expense->amount * $expense->exchangeRate;
        }
        return $total;
    }
    // {{{ *** Blameable Methods ***
    // {{{ getCreateUser
    public function getCreateUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    } // }}} 
    // {{{ getCreateUserName
    /**
     * @getCreateUserName
     * 
     */
    public function getCreateUserName() 
    {
        return $this->createUser ? $this->createUser->username : '- no user -';
    } // }}} 
    // {{{ getUpdateUser
    public function getUpdateUser()
    {
       return $this->hasOne(User::className(), ['id' => 'updated_by']);
    } // }}} 
    // {{{ getUpdateUserName
    /**
     * @getUpdateUserName
     * 
     */ 
    public function getUpdateUserName() 
    {
        return $this->updateUser ? $this->updateUser->username : '- no user -';
    } // }}} 
    // }}} End Blameable methods
}
