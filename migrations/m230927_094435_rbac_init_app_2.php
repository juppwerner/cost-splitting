<?php

use yii\db\Migration;

/**
 * Class m230927_094435_init_rbac
 */
class m230927_094435_rbac_init_app_2 extends Migration
{

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $auth = Yii::$app->authManager;
        
        // add the rule
        $rule = new \app\rbac\CreatorRule;
        $auth->add($rule);

        // add "manageCostprojects" permission
        $manageCostprojects = $auth->createPermission('manageCostprojects');
        $manageCostprojects->description = ';Manage cost projects';
        $auth->add($manageCostprojects);

        // add "viewCostproject" permission
        $viewCostproject = $auth->createPermission('viewCostproject');
        $viewCostproject->description = 'View a cost project';
        $auth->add($viewCostproject);
        
        // add "createCostproject" permission
        $createCostproject = $auth->createPermission('createCostproject');
        $createCostproject->description = 'Create a cost project';
        $auth->add($createCostproject);

        // add "deleteCostproject" permission
        $deleteCostproject = $auth->createPermission('deleteCostproject');
        $deleteCostproject->description = 'Delete a cost project';
        $auth->add($deleteCostproject);

        // add "updateCostproject" permission
        $updateCostproject = $auth->createPermission('updateCostproject');
        $updateCostproject->description = 'Update cost project';
        $auth->add($updateCostproject);

        // add the "updateOwnCostproject" permission and associate the rule with it.
        $updateOwnCostproject = $auth->createPermission('updateOwnCostproject');
        $updateOwnCostproject->description = 'Update own costproject';
        $updateOwnCostproject->ruleName = $rule->name;
        $auth->add($updateOwnCostproject);

        // "updateOwnCostproject" will be used from "updateCostproject"
        $auth->addChild($updateOwnCostproject, $updateCostproject);

        // add "author" role and give this role the "createPost" permission
        $author = $auth->createRole('author');
        $author->description = 'Manage cost projects';
        $auth->add($author);
        $auth->addChild($author, $manageCostprojects);
        $auth->addChild($author, $createCostproject);
        $auth->addChild($author, $viewCostproject);
        $auth->addChild($author, $deleteCostproject);

        // allow "author" to update their own cost projects
        $auth->addChild($author, $updateOwnCostproject);

        // add "admin" role and give this role the "updatePost" permission
        // as well as the permissions of the "author" role
        $admin = $auth->getRole('admin');
        $auth->addChild($admin, $author);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;
        $admin = $auth->getRole('admin');
        $author = $auth->getRole('author');

        $auth->removeChild($author, $auth->getPermission('manageCostprojects'));
        $auth->remove($auth->getPermission('manageCostprojects'));

        $auth->removeChild($author, $auth->getPermission('viewCostproject'));
        $auth->remove($auth->getPermission('viewCostproject'));
        
        $auth->removeChild($author, $auth->getPermission('deleteCostproject'));
        $auth->remove($auth->getPermission('deleteCostproject'));

        $auth->removeChild($author, $auth->getPermission('updateCostproject'));

        $auth->remove($auth->getPermission('updateCostproject'));
        $auth->removeChild($auth->getPermission('updateOwnCostproject'), $auth->getPermission('updateCostproject'));

        $auth->removeChild($author, $auth->getPermission('updateOwnCostproject'));
        $auth->remove($auth->getPermission('updateOwnCostproject'));

        $auth->removeChild($author, $auth->getPermission('createCostproject'));
        $auth->remove($auth->getPermission('createCostproject'));

        $auth->removeChild($admin, $author);
        $auth->remove($author);

        $auth->remove($auth->getRule('isCreator'));
    }

}
