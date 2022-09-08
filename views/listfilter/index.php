<?php

use yii\bootstrap4\Html;
//use yii\grid\GridView;
use yii\bootstrap4\Button;
use yii\bootstrap4\Nav;
use yii\web\View;

use \app\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ListfilterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'List Filters');
// $this->params['breadcrumbs'][] = [ 'label' => Yii::t('CATEGORY', 'MODULENAME', 'url' => [ '/URL'] ];
$this->params['breadcrumbs'][] = $this->title;
/* {{{ 
$this->context->leftMenu = [
    [
        'label' => Yii::t('app', 'Operations'),
        'items' => [
            [ 'label' => Yii::t('app', 'Clear Filters'),        'url' => '#', 'bodyOptions'=>['id'=>'clearFiltersBtn'], 'icon'=>'refresh' ],
        ],
    ],
]; }}} */ 
?>
<div class="listfilter-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php echo Nav::widget([
        'items' => [
             [ 'label' => '<span class="fas fa-redo"></span>'.' '.Yii::t('app', 'Clear Filters'), 'url' => '#', 'linkOptions'=>['id'=>'clearFiltersBtn'] ],
        ],
        'encodeLabels' => false,
        'options' => ['class' => 'nav-pills alert alert-info action-buttons'],
        ]); ?>

    <p>
        <?= Yii::t('app', 'To create a new List Filter, set filters in the Service or Certificate Request grid view, then click Create List Filter') ?>
        <?= '' // DEACTIVATED Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Yii::t('app', 'Create New List Filter'), ['create'], ['class' => 'btn btn-success']) ?>

    <?php $form = Html::beginForm(['index'], 'post', ['id'=>'clear-filters-form', 'style'=>'display:inline']);
    $form .= Html::hiddenInput('clear-state', '1');
    $form .= Html::hiddenInput('redirect-to', '');
    // $form .= Button::widget(['label' => Yii::t('app', 'Reset Filter'), 'options' => ['class'=>'btn btn-primary btn-sm']]);
    $form .= Html::endForm();
    echo $form; ?>

    </p>

    <?= GridView::widget([
        'id' => 'listfilter-grid-view',
        'as filterBehavior' => \thrieu\grid\FilterStateBehavior::className(),
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'grid-view table-responsive'],
        'tableOptions' => ['class' => 'table table-striped table-condensed'], // table-bordered 
        'rowOptions'   => function ($model, $key, $index, $grid) {
            return ['data-id' => $model->id];
        },
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn', 
                'template' => '{view} {update}',
                'contentOptions' => [ 'class' => 'text-center', 'style'=>'white-space:nowrap' ],
            ],
            [
                'class' => 'yii\grid\ActionColumn', 
                'template' => '{apply}',
                'contentOptions' => [ 'class' => 'text-center' ],
                'buttons' => [
                    'apply' => function ($url, $model, $key) {
                        list($controller, $action) = explode('/', $model->route);
                        return Html::a('<i class="far fa-play-circle"></i>', ['/listfilter/apply', 'id'=>$model->id], ['title'=>Yii::t('app','Apply List Filter: {name}', ['name'=>$model->name])]);
                    },
                ]
            ],
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [
                'attribute' => 'sortorder',
                'contentOptions' => [ 'class' => 'text-center' ],
            ],
            [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function ($model, $key, $index, $column) {
                    return '<b>'.Html::a($model->name, ['view', 'id' => $model->id], ['title'=>Yii::t('app','View List Filter: {name}', ['name'=>$model->name])]).'</b>';
                },
            ],
            'route',
            'filterState:ntext',

            [
                'class' => 'yii\grid\ActionColumn', 
                'template' => '{delete}',
                'contentOptions' => [ 'class' => 'text-center' ],
            ],
        ],
    ]); ?>

</div>

<?php $this->registerJs("
$('#clearFiltersBtn').on('click', function() { 
    $('#clear-filters-form').submit();
});
", View::POS_READY, 'my-button-handler' ); ?>
