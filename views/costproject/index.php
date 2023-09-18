<?php

use app\components\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

use app\dictionaries\CurrencyCodesDict;
use app\models\Costproject;

/** @var yii\web\View $this */
/** @var app\models\search\CostprojectSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Cost Projects');
$this->params['breadcrumbs'][] = $this->title;
\yii\helpers\Url::remember('', 'cost-project');
?>
<div class="costproject-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Html::icon('plus-square') . Yii::t('app', 'New Cost Project'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class'=>'table table-striped table-responsive'],
        'columns' => [
            [
                'class' => ActionColumn::className(),
                'template' => '{view}&nbsp;{update}',
                'contentOptions' => [ 'class' => 'text-center text-nowrap' ],
                'urlCreator' => function ($action, Costproject $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],

            [
                'attribute'=>'title',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::a(Html::tag('strong', $data->title), ['view', 'id'=>$data->id]);
                },
            ],
            [
                'attribute'=>'participants',
                'value'=>function($data) {
                    return join(', ', array_values($data->participantsList));
                }
            ],
            [
                'attribute' => 'currency',
                'value' => function($data) {
                    return CurrencyCodesDict::get($data->currency);
                },
            ],
            [
                'attribute'=>'created_at',
                'value' => function($data) {
                    return Yii::$app->formatter->asDatetime($data->created_at, 'php:'.Yii::t('app', 'Y-m-d H:i:s'));
                },
            ],
            'createUserName',
            [
                'attribute'=>'updated_at',
                'value' => function($data) {
                    return Yii::$app->formatter->asDatetime($data->updated_at, 'php:'.Yii::t('app', 'Y-m-d H:i:s'));
                },
            ],
            //'updated_by',
            [
                'class' => ActionColumn::className(),
                'template' => '{delete}',
                'urlCreator' => function ($action, Costproject $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
            // 'id',
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
