<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%costproject}}`.
 */
class m220911_100858_add_currency_column_to_costproject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%costproject}}', 'currency', $this->char(3)->notNull()->defaultValue('EUR')->after('participants'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%costproject}}', 'currency');
    }
}
