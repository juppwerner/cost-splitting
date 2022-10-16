<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%costproject}}`.
 */
class m220911_162322_add_useCurrency_columns_to_costproject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%costproject}}', 'useCurrency', $this->boolean()->defaultValue(0)->after('participants'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%costproject}}', 'useCurrency');
    }
}
