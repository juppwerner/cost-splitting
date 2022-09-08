<?php

use app\models\Costproject;
use app\models\Expense;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\search\ExpenseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Expenses');
$this->params['breadcrumbs'][] = $this->title;

// Get an array of all participants for the filter dropdown:
$allParticipants = \app\models\Costproject::getAllParticipants();
$splittingOptions = \app\models\Expense::getSplittingOptions();
?>
<div class="expense-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Expense'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class'=>'table table-striped table-bordered table-responsive'],
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => ActionColumn::className(),
                'template' => '{view}&nbsp;{update}',
                'contentOptions' => [ 'class' => 'text-center text-nowrap' ],
                'urlCreator' => function ($action, Expense $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],

            [
                'attribute'=>'itemDate',
                'contentOptions' => [ 'class' => 'text-center' ],
                'value'=>function($data) {
                    return Yii::$app->formatter->asDate($data->itemDate, 'php:'.Yii::t('app', 'Y-m-d'));
                },
            ],
            [
                'attribute'=>'title',
                'format'=>'html',
                'value'=>function($data) {
                    return Html::a(Html::tag('strong', $data->title), ['view', 'id'=>$data->id], ['title'=>Yii::t('app', 'View expense: {title}', ['title'=>$data->title])]);
                },
            ],
            [
                'attribute'=>'amount',
                'contentOptions' => [ 'class' => 'text-right' ],
                'value' => function($data) {
                    return Yii::$app->formatter->asCurrency($data->amount, 'EUR');
                },
            ],
            [
                'attribute'=>'payedBy',
                'filter'=>$allParticipants,
            ],
            [
                'attribute'=>'splitting',
                'filter'=>$splittingOptions,
                'contentOptions'=>['style'=>'font-size:smaller'],
                'value'=>function($data) use($splittingOptions) {
                    return $splittingOptions[$data->splitting];
                },
            ],
            [
                'attribute'=>'costprojectId',
                'format'=>'html',
                'filter'=>ArrayHelper::map(Costproject::find()->all(), 'id', 'title'),
                'value'=>function($data) {
                    return Html::a($data->costproject->title, ['costproject/view', 'id'=>$data->costprojectId], ['title'=>Yii::t('app', 'View cost project: {title}', ['title'=>$data->costproject->title])]);
                },
            ],
            // 'id',
            [
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action, Expense $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
