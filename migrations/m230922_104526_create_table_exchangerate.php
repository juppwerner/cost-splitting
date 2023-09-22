<?php

use yii\db\Migration;

class m230922_104526_create_table_exchangerate extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%exchangerate}}',
            [
                'id' => $this->primaryKey()->comment('Primary Key'),
                'histDate' => $this->date()->notNull()->comment('Date of exchange rate'),
                'currencyCode' => $this->char(3)->notNull(),
                'exchangeRate' => $this->float()->notNull(),
            ],
            $tableOptions
        );
        $this->addCommentOnTable( '{{%exchangerate}}', 'Historical Exchange Rates');
        $this->createIndex('histDate_currencyCode', '{{%exchangerate}}', ['histDate', 'currencyCode'], true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%exchangerate}}');
    }
}
