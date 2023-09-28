<?php

namespace app\models\forms;

use app\models\Costproject;
use app\models\User;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class AddUserForm extends Model
{
    const SCENARIO_ADD_USER = 'addUser';
    const SCENARIO_REMOVE_USER = 'removeUser';
    public $costprojectId;

    public $username;

    public $userId;
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_ADD_USER] = ['costprojectId', 'username'];
        $scenarios[self::SCENARIO_REMOVE_USER] = ['costprojectId', 'username'];
        return $scenarios;
    }

    public function rules()
    {
        return [
            [['costprojectId', 'username'], 'required'],
            ['username', 'validateUserIsOnProject', 'on'=>['addUser']],
            ['userId', 'integer', 'min'=>1],
            ['costprojectId', 'integer', 'min'=>1],
            ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@\+]+$/'],
            ['username', 'string', 'min' => 3, 'max' => 255],
            ['username', 'trim'],

        ];
    }

    public function validateUserIsOnProject($attribute, $params)
    {
        $user = User::find()->where(['username' => $this->username])->one();
        if(empty($user)) {
            $this->addError($attribute, Yii::t('app', 'The user {username} was not found.', ['username' => $this->username]));
            return;
        }
        $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM {{%user_costproject}} WHERE userId=:userId AND costprojectId=:costprojectId')
            ->bindValue(':userId', $user->id)
            ->bindValue(':costprojectId', $this->costprojectId)
            ->queryScalar();
            Yii::info('Count: '.$count, __METHOD__);
        if($count>0) {
            $this->addError($attribute, Yii::t('app', 'The user is already assigned to the cost project.'));
        }
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
        ];
    }

    public function addUser()
    {
        $user = User::find()->where(['username' => $this->username])->one();
        if(empty($user))
            return false;
        $costproject = Costproject::findOne($this->costprojectId);
        if(empty($costproject))
            return false;

        $result = Yii::$app->db->createCommand()->insert('{{%user_costproject}}', [
            'userId' => $user->id,
            'costprojectId' => $this->costprojectId,
        ])->execute();
        // Confirm addition to new user by mail
        Yii::$app->mailer->compose('project-added-html', 
            [
                'model'=>$this, 
                'costproject'=>$costproject
            ])
            ->setFrom([Yii::$app->params['contactForm.senderEmail'] => Yii::$app->params['contactForm.senderName']])
            ->setTo($user->email)
            ->setSubject(Yii::t('app', '[{appName}] You have been added to the cost project {title}', ['appName' => Yii::$app->name, 'title' => $costproject->title]))
            ->send();
        return $result===1;
    }

    public function removeUser()
    {
        $user = User::find()->where(['username' => $this->username])->one();
        if(empty($user))
            return false;

        $costproject = Costproject::findOne($this->costprojectId);
        if(empty($costproject))
            return false;

        $this->userId = $user->id;
        $result = Yii::$app->db->createCommand()->delete(
            '{{%user_costproject}}', 
            'userId = ' . $this->userId . ' AND costprojectId = ' . $this->costprojectId
            )->execute();
        // Confirm removal to user by mail
        Yii::$app->mailer->compose('project-removed-html', 
        [
            'model'=>$this, 
            'costproject'=>$costproject
        ])
            ->setFrom([Yii::$app->params['contactForm.senderEmail'] => Yii::$app->params['contactForm.senderName']])
            ->setTo($user->email)
            ->setSubject(Yii::t('app', '[{appName}] You have been removed from the cost project {title}', ['appName' => Yii::$app->name, 'title' => $costproject->title]))
            ->send();
        return $result===1;
    }
}