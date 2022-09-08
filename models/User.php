<?php
namespace app\models;

use Yii;
use Da\User\Model\User as BaseUser;

class User extends BaseUser
{
    // {{{ scenarios
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        // add field to scenarios
        /*
        $scenarios['create'][]   = 'markets';
        $scenarios['update'][]   = 'markets';
        $scenarios['register'][] = 'markets';
         */
        return $scenarios;
    } // }}} 
    // {{{ attributeLabels
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        // Add more labels
        // $labels['marketsArray'] = Yii::t('app','Markets');
        return $labels;
    } // }}} 
    // {{{ rules
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();
        // DEBUG \yii\helpers\VarDumper::dump($rules, 10, true);

        // add own rules
        // $rules['markets'] = ['markets', 'string'];
        // $rules['marketsArray'] = ['marketsArray', 'safe'];
        // DEBUG \yii\helpers\VarDumper::dump($rules, 10, true);
        
        return $rules;
    } // }}} 
    // {{{ getDisplayName
    /**
     * Returns a 'speaking' name for thie record instance
     * @return string
     */
    public function getDisplayName()
    {
        $name = $this->username;
        if(!empty($this->profile->name))
            return $this->profile->name . ' ('.$name.')';
        return $name;
    } // }}} 
    // {{{ getFull_name
    public function getFull_name() {
        return $this->displayName;
    } // }}} 
}
