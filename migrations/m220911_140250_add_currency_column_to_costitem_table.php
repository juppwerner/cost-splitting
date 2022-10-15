<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%costitem}}`.
 */
class m220911_140250_add_currency_column_to_costitem_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%costitem}}', 'currency', $this->char(3)->notNull()->defaultValue('EUR')->after('amount'));
        $this->addColumn('{{%costitem}}', 'exchangeRate', $this->float()->defaultValue(1)->after('currency'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%costitem}}', 'currency');
        $this->dropColumn('{{%costitem}}', 'exchangeRate');
    }
}
