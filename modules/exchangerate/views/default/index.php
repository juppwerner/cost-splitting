<?php

use app\modules\exchangerate\models\Exchangerate;
use app\components\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\modules\exchangerate\models\search\ExchangerateSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('exchangerate', 'Exchange Rates');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exchangerate-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Html::icon('plus-square') . Yii::t('exchangerate', 'Add Exchange Rate'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Html::icon('upload') . Yii::t('exchangerate', 'Import EZB Data'), ['import'], ['class' => 'btn btn-info']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'histDate',
            'currencyCode',
            'exchangeRate',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Exchangerate $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
