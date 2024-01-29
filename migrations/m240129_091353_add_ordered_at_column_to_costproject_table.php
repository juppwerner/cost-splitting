<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%costproject}}`.
 */
class m240129_091353_add_ordered_at_column_to_costproject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%costproject}}', 'ordered_at', $this->integer()->after('orderId'));
        $this->addCommentOnColumn('{{%costproject}}', 'ordered_at', 'Timestamp of PayPal order');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%costproject}}', 'ordered_at');
    }
}
