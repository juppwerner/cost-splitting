<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_costproject}}`.
 */
class m230918_184103_create_junction_table_for_user_and_costproject_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_costproject}}', [
            'userId' => $this->integer(),
            'costprojectId' => $this->integer(),
            'PRIMARY KEY(userId, costprojectId)',
        ]);

        // creates index for column `userId`
        $this->createIndex(
            'idx-user_costproject-userId',
            '{{%user_costproject}}',
            'userId'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-user_costproject-userId',
            '{{%user_costproject}}',
            'userId',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        // creates index for column `costprojectId`
        $this->createIndex(
            'idx-user_costproject-costprojectId',
            '{{%user_costproject}}',
            'costprojectId'
        );

        // add foreign key for table `tag`
        $this->addForeignKey(
            'fk-user_costproject-costprojectId',
            '{{%user_costproject}}',
            'costprojectId',
            '{{%costproject}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
                // drops foreign key for table `post`
        $this->dropForeignKey(
            'fk-user_costproject-userId',
            '{{%user_costproject}}'
        );

        // drops index for column `post_id`
        $this->dropIndex(
            'idx-user_costproject-userId',
            '{{%user_costproject}}'
        );

        // drops foreign key for table `tag`
        $this->dropForeignKey(
            'fk-user_costproject-costprojectId',
            '{{%user_costproject}}'
        );

        // drops index for column `tag_id`
        $this->dropIndex(
            'idx-user_costproject-costprojectId',
            '{{%user_costproject}}'
        );

        $this->dropTable('{{%user_costproject}}');
    }
}
