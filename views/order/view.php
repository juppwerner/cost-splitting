<?php

use app\dictionaries\OrderTypeDict;
use app\models\Order;

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Order $model */

$this->title = Yii::t('app', 'Credit Purchase #{id}', ['id'=>$model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            // 'userId',
            [
                'attribute' => 'purchaseType',
                'value' => OrderTypeDict::get($model->purchaseType),
            ],
            [
                'attribute' => 'paymentOptionCode',
                'label' => Yii::t('app', 'Product'),
                'value' => sprintf('%s (#%s)', $model->orderitem->translation->name, $model->paymentOptionCode),
            ],
            [
                'attribute' => 'amount',
                'value' => Yii::$app->formatter->asCurrency($model->amount,$model->currency),
            ],
            [
                'label' => Yii::t('app', 'Payment Info'),
                'format' => 'raw',
                'value' => DetailView::widget([
                    'model' => $model->getDetailsAsArray(),
                    'attributes' => [
                        [
                            'attribute' => 'paymentProvider',
                            'label' => Yii::t('app', 'Payment Provider'),
                        ],                        [
                            'attribute' => 'status',
                            'label' => Yii::t('app', 'Status'),
                        ],
                        [
                            'attribute' => 'email',
                            'label' => Yii::t('app', 'Email'),
                        ],
                        [
                            'attribute' => 'fullName',
                            'label' => Yii::t('app', 'Full Name'),
                        ],
                    ],
                ]),
            ],
            // [
            //     'attribute' => 'paymentInfo',
            //     'format' => 'raw',
            //     'value' => Yii::$app->formatter->asJson($model->paymentInfo, 'pre'),
            // ],
            'ordered_at:datetime',
            [
                'attribute' => 'quantityRemaining',
                'visible' => $model->purchaseType == Order::PURCHASETYPE_QUANTITY,
            ],
            [
                'attribute' => 'expiresAtTimestamp',
                'visible' => $model->purchaseType == Order::PURCHASETYPE_TIME,
                'value' => Yii::$app->formatter->asDatetime($model->expiresAtTimestamp),
            ],
            'isConsumed:checkbox',
        ],
    ]) ?>

</div>
