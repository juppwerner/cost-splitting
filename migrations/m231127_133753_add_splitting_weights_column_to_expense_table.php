<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%expenase}}`.
 */
class m231127_133753_add_splitting_weights_column_to_expense_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%expense}}', 'splitting_weights', $this->text()->after('splitting'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%expense}}', 'splitting_weights');
    }
}
