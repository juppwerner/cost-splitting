<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%expense}}`.
 */
class m220911_163318_add_participants_columns_to_expense_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%expense}}', 'participants', $this->text()->after('splitting'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%expense}}', 'participants');
    }
}
