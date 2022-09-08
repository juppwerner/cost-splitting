<?php
/**
 * User: TheCodeholic
 * Date: 3/7/2020
 * Time: 9:27 AM
 */

namespace app\modules\api\resources;


use Da\User\Model\User;

/**
 * Class UserResource
 *
 * @author Zura Sekhniashvili <zurasekhniashvili@gmail.com>
 * @package app\modules\api\resources
 */
class UserResource extends User
{
    public function fields()
    {
        return [
            'id', 'username', 'auth_key'
        ];
    }
       
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return self::find()->andWhere(['username' => $username])->one();
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}
