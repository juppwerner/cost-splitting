<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%orderitem_lang}}".
 *
 * @property int $id
 * @property int $orderitemId
 * @property string $language
 * @property string|null $name
 * @property string|null $description
 */
class OrderitemLang extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%orderitem_lang}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['orderitemId', 'language'], 'required'],
            [['orderitemId'], 'integer'],
            [['description'], 'string'],
            [['language'], 'string', 'max' => 5],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'orderitemId' => Yii::t('app', 'Orderitem ID'),
            'language' => Yii::t('app', 'Language'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\queries\OrderitemLangQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\OrderitemLangQuery(get_called_class());
    }
}
