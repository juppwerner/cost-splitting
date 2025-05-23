<?php

use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\Pjax;

use app\components\Html;
use app\dictionaries\CurrencyCodesDictEwf;
use app\models\Costproject;

/** @var yii\web\View $this */
/** @var app\models\search\CostprojectSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Cost Projects');
$this->params['breadcrumbs'][] = $this->title;
Url::remember('', 'cost-project');
?>
<div class="costproject-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Html::icon('plus-square') . Yii::t('app', 'New Cost Project'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Html::icon('upload') . Yii::t('app', 'Import Cost Project'), ['import'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if(!Yii::$app->mobileSwitcher->showMobile) : ?>
        
    <?php $dataProvider->pagination->pageSize = 10; ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class'=>'table table-striped table-responsive-sm table-hover'],
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
                    return CurrencyCodesDictEwf::get($data->currency);
                },
            ],
            [
                'attribute' => 'expensesAmount',
                'contentOptions' => [ 'class' => 'text-right pr-4' ],
                'format' => 'raw',
                'value' => function($data) {
                    return Yii::$app->formatter->asCurrency($data->expensesAmount, $data->currency);
                },
            ],
            /*
            [
                'attribute'=>'created_at',
                'contentOptions' => [ 'class' => 'text-center text-nowrap' ],
                'value' => function($data) {
                    return Yii::$app->formatter->asDatetime($data->created_at, 'php:'.Yii::t('app', 'Y-m-d H:i:s'));
                },
            ],
            */
            [
                'attribute' => 'searchCreatedUsername',
                'label' => Yii::t('app', 'Created By'),
                'value' => function($data) {
                    return $data->createUserName;
                },
            ],
            // 'createUserName',
            [
                'attribute'=>'updated_at',
                'format' => 'html',
                'contentOptions' => [ 'class' => 'text-center text-nowrap' ],
                'value' => function($data) {
                    return Yii::$app->formatter->asDatetime($data->updated_at, 'php:'.Yii::t('app', 'Y-m-d H:i:s'));
                },
            ],
            //'updated_by',
            [
                'class' => ActionColumn::class,
                'template' => '{delete}',
                'urlCreator' => function ($action, Costproject $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
            // 'id',
        ],
    ]); ?>

    <?php else : ?>

    <?= ListView::widget([ // {{{ 
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'list-group'],
        'itemOptions' => function ($model, $key, $index, $widget) {
            return [
                'tag' => 'a',
                'class' => 'list-group-item list-group-item-action', 
                'href' => Url::to(['view', 'id' => $model->id])
            ];
        },
        'itemView' => function ($model, $key, $index, $widget) {
            return 
                Html::tag(
                    'div',
                    Html::tag('h5', Html::encode($model->title), ['class' => 'mb-0']) 
                    . Html::tag('div', 
                        Html::tag('span', /* Html::icon('dollar-sign') . */ Yii::$app->formatter->asCurrency($model->expensesAmount, $model->currency), ['class' => 'badge badge-info badge-pill p-2'])
                        . ' '
                        . Html::tag('span', /* Html::icon('file') . */ count($model->expenses) /*Yii::t('app', '{n,plural,=0{No expenses} =1{one expense} other{# expenses}}', ['n' => count($model->expenses)]) */, ['class' => 'badge badge-primary badge-pill p-2'])
                    ),
                    ['class' => 'd-flex w-100 justify-content-between']
                )
                . Html::tag('div', Yii::$app->formatter->asMarkdown(Html::encode($model->description)), ['class' => 'mb-0', 'style'=>'font-size: smaller']) 
                . Html::tag(
                    'small', 
                    Yii::t('app', 'Created by: <b>{createUserName}</b>', ['createUserName'=>$model->createUserName])
                    . ' | '
                    . Yii::t('app', 'Participants: <b>{participants}</b>', ['participants' => join(', ', $model->participantsList)])
                )
                ;
        },
    ]) /* }}} */ ?>

    <?php endif; ?>
    
    <?php Pjax::end(); ?>

</div>
