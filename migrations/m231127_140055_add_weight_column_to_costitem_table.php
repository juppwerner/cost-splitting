<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%costitem}}`.
 */
class m231127_140055_add_weight_column_to_costitem_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%costitem}}', 'weight', $this->float()->defaultValue(1)->after('participant'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%costitem}}', 'weight');
    }
}
