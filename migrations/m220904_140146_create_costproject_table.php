<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%costproject}}`.
 */
class m220904_140146_create_costproject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%costproject}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'participants' =>  $this->text(),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%costproject}}');
    }
}
