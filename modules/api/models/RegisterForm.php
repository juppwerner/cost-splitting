<?php

namespace app\modules\api\models;

use app\models\User;
use app\modules\api\resources\UserResource;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class RegisterForm extends Model
{
    public $username;
    public $password;
    public $password_repeat;
    public $email;

    public $user = null;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password', 'password_repeat', 'email'], 'required'],
            [['username', 'email'], 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['password', 'compare', 'compareAttribute' => 'password_repeat'],
            [['username', 'email'], 'unique',
                'targetClass' => '\app\modules\api\resources\UserResource',
                'message' => 'This {attribute} has already been taken.'
            ],
        ];
    }

    public function register()
    {
        if ($this->validate()) {
            $user = new UserResource();
            $user->username = $this->username;
            $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);
            $user->email = $this->email;
            $user->confirmed_at = time();
            // $user->access_token = Yii::$app->security->generateRandomString();
            $this->user = $user;
            if ($user->save()) {
                return Yii::$app->user->login($user, 0);
            }
            return false;
        }
        return false;
    }

}
