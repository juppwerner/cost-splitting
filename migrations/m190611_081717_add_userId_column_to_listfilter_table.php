<?php

use yii\db\Migration;

/**
 * Handles adding userId to table `{{%listfilter}}`.
 */
class m190611_081717_add_userId_column_to_listfilter_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%listfilter}}', 'userId', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%listfilter}}', 'userId');
    }
}
