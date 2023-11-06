<?php

use yii\db\Migration;

/**
 * Class m231106_080551_add_description_column_to_costproject
 */
class m231106_080551_add_description_column_to_costproject extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%costproject}}', 'description', $this->text()->after('currency'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%costproject}}', 'description');
    }

}
