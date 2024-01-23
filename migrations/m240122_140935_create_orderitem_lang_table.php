<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%orderitem_lang}}`.
 */
class m240122_140935_create_orderitem_lang_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%orderitem_lang}}', [
            'id' => $this->primaryKey(),
            'orderitemId' => $this->integer()->notNull(),
            'language' => $this->string(5)->notNull(),
            'name' => $this->string(100),
            'description' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%orderitem_lang}}');
    }
}
