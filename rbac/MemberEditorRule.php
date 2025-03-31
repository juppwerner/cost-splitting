<?php
namespace app\rbac;

use yii\rbac\Rule;

/**
 * Checks if user_costproject userId matches user passed via params
 */
class MemberEditorRule extends Rule
{
    public $name = 'isMemberEditor';

    /**
     * @param string|int $user the user ID.
     * @param \yii\rbac\Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        if(isset($params['costproject'])) {
            $users = $params['costproject']->users;
            foreach($users as $projectUser) {
                if($projectUser->id==$user)
                    return true;
            }
        }
        return false;
    }
}