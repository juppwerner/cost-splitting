<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%costproject}}`.
 */
class m250329_093908_add_replaceNames_column_to_costproject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%costproject}}', 'replaceNames', $this->text()->after('participants'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%costproject}}', 'replaceNames');
    }
}
