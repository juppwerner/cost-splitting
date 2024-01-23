<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%orderitem}}`.
 */
class m240122_134657_create_orderitem_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%orderitem}}', [
            'id'            => $this->primaryKey(),
            'sku'           => $this->string(15)->notNull(),
            'name'          => $this->string(100)->notNull(),
            'type'          => "ENUM('quantity','time') NOT NULL",
            'description'   => $this->text(),
            'amount'        => $this->decimal(10,2)->notNull(),
            'rule'          => $this->string(255)->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%orderitem}}');
    }
}
