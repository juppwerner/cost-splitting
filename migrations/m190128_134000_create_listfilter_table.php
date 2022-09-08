<?php

use yii\db\Migration;

/**
 * Handles the creation of table `listfilter`.
 */
class m190128_134000_create_listfilter_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%listfilter}}', [
            'id'            => $this->primaryKey(),
            'name'          => $this->string()->notNull(),
            'route'         => $this->string()->notNull(),
            'sortorder'     => $this->integer()->notNull()->defaultValue(1),
            'filterState'   =>$this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%listfilter}}');
    }
}
