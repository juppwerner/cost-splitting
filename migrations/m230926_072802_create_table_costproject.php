<?php

use yii\db\Migration;

class m230926_072802_create_table_costproject extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createIndex('created_by', '{{%costproject}}', ['created_by']);

        $this->addForeignKey(
            'costproject_ibfk_1',
            '{{%costproject}}',
            ['created_by'],
            '{{%user}}',
            ['id'],
            'CASCADE',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('costproject_ibfk_1', '{{%costproject}}');
        $this->dropIndex('created_by', '{{%costproject}}');
    }
}
