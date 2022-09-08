<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%costitem}}".
 *
 * @property int $id
 * @property int|null $expenseId
 * @property string $participant
 * @property float $amount
 *
 * @property Expense $expense
 */
class Costitem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%costitem}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['expenseId'], 'integer'],
            [['participant', 'amount'], 'required'],
            [['amount'], 'number'],
            [['participant'], 'string', 'max' => 30],
            [['expenseId'], 'exist', 'skipOnError' => true, 'targetClass' => Expense::class, 'targetAttribute' => ['expenseId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'expenseId' => Yii::t('app', 'Expense ID'),
            'participant' => Yii::t('app', 'Participant'),
            'amount' => Yii::t('app', 'Amount'),
        ];
    }

    /**
     * Gets query for [[Expense]].
     *
     * @return \yii\db\ActiveQuery|\app\models\queries\ExpenseQuery
     */
    public function getExpense()
    {
        return $this->hasOne(Expense::class, ['id' => 'expenseId']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\queries\CostitemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\CostitemQuery(get_called_class());
    }
}
