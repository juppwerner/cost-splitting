<?php

namespace app\modules\exchangerate\models;

use Yii;

/**
 * This is the model class for table "{{%exchangerate}}".
 *
 * @property int $id Primary Key
 * @property string $histDate Date of exchange rate
 * @property string $currencyCode
 * @property float $exchangeRate
 */
class Exchangerate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%exchangerate}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['histDate', 'currencyCode', 'exchangeRate'], 'required'],
            [['histDate'], 'safe'],
            [['exchangeRate'], 'number'],
            [['currencyCode'], 'string', 'max' => 3],
            [['histDate', 'currencyCode'], 'unique', 'targetAttribute' => ['histDate', 'currencyCode']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'histDate' => Yii::t('app', 'History Date'),
            'currencyCode' => Yii::t('app', 'Currency Code'),
            'exchangeRate' => Yii::t('app', 'Exchange Rate'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\modules\exchangerate\models\queries\ExchangerateQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\modules\exchangerate\models\queries\ExchangerateQuery(get_called_class());
    }
}
