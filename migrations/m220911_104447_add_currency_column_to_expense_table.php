<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%expense}}`.
 */
class m220911_104447_add_currency_column_to_expense_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%expense}}', 'currency', $this->char(3)->notNull()->defaultValue('EUR')->after('amount'));
        $this->addColumn('{{%expense}}', 'exchangeRate', $this->float()->defaultValue(1)->after('currency'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%expense}}', 'currency');
        $this->dropColumn('{{%expense}}', 'exchangeRate');
    }
}
