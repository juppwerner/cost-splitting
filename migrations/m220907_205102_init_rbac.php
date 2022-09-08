<?php

use yii\db\Migration;

/**
 * Class m220907_205102_init_rbac
 */
class m220907_205102_init_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        \Yii::$app->db->createCommand("UPDATE {{%auth_item}} SET description='The administrators' WHERE name='admin';")->execute();
        
        $blogAuthor = $auth->createRole('blogAuthor');
        \Yii::$app->db->createCommand("UPDATE {{%auth_item}} SET description='Manage blog posts' WHERE name='blogAuthor';")->execute();
        $auth->add($blogAuthor);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->getRole('admin');
        $auth->remove($admin);
        $blogAuthor = $auth->getRole('blogAuthor');
        $auth->remove($blogAuthor);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220907_205102_init_rbac cannot be reverted.\n";

        return false;
    }
    */
}
