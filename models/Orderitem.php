<?php

namespace app\models;

use Yii;
use app\components\TranslateBehavior;

/**
 * This is the model class for table "{{%orderitem}}".
 *
 * @property int $id
 * @property string $sku
 * @property string $name
 * @property string $type
 * @property string|null $description
 * @property float $amount
 * @property string $rule
 */
class Orderitem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%orderitem}}';
    }

    public function behaviors()
    {
        return [
            'trans' => [ // name it the way you want
                'class' => TranslateBehavior::class,
                // in case you named your relation differently, you can setup its relation name attribute
                'relation' => 'translations',
                // in case you named the language column differently on your translation schema
                'languageField' => 'language',
                'translationAttributes' => [
                    'name', 'description'
                ]
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sku', 'name', 'type', 'amount', 'rule'], 'required'],
            [['type', 'description'], 'string'],
            [['amount'], 'number'],
            [['sku'], 'string', 'max' => 15],
            [['name'], 'string', 'max' => 100],
            [['rule'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sku' => Yii::t('app', 'Sku'),
            'name' => Yii::t('app', 'Name'),
            'type' => Yii::t('app', 'Type'),
            'description' => Yii::t('app', 'Description'),
            'amount' => Yii::t('app', 'Amount'),
            'rule' => Yii::t('app', 'Rule'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\queries\OrderitemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\OrderitemQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(OrderitemLang::class, ['orderitemId' => 'id']);
    }
}
