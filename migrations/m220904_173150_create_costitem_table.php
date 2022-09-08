<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%costitem}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%expense}}`
 */
class m220904_173150_create_costitem_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%costitem}}', [
            'id' => $this->primaryKey(),
            'expenseId' => $this->integer(),
            'participant' => $this->string(30)->notNull(),
            'amount' => $this->decimal(10,2)->notNull(),
        ]);

        // creates index for column `expneseId`
        $this->createIndex(
            '{{%idx-costitem-expenseId}}',
            '{{%costitem}}',
            'expenseId'
        );

        // add foreign key for table `{{%expense}}`
        $this->addForeignKey(
            '{{%fk-costitem-expenseId}}',
            '{{%costitem}}',
            'expenseId',
            '{{%expense}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%expense}}`
        $this->dropForeignKey(
            '{{%fk-costitem-expenseId}}',
            '{{%costitem}}'
        );

        // drops index for column `expneseId`
        $this->dropIndex(
            '{{%idx-costitem-expenseId}}',
            '{{%costitem}}'
        );

        $this->dropTable('{{%costitem}}');
    }
}
