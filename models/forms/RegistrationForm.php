<?php
namespace app\models\forms;

use Yii;

use Da\User\Form\RegistrationForm as BaseForm;


class RegistrationForm extends BaseForm
{
    /**
     * @var string
     */
    public $passwordConfirm;

    /**
     * Override from parent
     */
    public function rules() {
        $rules = parent::rules();
        $rules['passwordConfirm'] = ['password', 'compare', 'compareAttribute' => 'passwordConfirm'];
        $rules['passwordConfirmRequired'] = ['passwordConfirm', 'required', 'message' => Yii::t('app', 'Please confirm your password')];
        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['password'] = Yii::t('app', 'Password');
        $labels['passwordConfirm'] = Yii::t('app', 'Password (confirm)');
        return $labels;
    }

}