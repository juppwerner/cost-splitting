<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\httpclient\Client as HttpClient;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

use app\models\Costproject;
use app\models\Order;
use app\models\Orderitem;

class PaypalController extends Controller
{

    public $paypalBaseUrl;

    /**
     * @inherit
     */
    public function __construct($id, $module, $config = [])
    {
        $this->id = $id;
        $this->module = $module;
        if(!isset(Yii::$app->params['paypalBaseUrl'])) {
            Yii::error('PayPal base URL not configured');
            throw new HttpException(500, 'Failed to init payment controller');
        }
        $this->paypalBaseUrl = Yii::$app->params['paypalBaseUrl'];
        parent::__construct($id, $module, $config);
    }

    public function generateAccessToken()
    {
        // Get PayPal config parameters
        // 1. client ID
        if(empty(Yii::$app->params['paypal.clientId'])) {
            Yii::error('App parameter paypal.clientId is not configured', __METHOD__);
            return;
        }
        $paypalClientId = Yii::$app->params['paypal.clientId'];
        // 2. client secret
        if(empty(Yii::$app->params['paypal.clientSecret'])) {
            Yii::error('App parameter paypal.clientSecret is not configured', __METHOD__);
            return;
        }
        $paypalClientSecret = Yii::$app->params['paypal.clientSecret'];
        $auth = base64_encode($paypalClientId . ":" . $paypalClientSecret);
        $client = new HttpClient();
        $request = $client->createRequest();
        $request->addHeaders(['Authorization' => 'Basic '.$auth]);
        $response = $request
            ->setMethod('POST')
            ->setUrl($this->paypalBaseUrl . '/v1/oauth2/token')
            ->setData(['grant_type' => 'client_credentials'])
            ->send();
        if ($response->isOk) {
            $data = $response->data;
            Yii::info($data, __METHOD__);
            return $data['access_token'];
        } else {
            Yii::error('Failed to generate access token', __METHOD__);

        }
    }
    
    public function actionOrders()
    {
        $request = Yii::$app->request;
        $cart    = $request->post()['cart'];
        Yii::info($cart, __METHOD__);
        $result = $this->createOrder($cart);
        Yii::$app->response->statusCode = $result[0];
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $result[1];
        return $response;
    }

    public function createOrder($cart)
    {
        Yii::info("shopping cart information passed from the frontend createOrder() callback:", __METHOD__);
        Yii::info($cart, __METHOD__.' $cart');
        
        // Get payment option item
        $paymentOption = Orderitem::find()->where(['sku'=>$cart[0]['paymentOptionId']])->one();
        if(is_null($paymentOption)) {
            Yii::error('Payment Option not configured: '.$cart[0]['paymentOptionId'], __METHOD__);
            throw new HttpException(500, 'Failed to capture order');
        }
        $paymentOption->loadTranslations(Yii::$app->language);

        // Get configured payment currency code
        if(empty(Yii::$app->params['paymentCurrencyCode'])) {
            Yii::error('App Parameter paymentCurrencyCode is not configured', __METHOD__);
            throw new HttpException(500, 'Failed to capture order');
        }
        $currencyCode = Yii::$app->params['paymentCurrencyCode'];

        // Get amount
        $amount = $paymentOption->amount;

        $accessToken = $this->generateAccessToken();
        Yii::info('Access Token: '.$accessToken, __METHOD__);
        $url = $this->paypalBaseUrl . '/v2/checkout/orders';

        $payload =<<<EOL
{
    "application_context": {
        "brand_name": "Diggin' Data EDV-Dienstleistungen"
    },
    "intent": "CAPTURE",
    "purchase_units": [
        {
            "custom_id": 0,
            "description": "Cost Splitting Purchase",
            "amount": {
                "currency_code": "XXX",
                "value": "0"
            }
        }
    ]
}
EOL;
        $payload = json_decode($payload);
        Yii::info(json_encode($payload), __METHOD__.' $payload 0');
        $payload->purchase_units[0]->amount->description = Yii::t('app', 'Purchase at {appName}', ['appName'=>Yii::$app->name]);
        $payload->purchase_units[0]->amount->currency_code = $currencyCode;
        $payload->purchase_units[0]->amount->value = $amount;
        $payload->purchase_units[0]->custom_id = base64_encode(json_encode([
            'costprojectId' => $cart[0]['costprojectId'],
            'paymentOptionId' => $cart[0]['paymentOptionId']
        ]));
        // Add breakdown
        $breakdown = new \StdClass;
        $breakdown->item_total = new \StdClass;
        $breakdown->item_total->currency_code = $currencyCode;
        $breakdown->item_total->value = $paymentOption->amount;
        $payload->purchase_units[0]->amount->breakdown = $breakdown;

        // Add item
        $item = new \StdClass;
        $item->name         = '['.Yii::$app->name.'] '.$paymentOption->translation->name;
        $item->quantity     = 1;
        $item->description  = $paymentOption->translation->description ?? 'Cost Project rate';
        $item->sku          = $cart[0]['paymentOptionId'];
        $item->unit_amount  = new \StdClass;
        $item->unit_amount->currency_code = $currencyCode;
        $item->unit_amount->value = $paymentOption->amount;
        Yii::info(json_encode($item), __METHOD__.' $item');
        $payload->purchase_units[0]->items = [$item];
        Yii::info(json_encode($payload), __METHOD__.' $payload 1');

        $client = new HttpClient();
        $request = $client->createRequest();
        $request->addHeaders([
            // 'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$accessToken,
        ]);
        $response = $request
            ->setMethod('POST')
            ->setFormat(HttpClient::FORMAT_JSON)
            ->setUrl($url)
            ->setData($payload)
            ->send();
        if ($response->isOk) {
            $data = $response->data;
            Yii::info($data, __METHOD__);
            return [$response->statusCode, $response->data];
        } else {
            Yii::error('Failed to create order', __METHOD__);
            Yii::error($response->data, __METHOD__);
            throw new HttpException(500, 'Failed to create order');
        }
    }

