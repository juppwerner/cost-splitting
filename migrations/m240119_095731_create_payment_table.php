<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payment}}`.
 */
class m240119_095731_create_payment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer(),
            'purchaseType' => "ENUM('quantity','time') NOT NULL",
            'paymentOptionCode' => $this->string(20)->notNull()->defaultValue('-'),
            'amount' => $this->decimal(10,2)->notNull(),
            'currency' => $this->char(3)->notNull(),
            'paymentInfo' => $this->text(),
            'ordered_at' => $this->integer(),
            'quantityRemaining' => $this->integer(),
            'expiresAtTimestamp' => $this->integer(),
            'isConsumed' => $this->boolean()->defaultValue(0),
        ]);
        $this->addCommentOnTable('{{%order}}', 'Orders (payments) for cost projects');
        $this->addCommentOnColumn('{{%order}}', 'userId', 'User foreign key who sent the order');
        $this->addCommentOnColumn('{{%order}}', 'paymentOptionCode', 'Internal payment option reference)');
        $this->addCommentOnColumn('{{%order}}', 'currency', '3-char. ISO currency code');
        $this->addCommentOnColumn('{{%order}}', 'paymentInfo', 'PayPal payment information (JSON)');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%order}}');
    }
}
