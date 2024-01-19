<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%costproject}}`.
 */
class m240118_130214_add_payment_column_to_costproject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%costproject}}', 'payment', $this->text()->after('description'));
        $this->addCommentOnColumn('{{%costproject}}', 'payment', 'PayPal payment information');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%costproject}}', 'payment');
    }
}
