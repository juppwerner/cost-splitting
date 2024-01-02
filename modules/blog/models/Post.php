<?php

namespace app\modules\blog\models;

use Yii;

use app\models\User;
use app\modules\blog\models\query\PostQuery;

/**
 * This is the model class for table "{{%post}}".
 *
 * @property int $id
 * @property string $title
 * @property int $status
 * @property string $intro
 * @property string $content
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
 */
class Post extends \app\components\BaseActiveRecord
{

    // {{{ Members
    const STATUS_DRAFT      = 1;
    const STATUS_PUBLISHED  = 2;
    const STATUS_ARCHIVED   = 3;
    /**
     * @var string
     */
    public $recordNameTemplate = '{title}';
    // }}}
    // {{{ tableName
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%post}}';
    } // }}}
    // {{{ behaviors
    public function behaviors()
    {
        return parent::behaviors();
    }  // }}}
    // {{{ rules
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'status', 'intro'], 'required'],
            [['status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['intro', 'content'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    } // }}} 
    // {{{ attributeLabels
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'            => Yii::t('app', 'ID'),
            'title'         => Yii::t('app', 'Title'),
            'status'        => Yii::t('app', 'Status'),
            'intro'         => Yii::t('app', 'Intro'),
            'content'       => Yii::t('app', 'Content'),
            'created_at'    => Yii::t('app', 'Created At'),
            'created_by'    => Yii::t('app', 'Created By'),
            'updated_at'    => Yii::t('app', 'Updated At'),
            'updated_by'    => Yii::t('app', 'Updated By'),
        ];
    } // }}} 
    // {{{ getStatusOptions
    public static function getStatusOptions()
    {
        return [
            self::STATUS_DRAFT => Yii::t('app','Draft'),
            self::STATUS_PUBLISHED => Yii::t('app','Published'),
            self::STATUS_ARCHIVED => Yii::t('app','Archived'),
        ];
    } // }}} 

    // {{{ find
    /**
     * {@inheritdoc}
     * @return PostQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PostQuery(get_called_class());
    } // }}} 

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
}
