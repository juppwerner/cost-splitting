<?php

use yii\db\Migration;

/**
 * Class m190218_080250_create_view_v_roles_and_users
 */
class m190218_080250_create_view_v_roles_and_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS v_role_users;');
        $sql = <<<EOL
CREATE VIEW v_role_users
AS
SELECT `ai`.`name` AS `roleName`,`ai`.`description` AS `roleDescription`,`u`.`id` AS `userId`,`p`.`name` AS `userFullName`,`u`.`username` AS `username`,`u`.`email` AS `userEmail`
FROM ((({{%auth_item}} `ai` 
LEFT JOIN  {{%auth_assignment}} `aa` ON((`ai`.`name` = `aa`.`item_name`))) 
LEFT JOIN  {{%user}} `u` ON((`aa`.`user_id` = `u`.`id`))) 
LEFT JOIN  {{%profile}} `p` ON((`u`.`id` = `p`.`user_id`))) 
WHERE `ai`.`type` = '1'
UNION 
SELECT `ai`.`name` AS `roleName`,`ai`.`description` AS `roleDescription`,`u`.`id` AS `userId`,`p`.`name` AS `userFullName`,`u`.`username` AS `username`,`u`.`email` AS `userEmail`
FROM {{%user}} `u`
LEFT JOIN {{%profile}} `p`          ON `u`.`id` = `p`.`user_id`
LEFT JOIN {{%auth_assignment}} `aa` ON `u`.`id` = `aa`.`user_id`
LEFT JOIN {{%auth_item}} `ai`       ON `aa`.`item_name` = `ai`.`name`
ORDER by roleName, userFullName
EOL;
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190218_080250_create_view_v_roles_and_users cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190218_080250_create_view_v_roles_and_users cannot be reverted.\n";

        return false;
    }
    */
}
