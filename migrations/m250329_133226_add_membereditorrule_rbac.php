<?php

use yii\db\Migration;

/**
 * Class m250329_133226_add_membereditorrule_rbac
 */
class m250329_133226_add_membereditorrule_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        // add the rule
        $rule = new \app\rbac\MemberEditorRule;
        $auth->add($rule);

        $author = $auth->getRole('author');
        $updateCostproject = $auth->getPermission('updateCostproject');

        // add the "updateAsMemberCostproject" permission and associate the rule with it.
        $updateAsMemberCostproject = $auth->createPermission('updateAsMemberCostproject');
        $updateAsMemberCostproject->description = 'Update costproject as member';
        $updateAsMemberCostproject->ruleName = $rule->name;
        $auth->add($updateAsMemberCostproject);

        // "updateAsMemberCostproject" will be used from "updateCostproject"
        $auth->addChild($updateAsMemberCostproject, $updateCostproject);
        $auth->addChild($author, $updateAsMemberCostproject);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $author = $auth->getRole('author');
        $auth->removeChild($author, $auth->getPermission('updateAsMemberCostproject'));
        $auth->removeChild($auth->getPermission('updateAsMemberCostproject'), $auth->getPermission('updateCostproject'));
        $auth->remove($auth->getRule('isMemberEditor'));
        $auth->remove($auth->getPermission('updateAsMemberCostproject'));
    }

}
