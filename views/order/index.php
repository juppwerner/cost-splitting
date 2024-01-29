<?php

use app\models\Order;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\dictionaries\OrderTypeDict;

/** @var yii\web\View $this */
/** @var app\models\search\OrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Credits');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= '' // Html::a(Yii::t('app', 'Create Order'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => ActionColumn::class,
                'template' => '{view}',
                'contentOptions' => [ 'class' => 'text-center text-nowrap' ],
                'urlCreator' => function ($action, Order $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
            // 'id',
            // 'userId',
            [
                'attribute' => 'purchaseType',
                'filter' => OrderTypeDict::all(),
                'value' => function($data) {
                    return OrderTypeDict::get($data->purchaseType);
                },
            ],
            [
                'attribute' => 'paymentOptionCode',
                'value' => function($data) {
                    return !empty($data->orderitem) ? $data->orderitem->translation->name : '-';
                },
            ],
            [
                'attribute' => 'amount',
                'contentOptions' => [ 'class' => 'text-right mr-4'],
                'value' => function($data) {
                    return Yii::$app->formatter->asCurrency($data->amount,$data->currency);
                }
            ],
            //'currency',
            //'paymentInfo:ntext',
            'ordered_at:datetime',
            [
                'attribute' => 'quantityRemaining',
                'contentOptions' => [ 'class' => 'text-center'],
            ],
            'expiresAtTimestamp:datetime',
            [
                'attribute' => 'isConsumed',
                'format' => 'raw',
                'contentOptions' => [ 'class' => 'text-center'],
                'filter' => ['NULL or 0' => Yii::t('app', 'No'), true=>Yii::t('app', 'Yes')],
                'value' => function($data) {
                    return Yii::$app->formatter->AsCheckbox($data->isConsumed);
                },
            ],
        ],
    ]); ?>


</div>
