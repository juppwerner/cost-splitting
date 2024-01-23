<?php

use app\models\OrderitemLang;
use yii\db\Migration;
use app\models\Orderitem;

/**
 * Class m240122_135232_add_orderitems
 */
class m240122_141000_add_orderitems extends Migration
{
    public $data = [
        [
            'sku'           => 'CSP_Q1',
            'name'          => 'One single cost project',
            'type'          => 'quantity',
            'description'   => 'Get a cost breakdown for one single cost project',
            'amount'        => 0.99,
            'rule'          => '1',
            'translations' => [
                [
                    'language' => 'de',
                    'name' => 'Ein Kostenprojekt',
                    'description' => 'Die Kostenverteilung für ein einziges Kostenprojekt erwerben',
                ],
            ],
        ],
        [
            'sku'           => 'CSP_Q5',
            'name'          => 'Five single cost projects',
            'type'          => 'quantity',
            'description'   => 'Get cost breakdown for five single cost projects',
            'amount'        => 4.90,
            'rule'          => '5',
            'translations' => [
                [
                    'language' => 'de',
                    'name' => 'Fünf Kostenprojekte',
                    'description' => 'Die Kostenverteilung für fünf Kostenprojekte erwerben',
                ],
            ],
        ], 
        [
            'sku'           => 'CSP_Q10',
            'name'          => 'Ten single cost projects',
            'type'          => 'quantity',
            'description'   => 'Get cost breakdown for ten single cost projects',
            'amount'        => 8.90,
            'rule'          => '10',
            'translations' => [
                [
                    'language' => 'de',
                    'name' => 'Zehn Kostenprojekte',
                    'description' => 'Die Kostenverteilung für zehn Kostenprojekte erwerben',
                ],
            ],
        ],           
        [
            'sku'           => 'CSP_T1D',
            'name'         => 'All cost projects on 1 day',
            'type'          => 'time',
            'description'   => 'Get cost breakdowns for all cost projects on one day',
            'amount'        => 9.90,
            'rule'          => '+1 day',
            'translations' => [
                [
                    'language' => 'de',
                    'name' => 'Alle Kostenprojekte an einem Tag',
                    'description' => 'Die Kostenverteilung für alle Kostenprojekte an einem Tag erwerben',
                ],
            ],
        ],        
        [
            'sku'           => 'CSP_T1W',
            'name'         => 'All cost projects during 1 week',
            'type'          => 'time',
            'description'   => 'Get cost breakdowns for all cost projects during one week',
            'amount'        => 14.90,
            'rule'          => '+1 week',
            'translations' => [
                [
                    'language' => 'de',
                    'name' => 'Alle Kostenprojekte innerhalb einer Woche',
                    'description' => 'Die Kostenverteilung für alle Kostenprojekte innerhalb einer Woche erwerben',
                ],
            ],
        ],       
        [
            'sku'           => 'CSP_T1M',
            'name'         => 'All cost projects during 1 month',
            'type'          => 'time',
            'description'   => 'Get cost breakdowns for all cost projects during one month',
            'amount'        => 29.90,
            'rule'          => '+1 month',
            'translations' => [
                [
                    'language' => 'de',
                    'name' => 'Alle Kostenprojekte innerhalb eines Monats',
                    'description' => 'Die Kostenverteilung für alle Kostenprojekte innerhalb eines Monats erwerben',
                ],
            ],
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach($this->data as $item) {
            $orderitem = new Orderitem();
            $translations = null;
            if(isset($item['translations'])) {
                $translations = $item['translations'];
                unset($item['translations']);
            }
            print_r($translations);
            $orderitem->attributes = $item;
            if(!$orderitem->save()) {
                echo 'Errors:'.PHP_EOL;
                print_r($item);
                print_r($orderitem->errors);
            } else {
                if(is_null($translations))
                    continue;
                foreach($translations as $n=>$translation) {}
                    print_r($translation);
                    $orderitem_lang = new OrderitemLang();
                    $orderitem_lang->orderitemId    = $orderitem->id;
                    $orderitem_lang->language       = $translation['language'];
                    $orderitem_lang->name           = $translation['name'];
                    $orderitem_lang->description    = $translation['description'];
                    if(!$orderitem_lang->save()) {
                        echo 'Errors:'.PHP_EOL;
                        print_r($translation);
                        print_r($orderitem_lang->errors);
                    }
                }
                unset($translation);
            }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        OrderitemLang::deleteAll();
        Orderitem::deleteAll();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240122_135232_add_orderitems cannot be reverted.\n";

        return false;
    }
    */
}
