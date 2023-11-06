<?php

namespace app\components;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

use app\models\user;
/**
 * This is the base model class.
 */
class BaseActiveRecord extends \yii\db\ActiveRecord
{
    // {{{ Members
    /**
     * @var string
     */
    public $recordNameTemplate = '{id}';
    // }}}
    // {{{ behaviors
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }  // }}}
    // {{{ attributeLabels
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            // {{{ Timestamp/Blameable attributes
            'created_at'    => Yii::t('app', 'Created At'),
            'created_by'        => Yii::t('app', 'Created By'),
            'createUserName'    => Yii::t('app', 'Created By'),

            'updated_at'        => Yii::t('app', 'Updated At'),
            'updated_by'        => Yii::t('app', 'Updated By'),
            'updateUserName'    => Yii::t('app', 'Updated By'),
            // }}} 
        ];
    } // }}} 
    // {{{ *** Blameable Methods ***
    // {{{ getCreateUser
    public function getCreateUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
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
       return $this->hasOne(User::class, ['id' => 'updated_by']);
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
    public function getRecordName()
    {
        $name = $this->recordNameTemplate;
        foreach($this->attributes as $k=>$v) {
            if(!empty($v))
                $name = str_replace('{'.$k.'}', $v, $name);
            else
                $name = str_replace('{'.$k.'}', '', $name);
        }
        return $name;
    }
}