    public function actionCaptureOrder($orderId)
    {
        Yii::info('Order ID: ' . $orderId, __METHOD__);
        $result = $this->captureOrder($orderId);
        Yii::$app->response->statusCode = $result[0];
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $result[1];
        return $response;
    }

    /**
     * Capture payment for the created order to complete the transaction.
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_capture
     */
    public function captureOrder($orderId)
    {
        $accessToken = $this->generateAccessToken();
        Yii::info('Access Token: '.$accessToken, __METHOD__);
        $url = $this->paypalBaseUrl . '/v2/checkout/orders/'.$orderId.'/capture';
        $client = new HttpClient();
        $request = $client->createRequest();
        $request->addHeaders([
            'Authorization' => 'Bearer '.$accessToken,
        ]);
        $response = $request
            ->setMethod('POST')
            ->setFormat(HttpClient::FORMAT_JSON)
            ->setUrl($url)
            ->send();
        if ($response->isOk) {
            $data = $response->data;
            Yii::info($data, __METHOD__);
            $custom_id = json_decode(base64_decode($data['purchase_units'][0]['payments']['captures'][0]['custom_id']));
            Yii::info('costprojectId: '.$custom_id->costprojectId, __METHOD__);
            Yii::info('paymentOptionId: '.$custom_id->paymentOptionId, __METHOD__);

            // Check if ordered payment option id exists
            $paymentOption = Orderitem::find()->where(['sku'=>$custom_id->paymentOptionId])->one();
            if(is_null($paymentOption)) {
                Yii::error('paymentOption not found: '.$custom_id->paymentOptionId, __METHOD__);
                throw new HttpException(500, 'Failed to capture order');
            }

            // Create an order item
            $order = new Order();
            $order->userId = Yii::$app->user->id;
            $order->purchaseType = $paymentOption->type;
            $order->paymentOptionCode = $custom_id->paymentOptionId;
            $order->amount = $paymentOption->amount;
            $order->currency = 'EUR';
            $order->paymentInfo = \yii\helpers\Json::encode($data);
            // Calculate rule
            if($paymentOption->type === 'quantity') {
                $order->quantityRemaining = (int)$paymentOption['rule']-1;
                if($order->quantityRemaining == 0)
                    $order->isConsumed = true;
            } elseif($paymentOption->type === 'time') {
                $order->expiresAtTimestamp = strtotime($paymentOption->rule, time());
            }

            if(!$order->save()) {
                Yii::error('Cannot create order record'.\yii\helpers\VarDumper::dumpAsString($order->errors), __METHOD__);
                throw new HttpException(500, 'Failed to capture order');
            }
            // Update cost project
            $costproject = $this->findModel($custom_id->costprojectId);
            $costproject->orderId = $order->id; // \yii\helpers\Json::encode($data);
            if(!$costproject->save()) {
                Yii::error('Cannot update cost project'.\yii\helpers\VarDumper::dumpAsString($costproject->errors), __METHOD__);
                throw new HttpException(500, 'Failed to capture order');
            }
            return [$response->statusCode, $response->data];
        } else {
            Yii::error('Failed to create order', __METHOD__);
            throw new HttpException(500, 'Failed to create order');
        }
    }

    /**
     * Finds the Costproject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Costproject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Costproject::find()
            ->select(['costproject.*'])
            ->innerJoinWith('users')
            ->where(['user.id' => Yii::$app->user->id, 'costproject.id' => $id])
            ->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}