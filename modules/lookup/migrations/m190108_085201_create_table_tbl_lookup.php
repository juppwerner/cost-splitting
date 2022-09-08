<?php

use yii\db\Migration;

class m190108_085201_create_table_tbl_lookup extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%lookup}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'code' => $this->string()->notNull(),
            'comment' => $this->text(),
            'active' => $this->tinyInteger()->notNull()->defaultValue('1'),
            'sort_order' => $this->integer()->defaultValue('1'),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
            'name_de' => $this->string(),
        ], $tableOptions);

        $this->createIndex('idx-lookup-type', '{{%lookup}}', 'type');
    }

    public function down()
    {
        $this->dropTable('{{%lookup}}');
    }
}
