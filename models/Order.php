<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\components\BaseActiveRecord;
/**
 * This is the model class for table "{{%order}}".
 *
 * @property int $id
 * @property int|null $userId User foreign key who sent the order
 * @property string $purchaseType
 * @property string $paymentOptionCode
 * @property float $amount
 * @property string $currency
 * @property string|null $paymentInfo PayPal payment information (JSON)
 * @property int|null $ordered_at
 * @property int|null $quantityRemaining
 * @property int|null $expiresAtTimestamp
 */
class Order extends BaseActiveRecord
{
    Const PURCHASETYPE_TIME = 'time';
    Const PURCHASETYPE_QUANTITY = 'quantity';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ordered_at'],
                ],
                //'value' => new Expression('NOW()'),
            ],
        ];
    } 
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'ordered_at', 'quantityRemaining', 'expiresAtTimestamp'], 'integer'],
            [['purchaseType', 'amount', 'currency'], 'required'],
            [['purchaseType', 'paymentInfo'], 'string'],
            [['paymentOptionCode'], 'string', 'max'=>20, 'min'=>3],
            [['amount'], 'number'],
            [['currency'], 'string', 'min'=>3, 'max' => 3],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'purchaseType' => Yii::t('app', 'Purchase Type'),
            'paymentOptionCode' => Yii::t('app', 'Payment Option Code'),
            'amount' => Yii::t('app', 'Amount'),
            'currency' => Yii::t('app', 'Currency'),
            'paymentInfo' => Yii::t('app', 'Payment Info'),
            'ordered_at' => Yii::t('app', 'Ordered At'),
            'quantityRemaining' => Yii::t('app', 'Remaining Quantity'),
            'expiresAtTimestamp' => Yii::t('app', 'Expires at'),
            'isConsumed' => Yii::t('app', 'Consumed'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\queries\OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\queries\OrderQuery(get_called_class());
    }

    /**
     * Mark a cost project as paid.
     * Check all open orders
     * Use time based orders first, then quantity
     */
    public static function pay($costprojectId)
    {
        $costproject = Costproject::findOne($costprojectId);
        if($costproject->getIsPaid()) {
            Yii::info('Cost project #'.$costprojectId.' is already paid.', __METHOD__);
            return true;
        }
        $orders = self::find()
            ->andWhere(['userId' => Yii::$app->user->id])
            ->andWhere(['isConsumed' => 0])
            ->orderBy(['purchaseType' => SORT_DESC]) // time first, then quantity
            ->all();
        $pay = false;
        foreach($orders as $n=>$order) {
            switch($order->purchaseType)
            {
                case self::PURCHASETYPE_TIME:
                    // is it expired already?
                    if(time()>$order->expiresAtTimestamp) {
                        $order->isConsumed = true;
                        $order->save();
                        Yii::info('Set order to isConsumed: #'.$order->id, __METHOD__);
                        echo 'This order is set to isConsumed now'.PHP_EOL;
                        continue 2;
                    }
                    // Not yet expired
                    $costproject->orderId = $order->id;
                    $costproject->save();
                    Yii::info('Set cost project to paid: order #'.$order->id.' - '.$order->purchaseType, __METHOD__);
                    return true;
                case self::PURCHASETYPE_QUANTITY:
                    if($order->quantityRemaining==0) {
                        $order->isConsumed = true;
                        $order->save();
                        Yii::info('Set order to isConsumed: #'.$order->id, __METHOD__);
                        echo 'This order is set to isConsumed now'.PHP_EOL;
                        continue 2;
                    } else {
                        // Decrement
                        $order->quantityRemaining--;
                        if($order->quantityRemaining==0)
                            $order->isConsumed = true;                        
                        $order->save();
                        $payment = new \StdClass;
                        $payment->status = 'COMPLETED';
                        $payment->orderId = $order->id;
                        $costproject->payment = json_encode($payment);
                        $costproject->save();
                        Yii::info('Set cost project to paid: order #'.$order->id.' - '.$order->purchaseType, __METHOD__);
                        return true;
                    }
            }
        }
        return false;
    }

    public function getOrderitem()
    {
        return $this->hasOne(Orderitem::class, ['sku' => 'paymentOptionCode']);
    } 

    /**
     * Returns certain details from PayPal order JSOn as array
     * 
     * @return mixed
     */
    public function getDetailsAsArray()
    {
        $details = \yii\helpers\Json::decode($this->paymentInfo, true);
        $result = [
            'status' => $details['status'],
        ];
        $payment_source = $details['payment_source'];
        foreach($payment_source as $provider => $source) {
            $result['paymentProvider'] = ucfirst($provider);
            $result['email'] = $source['email_address'];
            $result['fullName'] = $source['name']['given_name'].' '.$source['name']['surname'];
        }
        return $result;
    }
}
