<?php
namespace app\models;

use Yii;

use Da\User\Model\Profile as BaseProfile;

class Profile extends BaseProfile
{

    public $timezone = 'Europe/Berlin';

    public function rules()
    {
        $rules = parent::rules();
        // $rules[] = [['department'], 'string', 'max'=>30];
        $rules[] = [['timezone'], 'default', 'value'=>'Europe/Berlin'];
        return $rules;
    }
    public function attributeLabels() 
    {
        $labels = parent::attributeLabels();
        // $labels['department'] = Yii::t('app', 'Department');
        return $labels;
    }
}
