<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%expense}}`.
 */
class m240606_140428_add_sortParticipants_column_to_costproject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%costproject}}', 'sortParticipants', $this->boolean()->after('participants')->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%costproject}}', 'sortParticipants');
    }
}
