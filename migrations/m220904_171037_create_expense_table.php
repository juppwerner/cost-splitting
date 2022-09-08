<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%expense}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%costproject}}`
 */
class m220904_171037_create_expense_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%expense}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'costprojectId' => $this->integer()->notNull(),
            'payedBy' => $this->string(30)->notNull(),
            'itemDate' => $this->date(),
            'amount' => $this->decimal(10,2),
            'splitting' => $this->text()->notNull(),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `costprojectId`
        $this->createIndex(
            '{{%idx-expense-costprojectId}}',
            '{{%expense}}',
            'costprojectId'
        );

        // add foreign key for table `{{%costproject}}`
        $this->addForeignKey(
            '{{%fk-expense-costprojectId}}',
            '{{%expense}}',
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
        // drops foreign key for table `{{%costproject}}`
        $this->dropForeignKey(
            '{{%fk-expense-costprojectId}}',
            '{{%expense}}'
        );

        // drops index for column `costprojectId`
        $this->dropIndex(
            '{{%idx-expense-costprojectId}}',
            '{{%expense}}'
        );

        $this->dropTable('{{%expense}}');
    }
}
